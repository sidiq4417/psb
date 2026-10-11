<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\AdmissionOfferingModel;
use App\Models\ApplicationSettingModel;
use App\Models\RegistrationDocumentModel;
use App\Models\RegistrationExamScoreModel;
use App\Models\RegistrationModel;
use App\Models\RegistrationSubjectGradeModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use App\Libraries\PDF;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\HTTP\ResponseInterface;
use JsonException;
use ReflectionException;
use Throwable;

class ConfirmationController extends BaseController
{
    protected int $actor = 0;

    use DataTrait;

    public function __construct()
    {
        $this->actor = session()->get('actor') ?? 0;
    }

    /**
     * @throws JsonException
     */
    public function index(): string|ResponseInterface
    {
        $registrationModel = new RegistrationModel();
        $admission_year = $registrationModel->getAdmissionYearByStudent((int) session()->get('actor'));
        if ($admission_year <= 0) {
            return redirect()->to('/not-found')->with('error', 'No active admission period or existing registration found.');
        }
        $summaries = [];
        $registrations = $registrationModel->findActiveByStudent((int)session()->get('actor'));

        $gradeModel = new RegistrationSubjectGradeModel();
        $docModel = new RegistrationDocumentModel();

        foreach ($registrations as $registrationData) {
            if (!isset($registrationData->admission_offering_id) || (int)$registrationData->admission_offering_id <= 0) {
                continue;
            }
            $details = $registrationModel->findDetailsById((int)$registrationData->id)->getRow();
            if (!$details) {
                continue;
            }
            $summaries[] = [
                'id' => $registrationData->id,
                'entry_grade' => $details->entry_grade ?? '-',
                'school' => $details->branch ?? '-',
                'education_track' => $details->education_track ?? '-',
                'education_program' => $details->education_program ?? '-',
                'applicant_type' => $details->applicant_type ?? '-',
                'submitted' => (bool)($registrationData->submitted ?? false),
                'registration_number' => $registrationData->registration_number ?? '-',
                'admission_fee' => (int)($registrationData->admission_fee ?? 0),
                'grade_count' => $registrationData->id > 0 ? $gradeModel->countByRegistrationId((int)$registrationData->id) : 0,
                'document_count' => $registrationData->id > 0 ? $docModel->countUploadedByRegistrationId((int)$registrationData->id) : 0,
            ];
        }

        $vars['title'] = 'Konfirmasi Pendaftaran';
        $vars['breadcrumb'] = 'Konfirmasi Pendaftaran';
        $vars['applicant_types'] = toJson(applicant_types());
        $vars['admission_pathways'] = toJson(admission_pathways());
        $vars['educations'] = toJson(educations());
        $vars['summaries'] = $summaries;
        $vars['allow_multiple'] = new ApplicationSettingModel()->isMultiRegistrationEnabled();
        $vars['unsubmitted_count'] = count($registrationModel->findUnsubmittedByStudent((int)session()->get('actor')));

        return view('students/confirmation', $vars);
    }

    public function lists(): ResponseInterface
    {
        $registration = new RegistrationModel();
        $query = $registration->findByStudentId((int) $this->actor);
        return $this->response->setJSON([
            'records' => $query->getResult()
        ]);
    }

