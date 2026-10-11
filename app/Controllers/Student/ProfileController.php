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
        $vars['row'] = $userModel->find($this->id);
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
        $validation = service('validation');
        $validation->setRules([
            'full_name' => ['label' => 'Nama Lengkap', 'rules' => 'required'],
            'nik' => ['label' => 'NIK', 'rules' => 'required'],
            'gender' => ['label' => 'Jenis Kelamin', 'rules' => 'required'],
        ]);

        if (new RegistrationModel()->isRegistrationLocked((int) $this->id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran sudah dikunci dan tidak dapat diubah.'
            ]);
        }

        if (!$validation->run((array) $this->request->getPost())) {
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

        $strFields = ['full_name', 'nik', 'npwp', 'gender', 'citizenship', 'mother_name', 'kps'];
        $data = [];
        foreach ($strFields as $f) {
            $val = $this->request->getPost($f);
            if ($val !== null) {
                $data[$f] = $val;
            }
        }

        $data['birth_place_country_id'] = null;
        $data['birth_place_province_id'] = null;
        $data['birth_place_district_id'] = null;
        if ($this->request->getPost('birth_place_country_id') === 'ID') {
            $data['birth_place_country_id'] = $this->request->getPost('birth_place_country_id');
            $data['birth_place_province_id'] = $this->request->getPost('birth_place_province_id');
            $data['birth_place_district_id'] = $this->request->getPost('birth_place_district_id');
        }

        $birthDate = $this->request->getPost('birth_date');
        if ($birthDate !== null) {
            $data['birth_date'] = $birthDate;
        }

        $religionId = $this->request->getPost('religion_id');
        if ($religionId !== null) {
            $data['religion_id'] = (int)$religionId;
        }

        $kpsReceiver = $this->request->getPost('kps_receiver');
        if ($kpsReceiver !== null) {
            $data['kps_receiver'] = (bool)$kpsReceiver;
        }

        $userModel->updateUser($this->id, $data);

        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Updated successfully.',
            'row' => $userModel->find($this->id),
        ]);
    }
}
