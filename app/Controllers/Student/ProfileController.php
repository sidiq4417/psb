<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends BaseController
{
    protected int $id = 0;

    use DataTrait;

    public function __construct()
    {
        $this->id = session()->get('actor') ?? 0;
    }

    public function index(): string
    {
        $userModel = new UserModel();
        $vars['title'] = 'Data Pribadi';
        $vars['row'] = $userModel->findWithoutSecrets($this->id);
        $vars['is_locked'] = new RegistrationModel()->isRegistrationLocked((int) $this->id);
        $vars['countries'] = countries();
        $vars['religions'] = religions();
        return view('students/profile', $vars);
    }

    public function provinces(): ResponseInterface
    {
        $provinces = json_decode(file_get_contents(WRITEPATH . 'provinces.json'));
        $data = [];
        foreach ($provinces as $province) {
            $data[$province->province_id] = $province->province_name;
        }
        ksort($data);
        return $this->response->setJSON([
            'provinces' => $data
        ]);
    }

    public function districts(): ResponseInterface
    {
        $province_id = $this->request->getPost('province_id');
        $districts = json_decode(file_get_contents(WRITEPATH . 'districts.json'));
        $data = [];
        foreach ($districts as $district) {
            if ($district->province_id == $province_id) {
                $data[$district->district_id] = $district->district_name;
            }
        }
        ksort($data);
        return $this->response->setJSON([
            'districts' => $data
        ]);
    }

    public function save(): ResponseInterface
    {
        if (new RegistrationModel()->isRegistrationLocked((int) $this->id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran sudah dikunci dan tidak dapat diubah.'
            ]);
        }

        $post = array_map(fn($v) => is_string($v) ? trim($v) : $v, (array) $this->request->getPost());
        $citizenship = (string)($post['citizenship'] ?? '');
        $is_foreigner = $citizenship !== '' && $citizenship !== 'ID';

        $validation = service('validation');
        $validation->setRules([
            'full_name' => UserModel::nameRule('Nama Lengkap'),
            'nik' => [
                'label' => $is_foreigner ? 'No. Paspor' : 'NIK',
                'rules' => ['required', $is_foreigner ? 'regex_match[/^[A-Za-z0-9]{5,20}$/]' : 'regex_match[/^[0-9]{16}$/]'],
                'errors' => [
                    'required' => ($is_foreigner ? 'No. Paspor' : 'NIK') . ' wajib diisi.',
                    'regex_match' => $is_foreigner ? 'No. Paspor harus 5-20 karakter huruf/angka.' : 'NIK harus terdiri dari 16 digit angka.',
                ],
            ],
            'gender' => ['label' => 'Jenis Kelamin', 'rules' => 'required|in_list[M,F]'],
            'citizenship' => [
                'label' => 'Kewarganegaraan',
                'rules' => 'permit_empty|in_list[' . implode(',', array_keys(countries())) . ']',
                'errors' => ['in_list' => 'Kewarganegaraan tidak valid.'],
            ],
            'birth_place' => [
                'label' => 'Tempat Lahir',
                'rules' => ['permit_empty', 'max_length[100]', 'regex_match[/^[\p{L}\p{M}\s.,\'`()-]+$/u]'],
                'errors' => [
                    'max_length' => 'Tempat Lahir maksimal 100 karakter.',
                    'regex_match' => 'Tempat Lahir hanya boleh berisi huruf, spasi, dan tanda baca sederhana.',
                ],
            ],
            'birth_date' => [
                'label' => 'Tanggal Lahir',
                'rules' => 'permit_empty|valid_date[Y-m-d]',
                'errors' => ['valid_date' => 'Tanggal Lahir tidak valid.'],
            ],
            'religion_id' => [
                'label' => 'Agama',
                'rules' => 'permit_empty|in_list[' . implode(',', array_keys(religions())) . ']',
                'errors' => ['in_list' => 'Agama tidak valid.'],
            ],
            'mother_name' => UserModel::nameRule('Nama Ibu Kandung', false),
            'kps_receiver' => ['label' => 'Penerima KPS', 'rules' => 'permit_empty|in_list[0,1]'],
            'kps' => ['label' => 'No. KPS', 'rules' => 'permit_empty|max_length[50]'],
            'npwp' => ['label' => 'NPWP', 'rules' => 'permit_empty|max_length[30]'],
        ]);

        if (!$validation->run($post)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->find($this->id);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['user' => 'User not found.']
            ]);
        }

        $strFields = ['full_name', 'nik', 'npwp', 'gender', 'citizenship', 'mother_name', 'kps', 'birth_date'];
        $data = [];
        foreach ($strFields as $f) {
            if (array_key_exists($f, $post)) {
                $val = (string)$post[$f];
                $data[$f] = $val !== '' ? $val : null;
            }
        }

        $data = array_merge($data, $this->resolveBirthPlace($post));

        if (array_key_exists('religion_id', $post)) {
            $data['religion_id'] = (string)$post['religion_id'] !== '' ? (int)$post['religion_id'] : null;
        }

        if (array_key_exists('kps_receiver', $post)) {
            $data['kps_receiver'] = (string)$post['kps_receiver'] !== '' ? (int)$post['kps_receiver'] : null;
        }

        $userModel->updateUser($this->id, $data);

        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Updated successfully.',
            'row' => $userModel->findWithoutSecrets($this->id),
        ]);
    }

    /**
     * Resolve the birth place fields sent by the profile form.
     *
     * Indonesian birth places are picked from the province/district lists and
     * stored with a readable district name in birth_place (e.g. "Bekasi");
     * other countries (or "Lainnya") use the free-text place, falling back to
     * the country name.
     *
     * @param array $post The trimmed POST data.
     * @return array<string, string|null> Columns for the users table.
     */
    private function resolveBirthPlace(array $post): array
    {
        $country_id = (string)($post['birth_place_country_id'] ?? '');
        $free_text = (string)($post['birth_place'] ?? '');

        if ($country_id === 'ID') {
            $province_id = (string)($post['birth_place_province_id'] ?? '');
            $district_id = (string)($post['birth_place_district_id'] ?? '');
            $district_name = $this->findDistrictName($province_id, $district_id);
            return [
                'birth_place_country_id' => 'ID',
                'birth_place_province_id' => $province_id !== '' && $province_id !== '99' ? $province_id : null,
                'birth_place_district_id' => $district_name !== null ? $district_id : null,
                'birth_place' => $district_name ?? ($free_text !== '' ? $free_text : null),
            ];
        }

        $countries = countries();
        $country_id = $country_id !== '' && isset($countries[$country_id]) ? $country_id : null;
        return [
            'birth_place_country_id' => $country_id,
            'birth_place_province_id' => null,
            'birth_place_district_id' => null,
            'birth_place' => $free_text !== '' ? $free_text : ($country_id !== null ? $countries[$country_id] : null),
        ];
    }

    /**
     * Find a readable district (kabupaten/kota) name in the location data.
     *
     * @param string $province_id The selected province ID.
     * @param string $district_id The selected district ID.
     * @return string|null The name without the "Kab."/"Kota" prefix, or null if not found.
     */
    private function findDistrictName(string $province_id, string $district_id): ?string
    {
        if ($province_id === '' || $district_id === '' || $district_id === '99') {
            return null;
        }
        $districts = json_decode((string) file_get_contents(WRITEPATH . 'districts.json'));
        foreach ((array) $districts as $district) {
            if ((string)$district->district_id === $district_id && (string)$district->province_id === $province_id) {
                $name = trim((string) preg_replace('/^(kabupaten|kab\.|kota\.?)\s*/i', '', (string)$district->district_name));
                return $name !== '' ? $name : null;
            }
        }
        return null;
    }
}
