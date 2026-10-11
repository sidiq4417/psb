<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AdmissionPeriodModel;
use App\Models\DashboardModel;
use App\Controllers\BaseController;

/**
 * Class DashboardController
 *
 * Handles the administrator dashboard and provides an analytical overview of registrations.
 *
 * @package App\Controllers\Administrator
 */
class DashboardController extends BaseController
{
    /**
     * Display the administrator dashboard.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $admissionPeriodModel = new AdmissionPeriodModel();
        $admission_year = $admissionPeriodModel->getActiveAdmissionYear();
        $academic_year = $admissionPeriodModel->getActiveAcademicYearName();

        $model = new DashboardModel();

        $majorLabel = 'Program Pendidikan';

        // Retrieve statistics and analytical data from the model
        $stats = $model->getSummaryStats($admission_year);
        // Applicant names and registration numbers are admin-only, like "Data Pendaftar";
        // operators only see the aggregate counts and charts.
        $isAdmin = (bool) session()->get('is_admin');
        $registrationsToday = $isAdmin ? $model->getRegistrationsToday() : [];
        $pendingPayments = $isAdmin ? $model->getPendingPayments($admission_year) : [];
        $trendData = $model->getRegistrationTrend();
        $majorStats = $model->getPopularMajors($admission_year);

        return view('administrator/dashboard', [
            'title' => 'Dashboard',
            'majorLabel' => $majorLabel,
            'admission_year' => $admission_year,
            'academic_year' => $academic_year,
            'stats' => $stats,
            'registrations_today' => $registrationsToday,
            'pending_payments' => $pendingPayments,
            'is_admin' => $isAdmin,
            'trend_data' => $trendData,
            'major_stats' => $majorStats
        ]);
    }
}
