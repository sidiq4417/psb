<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class EntranceController extends BaseController
{
    protected int $actor = 0;

    use DataTrait;

    public function __construct()
    {
        $this->actor = session()->get('actor') ?? 0;
    }

    public function lists(): ResponseInterface
    {
        return $this->response->setJSON([
            'records' => []
        ]);
    }
}
