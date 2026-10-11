<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class SubdistrictController
 *
 * Handles the management of subdistrict (Kecamatan) data.
 *
 * @package App\Controllers\Administrator
 */
class SubdistrictController extends BaseController
{
    /**
     * Render the subdistrict page.
     *
     * @param string $district_id The district ID to filter subdistricts.
     * @return string Rendered HTML view.
     */
    public function index(string $district_id = ''): string
    {
        $vars = [
            'title' => 'Kecamatan',
            'district_id' => $district_id,
        ];

        return view('administrator/subdistricts', $vars);
    }

    /**
     * Return a list of subdistricts in JSON format filtered by district ID.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $district_id = (string)($this->request->getPost('district_id') ?? '');

        $filePath = WRITEPATH . 'subdistricts.json';

        // Defensive: check if file exists and is readable
        if (!is_file($filePath) || !is_readable($filePath)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Subdistricts file not found or unreadable.',
            ]);
        }

        $subdistricts = json_decode(file_get_contents($filePath));

        // Defensive: ensure JSON decoding succeeded
        if (!is_array($subdistricts) && !is_object($subdistricts)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Invalid subdistricts data format.',
            ]);
        }

        $records = [];
        $id = 1;

        foreach ($subdistricts as $subdistrict) {
            if ((string)$subdistrict->district_id == $district_id) {
                $records[] = [
                    'id' => $id,
                    'subdistrict_id' => $subdistrict->subdistrict_id ?? null,
                    'subdistrict_name' => $subdistrict->subdistrict_name ?? null,
                    'district_id' => $subdistrict->district_id ?? null,
                ];
                $id++;
            }
        }

        return $this->response->setJSON([
            'records' => $records
        ]);
    }
}
