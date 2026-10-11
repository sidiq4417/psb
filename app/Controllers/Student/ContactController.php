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
        $vars['row'] = $userModel->findWithoutSecrets($this->id);
        $vars['is_locked'] = $is_locked;
        return view('students/contact', $vars);
    }

    public function save(): ResponseInterface
    {
        $input = array_map(fn($v) => is_string($v) ? trim($v) : $v, (array) $this->request->getVar());
        $phoneFields = ['telephone', 'mobile_phone', 'whatsapp'];
        foreach ($phoneFields as $f) {
            if (isset($input[$f]) && is_string($input[$f])) {
                $input[$f] = (string) preg_replace('/[\s().-]+/', '', $input[$f]);
            }
        }

        $phoneRule = fn(string $label, bool $required = false) => [
            'label' => $label,
            'rules' => [$required ? 'required' : 'permit_empty', 'regex_match[/^\+?[0-9]{10,15}$/]'],
            'errors' => [
                'required' => $label . ' wajib diisi.',
                'regex_match' => $label . ' harus berisi 10-15 digit angka (boleh diawali +).',
            ],
        ];

        $validation = service('validation');
        $validation->setRules([
            'telephone' => $phoneRule('Telepon Rumah'),
            'mobile_phone' => $phoneRule('Handphone'),
            'whatsapp' => $phoneRule('WhatsApp', true),
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

        $fields = ['telephone', 'mobile_phone', 'whatsapp', 'telegram', 'facebook', 'twitter', 'instagram', 'tiktok'];
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
