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
        $vars['row'] = $userModel->findWithoutSecrets($this->id);
        $vars['is_locked'] = new RegistrationModel()->isRegistrationLocked((int) $this->id);
        return view('students/school', $vars);
    }

    public function save(): ResponseInterface
    {
        $input = array_map(fn($v) => is_string($v) ? trim($v) : $v, (array) $this->request->getVar());

        $validation = service('validation');
        $validation->setRules([
            'school_name' => ['label' => 'Nama Asal Sekolah', 'rules' => 'permit_empty|max_length[255]'],
            'nisn' => [
                'label' => 'Nomor Induk Siswa Nasional',
                'rules' => 'permit_empty|regex_match[/^[0-9]{10}$/]',
                'errors' => ['regex_match' => 'NISN harus terdiri dari 10 digit angka.'],
            ],
            'school_graduation_year' => [
                'label' => 'Tahun Lulus',
                'rules' => 'permit_empty|regex_match[/^[0-9]{4}$/]',
                'errors' => ['regex_match' => 'Tahun Lulus harus berupa 4 digit tahun.'],
            ],
        ]);

        if (new RegistrationModel()->isRegistrationLocked((int) $this->id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran sudah dikunci dan tidak dapat diubah.'
            ]);
        }

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

        $fields = ['nisn', 'npsn', 'school_address', 'school_name', 'school_major', 'school_graduation_year'];
        $data = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $input) && $input[$f] !== null) {
                $val = (string)$input[$f];
                $data[$f] = $val !== '' ? $val : null;
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
