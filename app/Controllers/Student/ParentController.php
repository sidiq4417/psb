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
        $vars['row'] = $userModel->findWithoutSecrets($this->id);
        $vars['is_locked'] = new RegistrationModel()->isRegistrationLocked((int) $this->id);
        $vars['educations'] = educations();
        $vars['occupations'] = occupations();
        $vars['incomes'] = incomes();
        return view('students/parents', $vars);
    }

    public function save(): ResponseInterface
    {
        $input = array_map(fn($v) => is_string($v) ? trim($v) : $v, (array) $this->request->getVar());

        $nikRule = fn(string $label) => [
            'label' => $label,
            'rules' => 'permit_empty|regex_match[/^[0-9]{16}$/]',
            'errors' => ['regex_match' => $label . ' harus terdiri dari 16 digit angka.'],
        ];
        $dateRule = fn(string $label) => [
            'label' => $label,
            'rules' => 'permit_empty|valid_date[Y-m-d]',
            'errors' => ['valid_date' => $label . ' tidak valid.'],
        ];
        $listRule = fn(string $label, array $items) => [
            'label' => $label,
            'rules' => 'permit_empty|in_list[' . implode(',', array_keys($items)) . ']',
            'errors' => ['in_list' => $label . ' tidak valid.'],
        ];

        $validation = service('validation');
        $validation->setRules([
            'father_nik' => $nikRule('NIK Ayah'),
            'father_name' => UserModel::nameRule('Nama Ayah Kandung'),
            'father_birth_date' => $dateRule('Tanggal Lahir Ayah'),
            'father_education_id' => $listRule('Pendidikan Ayah', educations()),
            'father_occupation_id' => $listRule('Pekerjaan Ayah', occupations()),
            'father_income_id' => $listRule('Penghasilan Ayah', incomes()),
            'mother_nik' => $nikRule('NIK Ibu'),
            'mother_name' => UserModel::nameRule('Nama Ibu Kandung'),
            'mother_birth_date' => $dateRule('Tanggal Lahir Ibu'),
            'mother_education_id' => $listRule('Pendidikan Ibu', educations()),
            'mother_occupation_id' => $listRule('Pekerjaan Ibu', occupations()),
            'mother_income_id' => $listRule('Penghasilan Ibu', incomes()),
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

        $strFields = ['father_nik', 'father_name', 'father_birth_date', 'mother_nik', 'mother_name', 'mother_birth_date'];
        $intFields = ['father_education_id', 'father_occupation_id', 'father_income_id', 'mother_education_id', 'mother_occupation_id', 'mother_income_id'];

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
