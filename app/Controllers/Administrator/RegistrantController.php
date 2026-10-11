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
use App\Libraries\Remember;
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
            $grades_rows = $db->table('registration_subject_grades g')
                ->select('g.registration_id, g.subject_id, g.score, g.threshold_score, s.name AS subject_name')
                ->join('subjects s', 's.id = g.subject_id', 'left')
                ->whereIn('g.registration_id', $ids)
                ->get()
                ->getResult();
            foreach ($grades_rows as $g) {
                $g->score = $g->score !== null ? (float)$g->score : null;
                $g->threshold_score = $g->threshold_score !== null ? (float)$g->threshold_score : null;
                $grade_map[(int)$g->registration_id][] = $g;
            }
        }

        $doc_map = [];
        $transfer_proofs = [];
        if (!empty($ids)) {
            $doc_rows = $db->table('registration_documents d')
                ->select('d.registration_id, d.document_id, d.file AS file_name, d.verified, a.name AS document_name')
                ->join('admission_documents a', 'a.id = d.document_id', 'left')
                ->whereIn('d.registration_id', $ids)
                ->get()
                ->getResult();
            foreach ($doc_rows as $d) {
                $doc_map[(int)$d->registration_id][] = $d;
            }

            $proof_rows = $db->table('registrations')
                ->select('id, transfer_proof')
                ->whereIn('id', $ids)
                ->get()
                ->getResult();
            foreach ($proof_rows as $p) {
                $transfer_proofs[(int)$p->id] = basename((string)($p->transfer_proof ?? ''));
            }
        }

        foreach ($records as &$row) {
            $row->submitted = (bool)$row->submitted;
            $row->transfer_proof_verified = (bool)$row->transfer_proof_verified;
            $row->transfer_proof = $transfer_proofs[(int)$row->id] ?? '';
            $row->subject_grade_requirements = json_encode($grade_map[(int)$row->id] ?? []);
            $row->document_requirements = json_encode($doc_map[(int)$row->id] ?? []);
        }
        unset($row);

        return $this->response->setJSON([
            'records' => $records
        ]);
    }


    /**
     * Save or update the registrant verification status.
     *
     * The confirmation and verification dates are not taken from the form: they are
     * stamped when the matching checkbox is switched on and cleared when it is
     * switched off, so the stored time of day is never reset to midnight.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     */
    public function save(): ResponseInterface
    {
        $id = (int) ($this->request->getVar('id') ?? 0);
        $db = Database::connect();
        $registration = $id > 0
            ? $db->table('registrations')
                ->select('id, submitted, submitted_at, transfer_proof_verified, transfer_proof_verified_at, registration_number')
                ->where('id', $id)
                ->get()
                ->getRow()
            : null;
        if (!$registration) {
            return $this->response->setJSON([
                'id' => $id,
                'success' => false,
                'message' => 'Data pendaftaran tidak ditemukan.',
            ]);
        }

        $statuses = implode(',', array_map('strval', array_keys(registration_status())));
        $validation = service('validation');
        $validation->setRules([
            'selection_status' => [
                'label' => 'Status Seleksi',
                'rules' => 'permit_empty|in_list[' . $statuses . ']',
                'errors' => ['in_list' => 'Status seleksi tidak valid.'],
            ],
            'selection_notes' => ['label' => 'Catatan Seleksi', 'rules' => 'permit_empty|max_length[5000]'],
        ]);

        $input = [
            'selection_status' => (string)$this->request->getPost('selection_status'),
            'selection_notes' => (string)$this->request->getPost('selection_notes'),
        ];
        if (!$validation->run($input)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        // FormBuilder posts checkbox values as "true"/"false".
        $submitted = filter_var($this->request->getPost('submitted'), FILTER_VALIDATE_BOOLEAN);
        // Only the applicant's own final submission checks the grades and documents and assigns
        // the registration number, so a registration that never had one cannot be confirmed here.
        if ($submitted && (int) $registration->submitted !== 1 && trim((string) ($registration->registration_number ?? '')) === '') {
            return $this->response->setJSON([
                'id' => $id,
                'success' => false,
                'message' => 'Pendaftaran ini belum pernah dikirim final oleh calon siswa sehingga belum memiliki No. Registrasi. Konfirmasi Pendaftaran tidak dapat diaktifkan.',
            ]);
        }
        $updateData = registration_verification_changes(
            $registration,
            $submitted,
            filter_var($this->request->getPost('transfer_proof_verified'), FILTER_VALIDATE_BOOLEAN)
        );
        $updateData['updated_by'] = (int)session()->get('actor');

        // Selection fields are hidden (and not posted) until the registration is confirmed.
        if ($this->request->getPost('selection_status') !== null) {
            $updateData['selection_status'] = $input['selection_status'] !== '' ? $input['selection_status'] : '0';
        }
        if ($this->request->getPost('selection_notes') !== null) {
            $updateData['selection_notes'] = $input['selection_notes'];
        }

        $db->table('registrations')->where('id', $id)->update($updateData);

        return $this->response->setJSON([
            'id' => $id,
            'success' => true,
            'message' => 'Data pendaftaran berhasil diperbarui.',
        ]);
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
        $student = $userModel->find($studentId);
        if (!$student) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data calon siswa tidak ditemukan.'
            ]);
        }

        $in_list = static fn(array $items): string => 'permit_empty|in_list[' . implode(',', array_map('strval', array_keys($items))) . ']';
        $date_rule = ['rules' => 'permit_empty|valid_date[Y-m-d]'];

        $validation = service('validation');
        $validation->setRules([
            'full_name' => UserModel::nameRule('Nama Lengkap'),
            'nik' => [
                'label' => 'NIK / No. Paspor',
                'rules' => ['required', 'regex_match[/^[A-Za-z0-9]{5,20}$/]'],
                'errors' => ['regex_match' => 'NIK harus 16 digit angka, atau No. Paspor 5-20 karakter huruf/angka.'],
            ],
            'npwp' => ['label' => 'NPWP', 'rules' => 'permit_empty|max_length[30]'],
            'gender' => ['label' => 'Jenis Kelamin', 'rules' => 'required|in_list[' . implode(',', array_keys(gender())) . ']'],
            'citizenship' => ['label' => 'Kewarganegaraan', 'rules' => $in_list(countries())],
            'religion_id' => ['label' => 'Agama', 'rules' => $in_list(religions())],
            'kps_receiver' => ['label' => 'Penerima KPS', 'rules' => 'permit_empty|in_list[0,1]'],
            'kps' => ['label' => 'Nomor KPS', 'rules' => 'permit_empty|max_length[50]'],
            'birth_date' => ['label' => 'Tanggal Lahir'] + $date_rule,
            'father_birth_date' => ['label' => 'Tanggal Lahir Ayah'] + $date_rule,
            'mother_birth_date' => ['label' => 'Tanggal Lahir Ibu'] + $date_rule,
            'guardian_birth_date' => ['label' => 'Tanggal Lahir Wali'] + $date_rule,
            'father_name' => UserModel::nameRule('Nama Ayah', false),
            'mother_name' => UserModel::nameRule('Nama Ibu Kandung', false),
            'guardian_name' => UserModel::nameRule('Nama Wali', false),
            'father_nik' => ['label' => 'NIK Ayah', 'rules' => 'permit_empty|regex_match[/^[0-9]{16}$/]', 'errors' => ['regex_match' => 'NIK Ayah harus terdiri dari 16 digit angka.']],
            'mother_nik' => ['label' => 'NIK Ibu', 'rules' => 'permit_empty|regex_match[/^[0-9]{16}$/]', 'errors' => ['regex_match' => 'NIK Ibu harus terdiri dari 16 digit angka.']],
            'guardian_nik' => ['label' => 'NIK Wali', 'rules' => 'permit_empty|regex_match[/^[0-9]{16}$/]', 'errors' => ['regex_match' => 'NIK Wali harus terdiri dari 16 digit angka.']],
            'residence_id' => ['label' => 'Jenis Tinggal', 'rules' => $in_list(residences())],
            'transportation_id' => ['label' => 'Alat Transportasi', 'rules' => $in_list(transportations())],
            'father_education_id' => ['label' => 'Pendidikan Ayah', 'rules' => $in_list(educations())],
            'mother_education_id' => ['label' => 'Pendidikan Ibu', 'rules' => $in_list(educations())],
            'guardian_education_id' => ['label' => 'Pendidikan Wali', 'rules' => $in_list(educations())],
            'father_occupation_id' => ['label' => 'Pekerjaan Ayah', 'rules' => $in_list(occupations())],
            'mother_occupation_id' => ['label' => 'Pekerjaan Ibu', 'rules' => $in_list(occupations())],
            'guardian_occupation_id' => ['label' => 'Pekerjaan Wali', 'rules' => $in_list(occupations())],
            'father_income_id' => ['label' => 'Penghasilan Ayah', 'rules' => $in_list(incomes())],
            'mother_income_id' => ['label' => 'Penghasilan Ibu', 'rules' => $in_list(incomes())],
            'guardian_income_id' => ['label' => 'Penghasilan Wali', 'rules' => $in_list(incomes())],
            'rt' => ['label' => 'RT', 'rules' => 'permit_empty|regex_match[/^[0-9]{1,3}$/]'],
            'rw' => ['label' => 'RW', 'rules' => 'permit_empty|regex_match[/^[0-9]{1,3}$/]'],
            'post_code' => ['label' => 'Kode Pos', 'rules' => 'permit_empty|regex_match[/^[0-9]{5}$/]'],
            'nisn' => ['label' => 'NISN', 'rules' => 'permit_empty|max_length[50]'],
            'npsn' => ['label' => 'NPSN', 'rules' => 'permit_empty|max_length[50]'],
            'school_graduation_year' => ['label' => 'Tahun Lulus', 'rules' => 'permit_empty|regex_match[/^[0-9]{4}$/]'],
        ]);

        // Birth place: like the applicant's profile page, the readable birth_place is derived
        // from the chosen district (or the free text / country name outside Indonesia).
        $birth = null;
        if (is_scalar($this->request->getVar('birth_place_country_id'))) {
            $birth_place = $this->request->getVar('birth_place');
            $birth = resolve_birth_place([
                'birth_place_country_id' => $this->request->getVar('birth_place_country_id'),
                'birth_place_province_id' => $this->request->getVar('birth_place_province_id'),
                'birth_place_district_id' => $this->request->getVar('birth_place_district_id'),
                'birth_place' => is_scalar($birth_place) ? $birth_place : ($student->birth_place ?? ''),
            ]);
            // The free text is only stored when no district was chosen (district names such as
            // "Sawahlunto/ Sijunjung" need not follow the free-text rule).
            if ($birth['birth_place_district_id'] === null) {
                $validation->setRule('birth_place', 'Tempat Lahir', ['permit_empty', 'max_length[100]', 'regex_match[/^[\p{L}\p{M}\s.,\'`()-]+$/u]'], [
                    'max_length' => 'Tempat Lahir maksimal 100 karakter.',
                    'regex_match' => 'Tempat Lahir hanya boleh berisi huruf, spasi, dan tanda baca sederhana.',
                ]);
            }
        }

        $password = $this->request->getVar('password');
        if (is_string($password) && trim($password) !== '') {
            $validation->setRule('password', 'Kata Sandi', 'required|min_length[8]|max_length[255]');
        }

        if (!$validation->run((array) $this->request->getVar())) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $strFields = [
            'full_name', 'nik', 'npwp', 'citizenship', 'kps',
            'birth_date',
            'address_country_id', 'address_province_id', 'address_district_id', 'address_subdistrict_id',
            'street_address', 'village', 'rt', 'rw', 'urban_village', 'post_code',
            'telephone', 'mobile_phone', 'whatsapp', 'telegram', 'facebook', 'twitter', 'instagram', 'tiktok',
            'father_nik', 'father_name', 'father_birth_date',
            'mother_nik', 'mother_name', 'mother_birth_date',
            'guardian_nik', 'guardian_name', 'guardian_birth_date',
            'nisn', 'npsn', 'school_name', 'school_address', 'school_major', 'school_graduation_year',
        ];

        $intFields = [
            'religion_id', 'residence_id', 'transportation_id',
            'father_education_id', 'father_occupation_id', 'father_income_id',
            'mother_education_id', 'mother_occupation_id', 'mother_income_id',
            'guardian_education_id', 'guardian_occupation_id', 'guardian_income_id',
        ];

        $dateFields = ['birth_date', 'father_birth_date', 'mother_birth_date', 'guardian_birth_date'];

        $data = [];
        foreach ($strFields as $field) {
            $val = $this->request->getVar($field);
            if (is_scalar($val)) {
                $val = trim((string) $val);
                $data[$field] = (in_array($field, $dateFields, true) || $field === 'school_graduation_year') && $val === '' ? null : $val;
            }
        }
        foreach ($intFields as $field) {
            $val = $this->request->getVar($field);
            if (is_scalar($val)) {
                $data[$field] = trim((string) $val) === '' ? null : (int) $val;
            }
        }

        $kpsReceiver = $this->request->getVar('kps_receiver');
        if (is_scalar($kpsReceiver)) {
            $data['kps_receiver'] = (int) $kpsReceiver === 1 ? 1 : 0;
        }

        if ($birth !== null) {
            $data = array_merge($data, $birth);
        }

        if (is_string($password) && trim($password) !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->updateUser($studentId, $data);
        if (isset($data['password'])) {
            // A new password also revokes the applicant's remember-me token, so an old cookie stops working.
            new Remember()->revokeUserToken($studentId);
        }

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
        $shared_number = (string) ($reg->registration_number ?? '-');

        $html = view('students/registration-form', [
            'student' => $student,
            'registration' => $reg,
            'details' => $details->getResult(),
            'applicant_types' => applicant_types(),
            'admission_pathways' => admission_pathways(),
            'educations' => educations(),
            'app_settings' => $app_settings,
            'shared_number' => $shared_number,
        ]);

        $pdf = new PDF();
        $file_name = 'FORMULIR-PENDAFTARAN';
        $file_name .= '-' . $shared_number . '-' . time() . '.pdf';

        $binary = $pdf->render($html, $shared_number, $file_name);

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
    public function download(string $file_name = ''): ResponseInterface
    {
        $file_path = find_registration_file($file_name);
        if ($file_path === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        return $this->response->download($file_path, null)->setFileName(basename($file_path));
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
        $score = self::parseScore($this->request->getVar('score'), 2);

        if ($score === null) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['score' => 'Nilai harus berupa angka 0-100 (maksimal 2 angka desimal).']
            ]);
        }

        $db = Database::connect();
        if (!$this->registrationAndSubjectExist($id, $subject_id)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['score' => 'Data pendaftaran atau mata pelajaran tidak ditemukan.']
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
        // registration_exam_scores.score is an integer column.
        $score = self::parseScore($this->request->getVar('score'), 0);

        if ($score === null) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['score' => 'Nilai ujian harus berupa bilangan bulat 0-100.']
            ]);
        }
        $score = (int) $score;

        $db = Database::connect();
        if (!$this->registrationAndSubjectExist($regId, $subject_id)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['score' => 'Data pendaftaran atau mata ujian tidak ditemukan.']
            ]);
        }

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

    /**
     * Parse a score between 0 and 100.
     *
     * @param mixed $value    The submitted value.
     * @param int   $decimals Maximum number of decimal places allowed.
     * @return float|null The score, or null when it is missing, malformed or out of range.
     */
    private static function parseScore(mixed $value, int $decimals): ?float
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = str_replace(',', '.', trim((string) $value));
        $pattern = $decimals > 0 ? '/^[0-9]{1,3}(\.[0-9]{1,' . $decimals . '})?$/' : '/^[0-9]{1,3}$/';
        if (preg_match($pattern, $value) !== 1) {
            return null;
        }

        $score = (float) $value;

        return ($score >= 0 && $score <= 100) ? round($score, $decimals) : null;
    }

    /**
     * Check that both the registration and the subject exist.
     *
     * @param int $registration_id The registration ID.
     * @param int $subject_id      The subject ID.
     * @return bool
     */
    private function registrationAndSubjectExist(int $registration_id, int $subject_id): bool
    {
        if ($registration_id <= 0 || $subject_id <= 0) {
            return false;
        }

        $db = Database::connect();

        return $db->table('registrations')->where('id', $registration_id)->countAllResults() > 0
            && $db->table('subjects')->where('id', $subject_id)->countAllResults() > 0;
    }
}
