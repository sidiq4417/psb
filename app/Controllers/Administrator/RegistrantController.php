<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AdmissionPeriodModel;
use App\Models\BranchModel;
use App\Models\EducationProgramModel;
use App\Models\EducationTrackModel;
use App\Models\EntranceExamScoreModel;
use App\Models\EntryGradeModel;
use App\Models\RegistrationModel;
use App\Models\UserModel;
use App\Traits\DataTrait;
use App\Libraries\PDF;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Services;
use JsonException;

/**
 * Class RegistrantController
 *
 * Handles the management of prospective students (registrants) and their registration process.
 *
 * @package App\Controllers\Administrator
 */
class RegistrantController extends BaseController
{
    /**
     * Table
     * @var string $table
     */
    protected string $table = 'registrations';

    /**
     * Primary Key
     * @var int $id
     */
    protected int $id = 0;

    use DataTrait;

    /**
     * RegistrantController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $request = Services::request();
        $this->id = (int) ($request->getVar('id') ?? 0);
    }

    /**
     * Render the registrant management page.
     *
     * Loads supporting data such as schools, entry grades, education tracks,
     * education programs, and lookup values for the registrant form.
     *
     * @return string Rendered HTML view.
     * @throws JsonException
     */
    public function index(): string
    {
        $schools = [];
        foreach (new BranchModel()->findAll() as $row) {
            $schools[$row->id] = $row->name;
        }

        $entry_grades = [];
        foreach (new EntryGradeModel()->findAll() as $row) {
            $entry_grades[$row->id] = $row->name;
        }

        $education_tracks = [];
        foreach (new EducationTrackModel()->findAll() as $row) {
            $education_tracks[$row->id] = $row->name;
        }

        $education_programs = [];
        foreach (new EducationProgramModel()->findAll() as $row) {
            $education_programs[$row->id] = $row->name;
        }

        $admissionPeriodModel = new AdmissionPeriodModel();
        $admission_years = $admissionPeriodModel->getAllAdmissionYears();
        if (empty($admission_years)) {
            $admission_years = [(int) date('Y')];
        }
        $active_admission_year = $admissionPeriodModel->getActiveAdmissionYear();

        $vars = [
            'title' => 'Calon Siswa Baru',
            'admission_years' => $admission_years,
            'active_admission_year' => $active_admission_year,
            'countries' => toJson(countries()),
            'religions' => toJson(religions()),
            'residences' => toJson(residences()),
            'transportations' => toJson(transportations()),
            'educations' => toJson(educations()),
            'occupations' => toJson(occupations()),
            'incomes' => toJson(incomes()),
            'gender' => toJson(gender()),
            'registration_statuses' => toJson(registration_status()),
            'schools' => toJson($schools),
            'entry_grades' => toJson($entry_grades),
            'education_tracks' => toJson($education_tracks),
            'education_programs' => toJson($education_programs),
        ];

        return view('administrator/registrants', $vars);
    }

