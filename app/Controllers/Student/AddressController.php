<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AddressController extends BaseController
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
        $vars['title'] = 'Alamat';
        $vars['row'] = $userModel->find($this->id);
        $vars['is_locked'] = new RegistrationModel()->isRegistrationLocked((int) $this->id);
        $vars['countries'] = countries();
        $vars['residences'] = residences();
        $vars['transportations'] = transportations();
        return view('students/address', $vars);
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
        $province_id = (string) $this->request->getVar('province_id');
        $districts = json_decode(file_get_contents(WRITEPATH . 'districts.json'));
        if (!$districts) {
            return $this->response->setJSON(['districts' => []]);
        }
        $data = [];
        foreach ($districts as $district) {
            if ((string)$district->province_id === $province_id) {
                $data[$district->district_id] = $district->district_name;
            }
        }
        ksort($data);
        return $this->response->setJSON([
            'districts' => $data
        ]);
    }

    public function subdistricts(): ResponseInterface
    {
        $district_id = $this->request->getVar('district_id');
        $districts = json_decode(file_get_contents(WRITEPATH . 'subdistricts.json'));
        $data = [];
        foreach ($districts as $district) {
            if ($district->district_id === $district_id) {
                $data[$district->subdistrict_id] = $district->subdistrict_name;
            }
        }
        ksort($data);
        return $this->response->setJSON([
            'subdistricts' => $data
        ]);
    }

    public function save(): ResponseInterface
    {
        $validation = service('validation');
        $validation->setRules([
            'address_country_id' => ['label' => 'Negara', 'rules' => 'required'],
            'address_province_id' => ['label' => 'Provinsi', 'rules' => 'required'],
            'address_district_id' => ['label' => 'Kabupaten/Kota', 'rules' => 'required'],
            'address_subdistrict_id' => ['label' => 'Kecamatan', 'rules' => 'required'],
        ]);

        if (new RegistrationModel()->isRegistrationLocked((int) $this->id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran sudah dikunci dan tidak dapat diubah.'
            ]);
        }

        if (!$validation->run((array) $this->request->getVar())) {
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

        $strFields = ['address_country_id', 'address_province_id', 'address_district_id', 'address_subdistrict_id', 'urban_village', 'village', 'street_address', 'rt', 'rw', 'post_code'];
        $intFields = ['residence_id', 'transportation_id'];

        $data = [];
        foreach ($strFields as $f) {
            $val = $this->request->getVar($f);
            if ($val !== null) {
                $data[$f] = $val;
            }
        }
        foreach ($intFields as $f) {
            $val = $this->request->getVar($f);
            if ($val !== null) {
                $data[$f] = (int)$val;
            }
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
