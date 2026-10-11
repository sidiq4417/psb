<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class ProvinceController
 *
 * Handles the management of province data.
 *
 * @package App\Controllers\Administrator
 */
class ProvinceController extends BaseController
{
    /**
     * Render the province page.
     *
     * @return string Rendered HTML view.
     */
    public function index(string $province_id = ''): string
    {
        return view('administrator/provinces', [
            'title' => 'Provinsi',
            'province_id' => $province_id,
        ]);
    }

    /**
     * Return a list of provinces in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $filePath = WRITEPATH . 'provinces.json';

        // Defensive: check if file exists and is readable
        if (!is_file($filePath) || !is_readable($filePath)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Provinces file not found or unreadable.',
            ]);
        }

        $provinces = json_decode(file_get_contents($filePath));

        // Defensive: ensure JSON decoding succeeded
        if (!is_array($provinces) && !is_object($provinces)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Invalid provinces data format.',
            ]);
        }

        $records = [];
        $id = 1;

        foreach ($provinces as $province) {
            $records[] = [
                'id' => $id,
                'province_id' => $province->province_id ?? null,
                'province_name' => $province->province_name ?? null,
                'country_id' => $province->country_id ?? null,
            ];
            $id++;
        }

        return $this->response->setJSON([
            'records' => $records
        ]);
    }
}
