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
        $vars['row'] = $userModel->find($this->id);
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
