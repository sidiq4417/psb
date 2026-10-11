<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\AcademicYearModel;
use App\Models\ApplicationSettingModel;
use App\Models\RegistrationDocumentModel;
use App\Models\RegistrationModel;
use App\Models\RegistrationSubjectGradeModel;
use App\Models\UserModel;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\DownloadResponse;
use Exception;

class DashboardController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(): string
    {
        $actor = session()->get('actor') ?? 0;
        if ($actor <= 0) {
            throw new Exception('Unauthorized access.');
        }

        $registrationModel = new RegistrationModel();
        $admission_year = $registrationModel->getLatestAdmissionYearByStudent((int) $actor);

        $registration_data = null;
        if ($admission_year > 0) {
            $registration_data = $registrationModel->findActiveByStudentAndYear($actor, $admission_year);
        }

        $transfer_proof_verified = $registration_data?->transfer_proof_verified ?? false;
        $is_verified = ((int)$transfer_proof_verified === 1);
        $transfer_proof_uploaded_at = $registration_data?->transfer_proof_uploaded_at ?? '';
        $is_uploaded = !empty($transfer_proof_uploaded_at);

        $transfer_proof_status = match (true) {
            $is_verified => 'Sudah Diverifikasi',
            $is_uploaded => 'Belum Diverifikasi',
            default => 'Belum Dibayar',
        };

        $userModel = new UserModel();
        $user_data = $userModel->find($actor) ?? (object) [];

        $reg_id = (int) ($registration_data?->id ?? 0);
        $offering_id = (int) ($registration_data?->admission_offering_id ?? 0);

        $gradeModel = new RegistrationSubjectGradeModel();
        $docModel = new RegistrationDocumentModel();

        $steps = [
            'profil' => !empty($user_data->nik) && !empty($user_data->gender) && !empty($user_data->birth_place) && !empty($user_data->religion_id),
            'alamat' => !empty($user_data->address_country_id) && !empty($user_data->address_province_id) && !empty($user_data->address_district_id) && !empty($user_data->address_subdistrict_id) && !empty($user_data->street_address),
            'kontak' => !empty($user_data->mobile_phone) || !empty($user_data->whatsapp),
            'orang_tua' => !empty($user_data->father_name) || !empty($user_data->mother_name),
            'wali' => !empty($user_data->guardian_name),
            'sekolah' => !empty($user_data->nisn) || !empty($user_data->school_name),
            'pilihan_prodi' => $registration_data && !empty($registration_data->admission_offering_id),
            'nilai_rapor' => $reg_id > 0 && $offering_id > 0 && empty($gradeModel->findMissingScores($reg_id, $offering_id)),
            'dokumen' => $reg_id > 0 && $offering_id > 0 && empty($docModel->findMissingRequired($reg_id, $offering_id)),
            'finalisasi' => $registration_data && !empty($registration_data->submitted),
        ];

        $completed_count = count(array_filter($steps));
        $total_count = count($steps);
        $kebab = ($completed_count / max($total_count, 1)) * 100;

        $setting_data = new ApplicationSettingModel()->getSetting();

        $vars['title'] = 'Dashboard';
        $vars['email'] = $setting_data->email ?? '';
        $vars['telephone'] = $setting_data->telephone ?? '';
        $vars['mobile_phone'] = $setting_data->mobile_phone ?? '';
        $vars['admission_fee'] = $registration_data->admission_fee ?? '';
        $vars['admission_year'] = $admission_year;
        $vars['academic_year'] = (new AcademicYearModel())->getNameByAdmissionYear($admission_year);
        $vars['institution_name'] = $setting_data->institution_name ?? '';

        $is_submitted = $registration_data && (!empty($registration_data->submitted) || !empty($registration_data->submitted_at));
        $vars['is_submitted'] = $is_submitted;
        $vars['has_registration'] = $registration_data !== null;
        $vars['majorLabel'] = 'Program Pendidikan';

        $vars['transfer_proof_status'] = $transfer_proof_status;
        $vars['selection_status_label'] = $registration_data ? registration_status($registration_data->selection_status ?? '0') : '-';
        $vars['selection_status'] = $registration_data ? ($registration_data->selection_status ?? '0') : '0';
        $vars['payment_method'] = $setting_data->payment_method ?? '';

        $vars['steps'] = $steps;
        $vars['completed_count'] = $completed_count;
        $vars['total_count'] = $total_count;
        $vars['completion_pct'] = round($kebab);

        $vars['study_programs'] = [];

        return view('students/dashboard', $vars);
    }

    public function paymentMethod(): ?DownloadResponse
    {
        $setting_data = new ApplicationSettingModel()->getSetting();
        $payment_method = $setting_data->payment_method ?? '';

        if ($payment_method === '') {
            throw PageNotFoundException::forPageNotFound('Payment method file not specified.');
        }

        $safeName = basename($payment_method);
        $file_path = public_upload_path() . $safeName;

        if (!is_file($file_path)) {
            throw PageNotFoundException::forPageNotFound('File not found: ' . $safeName);
        }

        return $this->response->download($file_path, null)->setFileName($safeName);
    }

    public function registrationTerms(): ?DownloadResponse
    {
        $setting_data = new ApplicationSettingModel()->getSetting();
        $registration_terms = $setting_data->registration_terms ?? '';

        if ($registration_terms === '') {
            throw PageNotFoundException::forPageNotFound('Registration terms file not specified.');
        }

        $safeName = basename($registration_terms);
        $file_path = public_upload_path() . $safeName;

        if (!is_file($file_path)) {
            throw PageNotFoundException::forPageNotFound('File not found: ' . $safeName);
        }

        return $this->response->download($file_path, null)->setFileName($safeName);
    }
}