    /**
     * Return a filtered list of registrants in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $admission_year = $this->request->getVar('admission_year') ?? 0;
        $selection_status = $this->request->getVar('selection_status') ?? '';
        $school_id = $this->request->getVar('school_id') ?? 0;
        $entry_grade_id = $this->request->getVar('entry_grade_id') ?? 0;
        $education_track_id = $this->request->getVar('education_track_id') ?? 0;
        $education_program_id = $this->request->getVar('education_program_id') ?? 0;
        $model = new RegistrationModel();
        $query = $model->findByAdmissionYear((int) $admission_year, (string) $selection_status, (int) $school_id, (int) $entry_grade_id, (int) $education_track_id, (int) $education_program_id);
        $records = $query->getResult();

        $db = Database::connect();

        $ids = array_map(fn($r) => (int)$r->id, $records);

        $grade_map = [];
        if (!empty($ids)) {
            $grades_rows = $db->table('registration_subject_grades')
                ->whereIn('registration_id', $ids)
                ->get()
                ->getResult();
            foreach ($grades_rows as $g) {
                $grade_map[(int)$g->registration_id][] = $g;
            }
        }

        $doc_map = [];
        if (!empty($ids)) {
            $doc_rows = $db->table('registration_documents')
                ->whereIn('registration_id', $ids)
                ->get()
                ->getResult();
            foreach ($doc_rows as $d) {
                $doc_map[(int)$d->registration_id][] = $d;
            }
        }

        foreach ($records as &$row) {
            $row->submitted = (bool)$row->submitted;
            $row->transfer_proof_verified = (bool)$row->transfer_proof_verified;
            $row->subject_grade_requirements = json_encode($grade_map[(int)$row->id] ?? []);
            $row->document_requirements = json_encode($doc_map[(int)$row->id] ?? []);
        }

        return $this->response->setJSON([
            'records' => $records
        ]);
    }


    /**
     * Save or update the registrant verification status.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function save(): ResponseInterface
    {
        $id = (int) ($this->request->getVar('id') ?? 0);
        $selection_status = (string)$this->request->getPost('selection_status');

        $allowed_statuses = array_keys(registration_status());
        if ($selection_status !== '' && !in_array($selection_status, $allowed_statuses, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status seleksi tidak valid.',
            ]);
        }

        $db = Database::connect();
        $updateData = [
            'submitted' => (bool)$this->request->getPost('submitted'),
            'submitted_at' => (string)$this->request->getPost('submitted_at'),
            'transfer_proof_verified' => (bool)$this->request->getPost('transfer_proof_verified'),
            'transfer_proof_verified_at' => (string)$this->request->getPost('transfer_proof_verified_at'),
            'selection_status' => $selection_status,
            'selection_notes' => (string)$this->request->getPost('selection_notes'),
            'updated_by' => (int)session()->get('actor'),
        ];
        $db->table('registrations')->where('id', $id)->update($updateData);
        $success = $db->affectedRows() > 0;
        $res = (object)[
            'id' => $id,
            'success' => $success,
            'message' => $success ? 'Updated successfully.' : 'Update failed.',
            'errors' => $db->error(),
        ];
        return $this->response->setJSON($res);
    }

    /**
     * Return the list of provinces for the cascade selects.
     *
     * @return ResponseInterface JSON response containing the provinces map.
     */
    public function provinces(): ResponseInterface
    {
        $filePath = WRITEPATH . 'provinces.json';
        if (!is_file($filePath) || !is_readable($filePath)) {
            return $this->response->setJSON(['provinces' => []]);
        }

        $provinces = json_decode(file_get_contents($filePath));
        if (!is_array($provinces) && !is_object($provinces)) {
            return $this->response->setJSON(['provinces' => []]);
        }

        $data = [];
        foreach ($provinces as $province) {
            $data[$province->province_id] = $province->province_name;
        }
        ksort($data);

        return $this->response->setJSON(['provinces' => $data]);
    }

    /**
     * Return the districts belonging to the given province.
     *
     * @return ResponseInterface JSON response containing the districts map.
     */
    public function districts(): ResponseInterface
    {
        $province_id = (string) $this->request->getVar('province_id');
        $filePath = WRITEPATH . 'districts.json';
        if (!is_file($filePath) || !is_readable($filePath)) {
            return $this->response->setJSON(['districts' => []]);
        }

        $districts = json_decode(file_get_contents($filePath));
        if (!is_array($districts) && !is_object($districts)) {
            return $this->response->setJSON(['districts' => []]);
        }

        $data = [];
        foreach ($districts as $district) {
            if ((string) $district->province_id === $province_id) {
                $data[$district->district_id] = $district->district_name;
            }
        }
        ksort($data);

        return $this->response->setJSON(['districts' => $data]);
    }

    /**
     * Return the subdistricts belonging to the given district.
     *
     * @return ResponseInterface JSON response containing the subdistricts map.
     */
    public function subdistricts(): ResponseInterface
    {
        $district_id = (string) $this->request->getVar('district_id');
        $filePath = WRITEPATH . 'subdistricts.json';
        if (!is_file($filePath) || !is_readable($filePath)) {
            return $this->response->setJSON(['subdistricts' => []]);
        }

        $subdistricts = json_decode(file_get_contents($filePath));
        if (!is_array($subdistricts) && !is_object($subdistricts)) {
            return $this->response->setJSON(['subdistricts' => []]);
        }

        $data = [];
        foreach ($subdistricts as $subdistrict) {
            if ((string) $subdistrict->district_id === $district_id) {
                $data[$subdistrict->subdistrict_id] = $subdistrict->subdistrict_name;
            }
        }
        ksort($data);

        return $this->response->setJSON(['subdistricts' => $data]);
    }

