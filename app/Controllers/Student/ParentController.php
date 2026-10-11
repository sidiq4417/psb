<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ParentController extends BaseController
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
        $vars['title'] = 'Orang Tua';
        $vars['row'] = $userModel->find($this->id);
        $vars['is_locked'] = new RegistrationModel()->isRegistrationLocked((int) $this->id);
        $vars['educations'] = educations();
        $vars['occupations'] = occupations();
        $vars['incomes'] = incomes();
        return view('students/parents', $vars);
    }

    public function save(): ResponseInterface
    {
        $validation = service('validation');
        $validation->setRules([
            'father_name' => ['label' => 'Nama Ayah Kandung', 'rules' => 'required'],
            'mother_name' => ['label' => 'Nama Ibu Kandung', 'rules' => 'required'],
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

        $strFields = ['father_nik', 'father_name', 'father_birth_date', 'mother_nik', 'mother_name', 'mother_birth_date'];
        $intFields = ['father_education_id', 'father_occupation_id', 'father_income_id', 'mother_education_id', 'mother_occupation_id', 'mother_income_id'];

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
