<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ContactController extends BaseController
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
        $is_locked = new RegistrationModel()->isRegistrationLocked((int) $this->id);

        $vars['title'] = 'Kontak';
        $vars['row'] = $userModel->find($this->id);
        $vars['is_locked'] = $is_locked;
        return view('students/contact', $vars);
    }

    public function save(): ResponseInterface
    {
        $validation = service('validation');
        $validation->setRules([
            'whatsapp' => ['label' => 'WhatsApp', 'rules' => 'required'],
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

        $fields = ['telephone', 'mobile_phone', 'whatsapp', 'telegram', 'facebook', 'twitter', 'instagram', 'tiktok'];
        $data = [];
        foreach ($fields as $f) {
            $val = $this->request->getVar($f);
            if ($val !== null) {
                $data[$f] = $val;
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