    public function details(): ?ResponseInterface
    {
        $input = $this->request->getJSON(true) ?? [];
        $id = (int)($input['id'] ?? 0);
        $registration = new RegistrationModel();
        $data = $registration->findActiveById($id);
        if (!$data || (int)$data->student_id !== (int)session()->get('actor')) {
            throw PageNotFoundException::forPageNotFound();
        }
        $query = $registration->findDetailsById($id);

        return $this->response->setJSON([
            'records' => $query->getResult()
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function delete(): ?ResponseInterface
    {
        $input = $this->request->getJSON(true);
        $id = (int)($input['id'] ?? 0);
        $registrationModel = new RegistrationModel();
        $data = $registrationModel->findActiveById($id);
        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ]);
        }
        if ((int)$data->student_id !== (int)session()->get('actor')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak.'
            ]);
        }
        if ((int)($data->submitted ?? 0) === 1 || ($data->submitted_at ?? '') !== '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran yang sudah disubmit tidak dapat dibatalkan!',
            ]);
        }
        $transfer_proof_verified = $data->transfer_proof_verified ?? false;
        if ((int)$transfer_proof_verified === 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pembayaran sudah diverifikasi admin. Tidak dapat membatalkan pendaftaran.',
            ]);
        }
        $actor = (int) session()->get('actor');

        $siblings = $registrationModel->findUnsubmittedSiblings($actor, $id);

        $gradeModel = new RegistrationSubjectGradeModel();
        $docModel = new RegistrationDocumentModel();

        foreach ($siblings as $sibling) {
            $sibling_id = (int)$sibling->id;
            $gradeModel->copyFromRegistration($id, $sibling_id);
            $docModel->copyFromRegistration($id, $sibling_id);
        }

        $stored_files = array_map(fn($d) => (string)($d->file ?? ''), $docModel->findByRegistrationId($id));
        $stored_files[] = (string)($data->transfer_proof ?? '');

        $db = db_connect();
        $db->transStart();
        $registrationModel->update($id, [
            'unique_id' => null,
            'admission_offering_id' => 0,
            'admission_fee' => 0,
            'transfer_proof' => null,
            'transfer_proof_uploaded_at' => null,
            'transfer_proof_verified' => 0,
            'is_deleted' => 1,
            'deleted_by' => $actor,
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);
        $gradeModel->deleteByRegistrationId($id);
        $docModel->deleteByRegistrationId($id);
        new RegistrationExamScoreModel()->deleteByRegistrationId($id);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal membatalkan pendaftaran. Silakan coba lagi.',
            ]);
        }

        $docModel->deleteUnreferencedFiles($stored_files);

        return $this->response->setJSON([
            'id' => $id,
            'success' => true,
            'message' => 'Pendaftaran berhasil dibatalkan.',
        ]);
    }

    /**
     * Finalise all of the student's unsubmitted registrations under one shared
     * registration number. Nothing is submitted unless every registration is
     * complete.
     *
     * @throws ReflectionException
     */
    public function submitAll(): ResponseInterface
    {
        $student_id = (int) session()->get('actor');
        $registrationModel = new RegistrationModel();

        $registrations = $registrationModel->findUnsubmittedByStudent($student_id);

        if (empty($registrations)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada pendaftaran yang perlu disubmit.'
            ]);
        }

        $errors = [];
        foreach ($registrations as $reg) {
            $error = $this->findSubmitError($registrationModel, $reg, $student_id);
            if ($error !== null) {
                $errors[] = $this->registrationLabel($registrationModel, (int)$reg->id) . ': ' . $error;
            }
        }

        if (!empty($errors)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Belum ada pendaftaran yang dikirim final. ' . implode(' ', $errors),
                'errors' => $errors,
            ]);
        }

        $shared_year = (int)($registrations[0]->admission_year ?? 0);
        if ($shared_year <= 0) {
            $shared_year = $registrationModel->getAdmissionYearByStudent($student_id);
        }

        $ids = array_map(fn($r) => (int)$r->id, $registrations);
        $shared_number = $registrationModel->submitRegistrations($ids, $shared_year, $student_id);
        if ($shared_number === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengirim pendaftaran. Silakan coba lagi.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => count($ids) . ' pendaftaran berhasil dikirim final dengan No. Registrasi ' . $shared_number . '.',
            'submitted_ids' => $ids,
            'registration_number' => $shared_number,
        ]);
    }

    /**
     * Finalise a single registration.
     *
     * @throws ReflectionException
     */
    public function submit(): ?ResponseInterface
    {
        $student_id = (int) session()->get('actor');
        $registrationModel = new RegistrationModel();
        $input = $this->request->getJSON(true) ?? [];
        $id = (int)($input['id'] ?? 0);
        $data = $registrationModel->findActiveById($id);
        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ]);
        }

        if ((int)($data->student_id ?? 0) !== $student_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak.'
            ]);
        }

        if ((int)($data->submitted ?? 0) === 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran ini sudah dikirim final.'
            ]);
        }

        $admission_year = (int)($data->admission_year ?? 0);
        if ($admission_year <= 0) {
            $admission_year = $registrationModel->getAdmissionYearByStudent($student_id);
        }
        if ($admission_year <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sistem pendaftaran aktif tidak ditemukan.'
            ]);
        }

        $error = $this->findSubmitError($registrationModel, $data, $student_id);
        if ($error !== null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $error
            ]);
        }

        $registration_number = $registrationModel->submitRegistrations([$id], $admission_year, $student_id);
        if ($registration_number === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengirim pendaftaran. Silakan coba lagi.'
            ]);
        }

        return $this->response->setJSON([
            'id' => $id,
            'success' => true,
            'message' => 'Pendaftaran berhasil dikirim final dengan No. Registrasi ' . $registration_number . '.',
            'registration_number' => $registration_number,
        ]);
    }

    /**
     * Check whether a registration may be finalised.
     *
     * The admission period must still be open, every required subject grade
     * must have a score and every required document (general + offering
     * specific, the same list shown on the student pages) must be uploaded.
     * Grades and documents of the student's other unsubmitted registration are
     * copied first, because the student pages fill all registrations at once.
     *
     * @param RegistrationModel $registrationModel The registration model.
     * @param object            $registration      The registration row.
     * @param int               $student_id        The logged-in student.
     * @return string|null The reason it cannot be submitted, or null when ready.
     * @throws ReflectionException
     */
    private function findSubmitError(RegistrationModel $registrationModel, object $registration, int $student_id): ?string
    {
        $id = (int)$registration->id;
        $offering_id = (int)($registration->admission_offering_id ?? 0);
        if ($offering_id <= 0) {
            return 'Program pendaftaran belum dipilih.';
        }

        if (!new AdmissionOfferingModel()->isOpen($offering_id)) {
            return 'Periode pendaftaran untuk program ini sudah ditutup.';
        }

        $gradeModel = new RegistrationSubjectGradeModel();
        $docModel = new RegistrationDocumentModel();

        $source_reg = $registrationModel->findLatestUnsubmitted($student_id, $id);
        if ($source_reg) {
            $gradeModel->copyFromRegistration((int)$source_reg->id, $id);
            $docModel->copyFromRegistration((int)$source_reg->id, $id);
        }

        $missing_scores = $gradeModel->findMissingScores($id, $offering_id);
        if (!empty($missing_scores)) {
            return 'Harap lengkapi nilai rapor sebelum melakukan submit final (' . implode(', ', $missing_scores) . ').';
        }

        $missing_documents = $docModel->findMissingRequired($id, $offering_id);
        if (!empty($missing_documents)) {
            return 'Harap unggah semua dokumen wajib sebelum melakukan submit final (' . implode(', ', $missing_documents) . ').';
        }

        return null;
    }

    /**
     * Build a short human-readable label for a registration.
     *
     * @param RegistrationModel $registrationModel The registration model.
     * @param int               $id                The registration ID.
     * @return string The label, e.g. "SMP - Kelas 7 - Reguler".
     */
    private function registrationLabel(RegistrationModel $registrationModel, int $id): string
    {
        $details = $registrationModel->findDetailsById($id)->getRow();
        $parts = array_filter([
            (string)($details->branch ?? ''),
            (string)($details->entry_grade ?? ''),
            (string)($details->education_track ?? ''),
        ], fn($v) => $v !== '');
        return empty($parts) ? 'Pendaftaran #' . $id : implode(' - ', $parts);
    }

    /**
     * Upload or replace the transfer proof of a registration.
     *
     * @throws ReflectionException
     */
    public function upload(): ResponseInterface
    {
        $id = (int) $this->request->getPost('id');
        $student_id = (int) session()->get('actor');

        $registrationModel = new RegistrationModel();
        $data = $registrationModel->findActiveById($id);
        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['registration' => 'Data pendaftaran tidak ditemukan.']
            ]);
        }

        if ((int)$data->student_id !== $student_id) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['registration' => 'Akses ditolak.']
            ]);
        }

        if ((int)($data->transfer_proof_verified ?? 0) === 1) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['registration' => 'Bukti pembayaran sudah diverifikasi dan tidak dapat diganti.']
            ]);
        }

        $file = $this->request->getFile('file');
        if (!$file) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['file' => 'Tidak ada file yang diunggah.']
            ]);
        }

        $validated = $this->validate([
            'file' => [
                'label' => 'Bukti Transfer',
                'rules' => [
                    'uploaded[file]',
                    'mime_in[file,image/jpg,image/jpeg,image/gif,image/png,application/pdf]',
                    'max_size[file,2048]',
                ],
            ],
        ]);

        if (!$validated) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        if (!$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => [
                    'code' => 500,
                    'message' => $file->getErrorString()
                ]
            ]);
        }

        $uploadPath = private_upload_path();
        $newName = $file->getRandomName();

        try {
            $file->move($uploadPath, $newName);
        } catch (Throwable $e) {
            log_message('error', 'Transfer proof upload failed: {message}', ['message' => $e->getMessage()]);
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['upload' => 'Gagal menyimpan file. Silakan coba lagi.']
            ]);
        }

        $registrationModel->where('transfer_proof_verified', 0)->update($id, [
            'transfer_proof' => $newName,
            'transfer_proof_uploaded_at' => date('Y-m-d H:i:s'),
        ]);

        if (db_connect()->affectedRows() < 1) {
            @unlink($uploadPath . $newName);
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['registration' => 'Bukti pembayaran tidak dapat disimpan. Silakan muat ulang halaman.']
            ]);
        }

        $old_file = trim((string)($data->transfer_proof ?? ''));
        if ($old_file !== '' && $old_file !== $newName) {
            new RegistrationDocumentModel()->deleteUnreferencedFiles([$old_file]);
        }

        return $this->response->setJSON([
            'id' => $id,
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diunggah.',
            'file_name' => $newName,
        ]);
    }

    public function download(string $filename): ?DownloadResponse
    {
        $safeName = basename($filename);
        $student_id = (int)session()->get('actor');

        $registrationModel = new RegistrationModel();
        $owns_file = $registrationModel->ownsTransferProof($student_id, $safeName);

        if (!$owns_file) {
            $owns_file = new RegistrationDocumentModel()->ownsFile($student_id, $safeName);
        }
        if (!$owns_file) {
            throw PageNotFoundException::forPageNotFound('File not found.');
        }

        $filePath = find_registration_file($safeName);
        if ($filePath === null) {
            throw PageNotFoundException::forPageNotFound('File not found.');
        }

        return $this->response->download($filePath, null)->setFileName($safeName);
    }

    public function print(): ?ResponseInterface
    {
        $student_id = (int) session()->get('actor');
        if ($student_id <= 0) {
            return redirect()->to('/unauthorized')->with('error', 'You do not have permission to access this page.');
        }

        $registrationModel = new RegistrationModel();
        $registrations = $registrationModel->findSubmittedByStudent($student_id);

        if (empty($registrations)) {
            return redirect()->to('/not-found')->with('error', 'Registration record not found.');
        }

        $student = new UserModel()->find($student_id);
        if (!$student) {
            return redirect()->to('/not-found')->with('error', 'Student record not found.');
        }

        $first_reg = $registrations[0];
        $shared_number = $first_reg->registration_number ?? '-';

        $reg_ids = array_map(fn($r) => (int)$r->id, $registrations);
        if (empty($reg_ids)) {
            return redirect()->to('/not-found')->with('error', 'Registration record not found.');
        }
        $details = $registrationModel->findDetailsByIds($reg_ids);

        $app_settings = new ApplicationSettingModel()->getSetting();

        $html = view('students/registration-form', [
            'student' => $student,
            'registration' => $first_reg,
            'shared_number' => $shared_number,
            'details' => $details,
            'app_settings' => $app_settings
        ]);

        $pdf = new PDF();
        $file_name = 'FORMULIR-PENERIMAAN-SANTRI-BARU-' . $shared_number . '-' . time() . '.pdf';

        $content = $pdf->render($html, $shared_number, $file_name);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', "inline; filename=\"$file_name\"")
            ->setBody($content);
    }
}
