<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class GuardianController extends BaseController
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
        $vars['title'] = 'Wali';
        $vars['row'] = $userModel->findWithoutSecrets($this->id);
        $vars['is_locked'] = new RegistrationModel()->isRegistrationLocked((int) $this->id);
        $vars['educations'] = educations();
        $vars['occupations'] = occupations();
        $vars['incomes'] = incomes();
        return view('students/guardian', $vars);
    }

    public function save(): ResponseInterface
    {
        if (new RegistrationModel()->isRegistrationLocked((int) $this->id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran sudah dikunci dan tidak dapat diubah.'
            ]);
        }

        $input = array_map(fn($v) => is_string($v) ? trim($v) : $v, (array) $this->request->getVar());

        $listRule = fn(string $label, array $items) => [
            'label' => $label,
            'rules' => 'permit_empty|in_list[' . implode(',', array_keys($items)) . ']',
            'errors' => ['in_list' => $label . ' tidak valid.'],
        ];

        $validation = service('validation');
        $validation->setRules([
            'guardian_nik' => [
                'label' => 'NIK Wali',
                'rules' => 'permit_empty|regex_match[/^[0-9]{16}$/]',
                'errors' => ['regex_match' => 'NIK Wali harus terdiri dari 16 digit angka.'],
            ],
            'guardian_name' => UserModel::nameRule('Nama Wali', false),
            'guardian_birth_date' => [
                'label' => 'Tanggal Lahir Wali',
                'rules' => 'permit_empty|valid_date[Y-m-d]',
                'errors' => ['valid_date' => 'Tanggal Lahir Wali tidak valid.'],
            ],
            'guardian_education_id' => $listRule('Pendidikan Wali', educations()),
            'guardian_occupation_id' => $listRule('Pekerjaan Wali', occupations()),
            'guardian_income_id' => $listRule('Penghasilan Wali', incomes()),
        ]);

        if (!$validation->run($input)) {
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

        $strFields = ['guardian_nik', 'guardian_name', 'guardian_birth_date'];
        $intFields = ['guardian_education_id', 'guardian_occupation_id', 'guardian_income_id'];

        $data = [];
        foreach ($strFields as $f) {
            if (array_key_exists($f, $input) && $input[$f] !== null) {
                $val = (string)$input[$f];
                $data[$f] = $val !== '' ? $val : null;
            }
        }
        foreach ($intFields as $f) {
            if (array_key_exists($f, $input) && $input[$f] !== null) {
                $data[$f] = (string)$input[$f] !== '' ? (int)$input[$f] : null;
            }
        }

        $userModel->updateUser($this->id, $data);

        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Updated successfully.',
            'row' => $userModel->findWithoutSecrets($this->id),
        ]);
    }
}