    /**
     * Return the applicant's profile data (users row) for the fix form.
     *
     * Resolves the linked student from the given registration id, then
     * returns the users record so the admin can correct applicant data.
     *
     * @return ResponseInterface JSON response containing the user record.
     */
    public function formStudent(): ResponseInterface
    {
        $db = Database::connect();
        $reg = $db->table('registrations')->where('id', $this->id)->get()->getRow();
        if (!$reg) {
            return $this->response->setJSON([]);
        }

        $student = new UserModel()->find((int) $reg->student_id);
        if (!$student) {
            return $this->response->setJSON([]);
        }

        $vars = (array) $student;
        unset(
            $vars['password'],
            $vars['selector'],
            $vars['hashed_validator'],
            $vars['expiry'],
            $vars['password_reset_token'],
            $vars['password_reset_token_request_at']
        );

        return $this->response->setJSON($vars);
    }

    /**
     * Save corrected applicant profile data into the users table.
     *
     * This is a shortcut for admins to fix data entered by the applicant.
     * Only fields submitted by the applicant are accepted.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function saveStudent(): ResponseInterface
    {
        $regId = (int) ($this->request->getVar('id') ?? 0);
        $db = Database::connect();
        $reg = $db->table('registrations')->where('id', $regId)->get()->getRow();
        if (!$reg) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.'
            ]);
        }

        $studentId = (int) $reg->student_id;
        $userModel = new UserModel();
        if (!$userModel->find($studentId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data calon siswa tidak ditemukan.'
            ]);
        }

        $validation = service('validation');
        $validation->setRules([
            'full_name' => ['label' => 'Nama Lengkap', 'rules' => 'required'],
            'nik' => ['label' => 'NIK', 'rules' => 'required'],
            'gender' => ['label' => 'Jenis Kelamin', 'rules' => 'required'],
        ]);

        $password = $this->request->getVar('password');
        if (is_string($password) && trim($password) !== '') {
            $validation->setRule('password', ['label' => 'Kata Sandi', 'rules' => 'required|min_length[8]|max_length[255]']);
        }

        if (!$validation->run((array) $this->request->getVar())) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $strFields = [
            'full_name', 'nik', 'npwp', 'citizenship', 'mother_name', 'kps',
            'birth_place_country_id', 'birth_place_province_id', 'birth_place_district_id',
            'birth_date',
            'address_country_id', 'address_province_id', 'address_district_id', 'address_subdistrict_id',
            'street_address', 'village', 'rt', 'rw', 'urban_village', 'post_code',
            'telephone', 'mobile_phone', 'whatsapp', 'telegram', 'facebook', 'twitter', 'instagram', 'tiktok',
            'father_nik', 'father_name', 'father_birth_date',
            'mother_nik', 'mother_name', 'mother_birth_date',
            'guardian_nik', 'guardian_name', 'guardian_birth_date',
            'nisn', 'npsn', 'school_name', 'school_address', 'school_major',
        ];

        $intFields = [
            'religion_id', 'residence_id', 'transportation_id',
            'father_education_id', 'father_occupation_id', 'father_income_id',
            'mother_education_id', 'mother_occupation_id', 'mother_income_id',
            'guardian_education_id', 'guardian_occupation_id', 'guardian_income_id',
            'school_graduation_year',
        ];

        $data = [];
        foreach ($strFields as $field) {
            $val = $this->request->getVar($field);
            if ($val !== null) {
                $data[$field] = $val;
            }
        }
        foreach ($intFields as $field) {
            $val = $this->request->getVar($field);
            if ($val !== null) {
                $data[$field] = (int) $val;
            }
        }

        $kpsReceiver = $this->request->getVar('kps_receiver');
        if ($kpsReceiver !== null) {
            $data['kps_receiver'] = (bool) $kpsReceiver;
        }

        if (isset($data['birth_place_country_id']) && $data['birth_place_country_id'] !== 'ID') {
            $data['birth_place_province_id'] = null;
            $data['birth_place_district_id'] = null;
        }

        if (is_string($password) && trim($password) !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->updateUser($studentId, $data);

        return $this->response->setJSON([
            'id' => $regId,
            'success' => true,
            'message' => 'Data calon siswa berhasil diperbaiki.',
        ]);
    }

    /**
     * Print the registration form as PDF.
     *
     * @param int $id The registration identifier.
     * @return ResponseInterface PDF download response.
     */
    public function print(int $id = 0): ResponseInterface
    {
        $db = Database::connect();
        $data = $db->table('registrations')->where('id', $id)->get()->getRow();
        if (!$data) {
            throw PageNotFoundException::forPageNotFound();
        }
        $student_id = $data->student_id;
        $admission_year = $data->admission_year;

        $registration = new RegistrationModel();
        $query = $registration->findByStudentId((int) $student_id, (int) $admission_year);
        $student = new UserModel()->find($student_id);
        $rows = $query->getResult();
        if (empty($rows)) {
            throw PageNotFoundException::forPageNotFound();
        }
        $reg = $rows[0];

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

        $binary = $pdf->render($html, $reg->registration_number, $file_name);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $file_name . '"')
            ->setBody($binary);
    }

    /**
     * Download a registrant document.
     *
     * @param string $file_name The name of the file to download.
     * @return ResponseInterface|null The download response or null if not found.
     */
    public function download(string $file_name): ?object
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
     * Update entrance exam subject score.
     *
     * @return ResponseInterface|null JSON response indicating success or failure.
     */
    public function updateSubjectScore(): ?ResponseInterface
    {
        $id = (int) ($this->request->getVar('id') ?? 0);
        $subject_id = (int) ($this->request->getVar('subject_id') ?? 0);
        $score = (int) ($this->request->getVar('score') ?? 0);

        if ($score < 0 || $score > 100) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['score' => 'Nilai harus antara 0-100.']
            ]);
        }

        $db = Database::connect();
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
            $db->table('registration_subject_grades')->insert([
                'registration_id' => $id,
                'subject_id' => $subject_id,
                'score' => $score,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'errors' => [],
        ]);
    }

    /**
     * List entrance exam subjects for a registration's offering.
     *
     * @return ResponseInterface JSON response with entrance exam subjects and existing scores.
     */
    public function listEntranceExamSubjects(): ResponseInterface
    {
        $regId = (int) ($this->request->getVar('reg_id') ?? 0);
        $db = Database::connect();
        $data = $db->table($this->table)->where('id', $regId)->get()->getRow();
        $offering_id = (int)($data->admission_offering_id ?? 0);

        $model = new EntranceExamScoreModel();
        $subjects = $model->findAllByAdmissionOffering($offering_id)->getResult();
        $existingScores = $db->table('registration_exam_scores')
            ->where('registration_id', $regId)
            ->get()
            ->getResult();

        return $this->response->setJSON([
            'subjects' => $subjects,
            'existing_scores' => $existingScores,
        ]);
    }

    /**
     * Update entrance exam score for a registrant.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function updateEntranceExamScore(): ResponseInterface
    {
        $regId = (int) ($this->request->getVar('reg_id') ?? 0);
        $subject_id = (int) ($this->request->getVar('subject_id') ?? 0);
        $score = (float) ($this->request->getVar('score') ?? 0);

        if ($score < 0 || $score > 100) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['score' => 'Nilai harus antara 0-100.']
            ]);
        }

        $db = Database::connect();
        $existing = $db->table('registration_exam_scores')
            ->where('registration_id', $regId)
            ->where('subject_id', $subject_id)
            ->get()
            ->getRow();

        if ($existing) {
            $db->table('registration_exam_scores')
                ->where('id', $existing->id)
                ->update(['score' => $score]);
        } else {
            $db->table('registration_exam_scores')->insert([
                'registration_id' => $regId,
                'subject_id' => $subject_id,
                'score' => $score,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'errors' => [],
        ]);
    }
}
