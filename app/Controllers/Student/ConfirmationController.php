<?php

declare(strict_types=1);

namespace App\Controllers\Student;

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
        $input = $this->request->getJSON(true);
        $id = (int)($input['id'] ?? 0);
        $registration = new RegistrationModel();
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
                'message' => 'Registration not found.'
            ]);
        }
        if ((int)$data->student_id !== (int)session()->get('actor')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized.'
            ]);
        }
        if (($data->submitted_at ?? '') !== '') {
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

        if (!empty($siblings)) {
            $sibling_ids = array_map(fn($r) => (int)$r->id, $siblings);
            $grades = $gradeModel->findByRegistrationId($id);
            foreach ($grades as $g) {
                foreach ($sibling_ids as $sid) {
                    $gradeModel->copyFromRegistration($id, $sid);
                }
            }

            $docs = $docModel->findByRegistrationId($id);
            foreach ($docs as $d) {
                foreach ($sibling_ids as $sid) {
                    $docModel->copyFromRegistration($id, $sid);
                }
            }
        }

        $db = db_connect();
        $db->transStart();
        $registrationModel->update($id, [
            'unique_id' => null,
            'admission_offering_id' => 0,
            'admission_fee' => 0,
            'transfer_proof' => null,
            'transfer_proof_verified' => 0,
            'is_deleted' => 1,
            'deleted_by' => $actor,
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);
        $gradeModel->deleteByRegistrationId($id);
        $docModel->deleteByRegistrationId($id);
        new RegistrationExamScoreModel()->deleteByRegistrationId($id);
        $db->transComplete();
        return $this->response->setJSON([
            'id' => $id,
            'success' => true,
            'message' => 'Updated successfully.',
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function submitAll(): ResponseInterface
    {
        $student_id = (int) session()->get('actor');
        $registrationModel = new RegistrationModel();
        $gradeModel = new RegistrationSubjectGradeModel();
        $docModel = new RegistrationDocumentModel();

        $registrations = $registrationModel->findUnsubmittedByStudent($student_id);

        if (empty($registrations)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada pendaftaran yang perlu disubmit.'
            ]);
        }

        $errors = [];
        $submitted_ids = [];
        $shared_number = null;
        $shared_year = 0;

        $db = db_connect();
        $db->transStart();

        foreach ($registrations as $reg) {
            $id = (int)$reg->id;
            $admission_year = (int)($reg->admission_year ?? $registrationModel->getAdmissionYearByStudent($student_id));

            if ($shared_number === null) {
                $shared_year = $admission_year;
                $shared_number = $registrationModel->generateRegistrationNumber($shared_year);
            }

            $grade_count = $gradeModel->countByRegistrationId($id);
            if ($grade_count === 0) {
                $source_reg = $registrationModel->findLatestUnsubmitted($student_id, $id);
                if ($source_reg) {
                    $gradeModel->transferToRegistration((int)$source_reg->id, $id);
                }
                $grade_count = $gradeModel->countByRegistrationId($id);
            }
            if ($grade_count === 0) {
                $errors[] = 'Nilai rapor belum diinisialisasi.';
                continue;
            }
            if ($gradeModel->hasEmptyGrade($id)) {
                $errors[] = 'Harap lengkapi semua nilai rapor.';
                continue;
            }

            $doc_count = $docModel->countByRegistrationId($id);
            if ($doc_count === 0) {
                $source_reg = $registrationModel->findLatestUnsubmitted($student_id, $id);
                if ($source_reg) {
                    $docModel->copyFromRegistration((int)$source_reg->id, $id);
                }
                $doc_count = $docModel->countByRegistrationId($id);
            }
            if ($doc_count === 0) {
                $errors[] = 'Persyaratan dokumen belum diinisialisasi.';
                continue;
            }
            if ($docModel->hasEmptyFile($id)) {
                $errors[] = 'Harap unggah semua dokumen wajib.';
                continue;
            }

            $registrationModel->update($id, [
                'admission_year' => $shared_year,
                'registration_number' => $shared_number,
                'submitted' => 1,
                'submitted_at' => date('Y-m-d H:i:s'),
                'submitted_by' => $student_id,
            ]);
            $submitted_ids[] = $id;
        }

        $db->transComplete();

        if (empty($submitted_ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada pendaftaran yang berhasil disubmit. ' . implode(' ', $errors)
            ]);
        }

        $has_errors = !empty($errors);
        $message = count($submitted_ids) . ' pendaftaran berhasil disubmit.';
        if ($has_errors) {
            $message .= ' ' . implode(' ', $errors);
        }

        return $this->response->setJSON([
            'success' => !$has_errors,
            'message' => $message,
            'submitted_ids' => $submitted_ids,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function submit(): ?ResponseInterface
    {
        $registrationModel = new RegistrationModel();
        $admission_year = $registrationModel->getAdmissionYearByStudent((int) session()->get('actor'));
        if ($admission_year <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sistem pendaftaran aktif tidak ditemukan.'
            ]);
        }
        $input = $this->request->getJSON(true);
        $id = (int)($input['id'] ?? 0);
        $data = $registrationModel->findActiveById($id);
        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ]);
        }

        $student_id = (int)($data->student_id ?? 0);
        if ($student_id !== (int)session()->get('actor')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized.'
            ]);
        }

        $gradeModel = new RegistrationSubjectGradeModel();
        $docModel = new RegistrationDocumentModel();

        $grade_count = $gradeModel->countByRegistrationId($id);
        if ($grade_count === 0) {
            $source_reg = $registrationModel->findLatestUnsubmitted($student_id, $id);
            if ($source_reg) {
                $gradeModel->transferToRegistration((int)$source_reg->id, $id);
                $grade_count = $gradeModel->countByRegistrationId($id);
            }
        }
        if ($grade_count === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data nilai rapor belum diinisialisasi. Silakan buka menu Nilai Rapor.'
            ]);
        }
        if ($gradeModel->hasEmptyGrade($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Harap lengkapi semua nilai rapor sebelum melakukan submit final.'
            ]);
        }

        $doc_count = $docModel->countByRegistrationId($id);
        if ($doc_count === 0) {
            $source_reg = $registrationModel->findLatestUnsubmitted($student_id, $id);
            if ($source_reg) {
                $docModel->copyFromRegistration((int)$source_reg->id, $id);
                $doc_count = $docModel->countByRegistrationId($id);
            }
        }
        if ($doc_count === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Persyaratan dokumen belum diinisialisasi. Silakan buka menu Berkas Pendaftaran.'
            ]);
        }
        if ($docModel->hasEmptyFile($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Harap unggah semua dokumen wajib sebelum melakukan submit final.'
            ]);
        }

        $db = db_connect();
        $db->transStart();
        $registration_number = $registrationModel->generateRegistrationNumber($admission_year);
        $registrationModel->update($id, [
            'admission_year' => $admission_year,
            'registration_number' => $registration_number,
            'submitted' => 1,
            'submitted_at' => date('Y-m-d H:i:s'),
            'submitted_by' => session()->get('actor'),
        ]);
        $db->transComplete();
        return $this->response->setJSON([
            'id' => $id,
            'success' => true,
            'message' => 'Updated successfully.',
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function upload(): ResponseInterface
    {
        $id = (int) $this->request->getPost('id');
        $file = $this->request->getFile('file');

        if (!$file) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['file' => 'No file uploaded.']
            ]);
        }

        $validated = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,image/jpg,image/jpeg,image/gif,image/png,application/pdf]',
                'max_size[file,2048]',
            ],
        ]);

        if (!$validated) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        if ($file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/';
            $newName = $file->getRandomName();

            try {
                $file->move($uploadPath, $newName);
            } catch (Throwable $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['upload' => 'Failed to move file: ' . $e->getMessage()]
                ]);
            }

            $registrationModel = new RegistrationModel();
            $data = $registrationModel->findActiveById($id);
            if (!$data) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['registration' => 'Registration not found.']
                ]);
            }

            if ((int)$data->student_id !== (int)session()->get('actor')) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => ['registration' => 'Unauthorized.']
                ]);
            }

            $registrationModel->update($id, [
                'transfer_proof' => $newName,
            ]);
            return $this->response->setJSON([
                'id' => $id,
                'success' => true,
                'message' => 'Updated successfully.',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'errors' => [
                'code' => 500,
                'message' => $file->getErrorString()
            ]
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

        $filePath = FCPATH . 'uploads/' . $safeName;
        if (!is_file($filePath)) {
            throw PageNotFoundException::forPageNotFound('File not found: ' . $safeName);
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
        $file_name = 'FORMULIR-PENERIMAAN-MURID-BARU-' . $shared_number . '-' . time() . '.pdf';

        $content = $pdf->render($html, $shared_number, $file_name);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', "inline; filename=\"$file_name\"")
            ->setBody($content);
    }
}
