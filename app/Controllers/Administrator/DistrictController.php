<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class DistrictController
 *
 * Handles the management of district (Kabupaten/Kota) data.
 *
 * @package App\Controllers\Administrator
 */
class DistrictController extends BaseController
{
    /**
     * Display the district management page.
     *
     * @param string $province_id Province identifier used to filter districts.
     * @return string Rendered HTML view.
     */
    public function index(string $province_id = ''): string
    {
        $vars = [
            'title' => 'Kabupaten / Kota',
            'province_id' => $province_id
        ];

        return view('administrator/districts', $vars);
    }

    /**
     * List districts for a given province.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $province_id = $this->request->getPost('province_id') ?? '';

        $districts = json_decode(file_get_contents(WRITEPATH . 'districts.json'));
        $records = [];
        $id = 1;

        foreach ($districts as $district) {
            if ((string)$district->province_id == (string)$province_id) {
                $records[] = [
                    'id' => $id,
                    'district_id' => $district->district_id,
                    'district_name' => $district->district_name,
                    'province_id' => $district->province_id,
                ];
                $id++;
            }
        }

        $res['records'] = $records;
        return $this->response->setJSON($res);
    }
}
