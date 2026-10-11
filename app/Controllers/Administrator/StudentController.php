<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AdmissionOfferingModel;
use App\Models\DocumentRequirementModel;
use App\Models\RegistrationModel;
use App\Models\SubjectGradeRequirementModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use App\Libraries\PDF;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use JsonException;

/**
 * Class StudentController
 *
 * Handles the management of student registrations and verification processes.
 *
 * @package App\Controllers\Administrator
 */
class StudentController extends BaseController
{
    use DataTrait;

    /**
     * Render the student admission page.
     *
     * @param int $offering_id The ID of the admission offering.
     * @return string Rendered HTML view.
     * @throws JsonException
     */
    public function index(int $offering_id = 0): string
    {
        $model = new AdmissionOfferingModel();

        $vars = [
            'title' => 'Data Calon Mahasiswa',
            'query' => $model->findById($offering_id),
            'offering_id' => $offering_id,
            'subject_grade_requirements' => toJson($this->getSubjectGradeRequirements($offering_id)),
            'document_requirements' => toJson($this->getDocumentRequirements($offering_id)),
            'countries' => toJson(countries()),
            'religions' => toJson(religions()),
            'residences' => toJson(residences()),
            'transportations' => toJson(transportations()),
            'educations' => toJson(educations()),
            'occupations' => toJson(occupations()),
            'incomes' => toJson(incomes()),
            'gender' => toJson(gender()),
            'registration_statuses' => toJson(registration_status()),
        ];

        return view('administrator/students', $vars);
    }

    /**
     * Return a list of student registrations in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $offering_id = (int) ($this->request->getVar('offering_id') ?? 0);

        $model = new RegistrationModel();
        $query = $model->findByAdmissionOfferingId($offering_id);
        $records = $query->getResult();

        $db = Database::connect();
        foreach ($records as &$row) {
            $row->submitted = (bool)$row->submitted;
            $row->transfer_proof_verified = (bool)$row->transfer_proof_verified;

            $grades = $db->table('registration_subject_grades')
                ->where('registration_id', $row->record_id)
                ->get()
                ->getResult();
            $row->subject_grade_requirements = json_encode($grades);

            $docs = $db->table('registration_documents')
                ->where('registration_id', $row->record_id)
                ->get()
                ->getResult();
            $row->document_requirements = json_encode($docs);
        }

        return $this->response->setJSON([
            'records' => $records,
        ]);
    }

    /**
     * Retrieve subject grade requirements for a given admission offering.
     *
     * @param int $offering_id The admission offering identifier.
     * @return array List of subject grade requirements.
     */
    private function getSubjectGradeRequirements(int $offering_id = 0): array
    {
        $model = new SubjectGradeRequirementModel();
        $query = $model->findByAdmissionOfferingId($offering_id);

        $subject_grades = [];
        foreach ($query->getResult() as $row) {
            $subject_grades[] = [
                'subject_id' => (int) $row->subject_id,
                'subject_name' => $row->subject_name,
                'threshold_score' => (int) $row->threshold_score,
            ];
        }

        return $subject_grades;
    }

    /**
     * Save student registration details (Verification).
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function save(): ResponseInterface
    {
        $id = (int) ($this->request->getVar('id') ?? 0);
        $db = Database::connect();
        $data = $db->table('registrations')->where('id', $id)->get()->getRow();
        if (!$data || $id <= 0) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ]);
        }

        $updateData = [
            'submitted' => $this->request->getVar('submitted') ? 1 : 0,
            'submitted_at' => $this->request->getVar('submitted_at') ?? null,
            'transfer_proof_verified' => $this->request->getVar('transfer_proof_verified') ? 1 : 0,
            'transfer_proof_verified_at' => $this->request->getVar('transfer_proof_verified_at') ?? null,
            'selection_status' => $this->request->getVar('selection_status') ?? null,
            'selection_notes' => $this->request->getVar('selection_notes') ?? null,
        ];

        $db->table('registrations')->where('id', $id)->update($updateData);
        return $this->response->setJSON([
            'id' => $id,
            'success' => $db->affectedRows() > 0,
            'message' => 'Updated successfully.',
        ]);
    }

    /**
     * Retrieve document requirements for a given admission offering.
     *
     * @param int $offering_id The admission offering identifier.
     * @return array List of document requirements.
     */
    private function getDocumentRequirements(int $offering_id = 0): array
    {
        $model = new DocumentRequirementModel();
        $query = $model->findByAdmissionOfferingId($offering_id);

        $document_requirements = [];
        foreach ($query->getResult() as $row) {
            $document_requirements[] = [
                'document_id' => (int) $row->document_id,
                'document_name' => $row->document_name,
                'max_size' => $row->max_size,
                'required' => (int)$row->required === 1,
            ];
        }

        return $document_requirements;
    }

