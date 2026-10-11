<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class GeneralRequirementController
 *
 * Handles the management of general admission requirements.
 *
 * @package App\Controllers\Administrator
 */
class GeneralRequirementController extends BaseController
{
    /**
     * Render the general requirements page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $vars['title'] = 'Persyaratan Umum';
        $vars['breadcrumb'] = 'Persyaratan Umum';
        return view('administrator/general-requirements', $vars);
    }

    /**
     * Return a list of academic years in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $db = db_connect();
        $records = $db->table('academic_years')
            ->select('id, name, start_date, end_date')
            ->where('is_deleted', false)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();

        return $this->response->setJSON([
            'records' => $records
        ]);
    }
}
