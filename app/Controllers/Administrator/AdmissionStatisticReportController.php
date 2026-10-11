<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AdmissionPeriodModel;
use App\Models\AdmissionStatisticModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class AdmissionStatisticReportController
 *
 * Handles the generation of registration analytical charts and statistics reports.
 *
 * @package App\Controllers\Administrator
 */
class AdmissionStatisticReportController extends BaseController
{
    /**
     * Statistics model instance.
     *
     * @var AdmissionStatisticModel
     */
    protected AdmissionStatisticModel $model;

    /**
     * AdmissionStatisticReportController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = new AdmissionStatisticModel();
        helper('datasource');
    }

    /**
     * Display the statistics report page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $admissionPeriodModel = new AdmissionPeriodModel();
        $admissionYear = $admissionPeriodModel->getActiveAdmissionYear();
        $admission_years = $admissionPeriodModel->getAllAdmissionYears();
        if (empty($admission_years)) {
            $admission_years = [(int) date('Y')];
        }

        return view('administrator/admission-statistic-report', [
            'title' => 'Statistik Pendaftaran',
            'admission_year' => $admissionYear,
            'admission_years' => $admission_years
        ]);
    }

    /**
     * Fetch JSON data for charts based on the requested year.
     *
     * @return ResponseInterface JSON response containing statistics.
     */
    public function data(): ResponseInterface
    {
        $year = (int) ($this->request->getVar('admission_year') ?? date('Y'));

        $data = [
            'campus' => $this->model->getStatsByCampus($year),
            'status' => $this->model->getStatsByStatus($year),
            'major' => $this->model->getStatsByMajor($year),
            'class' => $this->model->getStatsByClass($year),
            'path' => $this->model->getStatsByPath($year),
            'type' => $this->model->getStatsByType($year),
            'school' => $this->model->getStatsBySchool($year),
            'province' => $this->getProvinceStats($year),
            'month' => $this->getMonthlyStats($year),
            'gender' => $this->getGenderStats($year),
            'income' => $this->getIncomeStats($year),
        ];

        // Map labels for status (codes like '0','1','2' -> readable names)
        foreach ($data['status'] as $row) {
            $row->label = registration_status((string) $row->label);
        }

        return $this->response->setJSON($data);
    }

    /**
     * Map province IDs to labels and counts.
     *
     * @param int $year The admission year.
     * @return array Province statistics.
     */
    private function getProvinceStats(int $year): array
    {
        $provinceStats = $this->model->getStatsByProvince($year);

        $provincesFile = WRITEPATH . 'provinces.json';

        if (!file_exists($provincesFile)) {
            return [];
        }

        $provinces = json_decode(file_get_contents($provincesFile), true);

        $provinceNames = [];
        foreach ($provinces as $p) {
            $provinceNames[$p['province_id']] = $p['province_name'];
        }

        $result = [];
        foreach ($provinceStats as $row) {
            $provId = (string) $row->province_id;
            if ($provId !== '0' && isset($provinceNames[$provId])) {
                $result[] = (object) [
                    'label' => $provinceNames[$provId],
                    'total' => (int) $row->total
                ];
            }
        }

        // Sort by total desc
        usort($result, fn($a, $b) => $b->total <=> $a->total);

        return $result;
    }

    /**
     * Get monthly statistics with month names.
     *
     * @param int $year The admission year.
     * @return array Monthly statistics.
     */
    private function getMonthlyStats(int $year): array
    {
        $stats = $this->model->getStatsByMonth($year);
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $result = [];
        foreach ($stats as $row) {
            $result[] = (object) [
                'label' => $months[(int) $row->month_index],
                'total' => (int) $row->total
            ];
        }
        return $result;
    }

    /**
     * Get gender statistics with labels.
     *
     * @param int $year The admission year.
     * @return array Gender statistics.
     */
    private function getGenderStats(int $year): array
    {
        $stats = $this->model->getStatsByGender($year);
        $result = [];
        foreach ($stats as $row) {
            $result[] = (object) [
                'label' => gender((string) $row->label),
                'total' => (int) $row->total
            ];
        }
        return $result;
    }

    /**
     * Get parental income statistics with labels.
     *
     * @param int $year The admission year.
     * @return array Income statistics.
     */
    private function getIncomeStats(int $year): array
    {
        $stats = $this->model->getStatsByIncome($year);
        $result = [];
        foreach ($stats as $row) {
            $label = incomes((string) $row->label);
            if ($label === '0')
                continue; // Skip "0" as it's not a valid category for the chart
            $result[] = (object) [
                'label' => $label,
                'total' => (int) $row->total
            ];
        }
        return $result;
    }
}
