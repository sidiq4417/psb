<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SchoolController extends BaseController
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
        $vars['title'] = 'Asal Sekolah';
        $vars['row'] = $userModel->find($this->id);
        $vars['is_locked'] = new RegistrationModel()->isRegistrationLocked((int) $this->id);
        return view('students/school', $vars);
    }

    public function save(): ResponseInterface
    {
        $validation = service('validation');
        $validation->setRules([
            'school_name' => ['label' => 'Nama Asal Sekolah', 'rules' => 'permit_empty'],
            'nisn' => ['label' => 'Nomor Induk Siswa Nasional', 'rules' => 'permit_empty'],
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

        $fields = ['nisn', 'npsn', 'school_address', 'school_name', 'school_major'];
        $data = [];
        foreach ($fields as $f) {
            $val = $this->request->getVar($f);
            if ($val !== null) {
                $data[$f] = $val;
            }
        }

        $val = $this->request->getVar('school_graduation_year');
        if ($val !== null) {
            $data['school_graduation_year'] = (int)$val;
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