    /**
     * Generate and render a PDF registration form for a student.
     *
     * @param int $id The registration identifier.
     * @return ResponseInterface|null PDF download response.
     */
    public function print(int $id = 0): ?ResponseInterface
    {
        $db = Database::connect();
        $data = $db->table('registrations')->where('id', $id)->get()->getRow();

        if ($id <= 0 || !isset($data->student_id)) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ]);
        }

        $student_id = $data->student_id;
        $admission_year = $data->admission_year ?? 0;

        $this->response->setHeader('Content-Type', 'application/pdf');

        $registration = new RegistrationModel();
        $query = $registration->findByStudentId((int) $student_id, (int) $admission_year);

        $student = new UserModel()->find($student_id);
        $reg = $query->getResult()[0] ?? null;

        if ($reg === null) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Detil pendaftaran tidak ditemukan.'
            ]);
        }

        $details = $registration->findDetailsById($id);

        $app_settings = $db->table('application_settings')->where('id', 1)->get()->getRow();

        $html = view('students/registration-form', [
            'student' => $student,
            'registration' => $reg,
            'details' => $details->getResult(),
            'applicant_types' => applicant_types(),
            'admission_pathways' => admission_pathways(),
            'educations' => educations(),
            'app_settings' => $app_settings
        ]);

        $pdf = new PDF();
        $file_name = 'FORMULIR-PENERIMAAN-MAHASISWA-BARU';
        $file_name .= '-' . $reg->registration_number . '-' . time() . '.pdf';

        return $this->response->setBody($pdf->render($html, $reg->registration_number, $file_name));
    }

    /**
     * Download a file from the uploads' directory.
     *
     * @param string $file_name The name of the file to download.
     * @return ResponseInterface|null The download response or null if not found.
     */
    public function download(string $file_name): ?ResponseInterface
    {
        $safe_name = basename($file_name);
        $path = FCPATH . 'uploads/';
        $file_path = $path . $safe_name;

        if (!is_file($file_path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($file_path, null)->setFileName($safe_name);
    }

    /**
     * Update a student's subject score.
     *
     * @return ResponseInterface|null JSON response indicating success or failure.
     */
    public function updateSubjectScore(): ?ResponseInterface
    {
        $id = (int) ($this->request->getVar('id') ?? 0);
        $subject_id = (int) ($this->request->getVar('subject_id') ?? 0);
        $score = (int) ($this->request->getVar('score') ?? 0);

        $db = Database::connect();
        $data = $db->table('registrations')->where('id', $id)->get()->getRow();
        if (!$data || $id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ]);
        }


        $existing = $db->table('registration_subject_grades')
            ->where('registration_id', $id)
            ->where('subject_id', $subject_id)
            ->get()
            ->getRow();

        if ($existing) {
            $db->table('registration_subject_grades')
                ->where('id', $existing->id)
                ->update(['score' => $score]);
        } else {
            $offering_id = (int) ($data->admission_offering_id ?? 0);
            if ($offering_id <= 0) {
                $registrationModel = new RegistrationModel();
                $details = $registrationModel->findDetailsById($id)->getRow();
                $offering_id = (int) ($details->admission_offering_id ?? 0);
            }

            $threshold_score = 0;
            if ($offering_id > 0) {
                $masterReqs = $this->getSubjectGradeRequirements($offering_id);
                foreach ($masterReqs as $master) {
                    if ((int) $master['subject_id'] === $subject_id) {
                        $threshold_score = (int) $master['threshold_score'];
                        break;
                    }
                }
            }

            $db->table('registration_subject_grades')->insert([
                'registration_id' => $id,
                'subject_id' => $subject_id,
                'score' => $score,
                'threshold_score' => $threshold_score,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'errors' => [],
        ]);
    }
}
