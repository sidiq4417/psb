<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AcademicYearModel;
use App\Models\AdmissionOfferingModel;
use App\Models\AdmissionPathwayModel;
use App\Models\AdmissionPeriodModel;
use App\Models\ApplicantTypeModel;
use App\Models\BranchModel;
use App\Models\EducationProgramModel;
use App\Models\EducationTrackModel;
use App\Models\EntryGradeModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class AdmissionOfferingController
 *
 * Handles the management of admission offerings (opened registrations).
 *
 * @package App\Controllers\Administrator
 */
class AdmissionOfferingController extends BaseController
{
    protected string $table = 'admission_offerings';
    protected int $id = 0;
    use DataTrait;

    /**
     * AdmissionOfferingController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
    }

    /**
     * Render the admission offering page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $branchModel = new BranchModel();
        $periodModel = new AdmissionPeriodModel();

        $branchSchoolTypes = [];
        $schools = [];
        foreach ($branchModel->findAll() as $branch) {
            $branchSchoolTypes[$branch->id] = $branch->education_level ?? '';
            $schools[$branch->id] = $branch->name;
        }

        $admission_periods = [];
        $period_academic_year_map = [];
        foreach ($periodModel->findAll() as $p) {
            $admission_periods[$p->id] = $p->name;
            $period_academic_year_map[$p->id] = (int)$p->academic_year_id;
        }

        $egQuery = db_connect()->table('entry_grades')
            ->select('entry_grades.id, entry_grades.name AS grade_name, entry_grades.branch_id, branches.name AS school_name')
            ->join('branches', 'entry_grades.branch_id = branches.id', 'left')
            ->where('entry_grades.is_deleted', false)
            ->get()
            ->getResult();
        $entry_grades_labeled = [];
        $entry_grade_branch_map = [];
        foreach ($egQuery as $eg) {
            $entry_grades_labeled[$eg->id] = ($eg->school_name ?? '?') . ' - ' . $eg->grade_name;
            $entry_grade_branch_map[$eg->id] = (int)$eg->branch_id;
        }

        $ayModel = new AcademicYearModel();
        $etModel = new EducationTrackModel();
        $epModel = new EducationProgramModel();
        $atModel = new ApplicantTypeModel();
        $pwModel = new AdmissionPathwayModel();

        $academic_years = [];
        foreach ($ayModel->findAll() as $row) {
            $academic_years[$row->id] = $row->name;
        }

        $education_tracks = [];
        foreach ($etModel->findAll() as $row) {
            $education_tracks[$row->id] = $row->name;
        }

        $education_programs = [];
        foreach ($epModel->findAll() as $row) {
            $education_programs[$row->id] = $row->name;
        }

        $applicant_types = [];
        foreach ($atModel->findAll() as $row) {
            $applicant_types[$row->id] = $row->name;
        }

        $admission_pathways = [];
        foreach ($pwModel->findAll() as $row) {
            $admission_pathways[$row->id] = $row->name;
        }

        $vars = [
            'title' => 'Pembukaan Pendaftaran',
            'academic_years' => $academic_years,
            'admission_periods' => $admission_periods,
            'period_academic_year_map' => $period_academic_year_map,
            'schools' => $schools,
            'entry_grades' => $entry_grades_labeled,
            'education_tracks' => $education_tracks,
            'education_programs' => $education_programs,
            'applicant_types' => $applicant_types,
            'admission_pathways' => $admission_pathways,
            'branchSchoolTypes' => $branchSchoolTypes,
            'entryGradeSchoolMap' => $entry_grade_branch_map,
        ];
        return view('administrator/admission-offerings', $vars);
    }

    /**
     * Return a list of admission offerings in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new AdmissionOfferingModel();
        $academic_year_id = (int)($this->request->getPost('academic_year_id') ?? 0);
        $admission_period_id = (int)($this->request->getPost('admission_period_id') ?? 0);
        $branch_id = (int)($this->request->getPost('branch_id') ?? 0);
        return $this->response->setJSON([
            'records' => $model->findAllWithDetails($academic_year_id, $admission_period_id, $branch_id)->getResult()
        ]);
    }

    /**
     * Save or update an admission offering record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $validation = service('validation');

        $exists = static fn(string $label, string $table): array => [
            'label' => $label,
            'rules' => 'required|is_natural_no_zero|is_not_unique[' . $table . '.id]',
            'errors' => ['is_not_unique' => $label . ' tidak ditemukan.'],
        ];
        $validation->setRules([
            'admission_period_id' => $exists('Periode', 'admission_periods'),
            'entry_grade_id' => $exists('Tingkat Masuk', 'entry_grades'),
            'education_track_id' => $exists('Jenis Kelas', 'education_tracks'),
            'education_program_id' => ['label' => 'Program Pendidikan', 'rules' => 'permit_empty|is_natural'],
            'applicant_type_id' => $exists('Jenis Pendaftaran', 'applicant_types'),
            'admission_pathway_id' => $exists('Jalur Masuk', 'admission_pathways'),
            'quota' => ['label' => 'Kuota', 'rules' => 'required|is_natural|less_than_equal_to[1000000]'],
            'sort_order' => ['label' => 'Urutan', 'rules' => 'permit_empty|is_natural|less_than_equal_to[1000000]'],
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $id = (int)($this->request->getPost('id') ?? 0);
        $model = new AdmissionOfferingModel();
        $actor = (int)session()->get('actor');

        $entry_grade_id = (int)$this->request->getPost('entry_grade_id');
        $entryGradeModel = new EntryGradeModel();
        $entryGrade = $entryGradeModel->find($entry_grade_id);
        $derived_branch_id = (int)($entryGrade->branch_id ?? 0);
        if ($derived_branch_id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['entry_grade_id' => 'Tingkat Masuk belum terhubung dengan sekolah.'],
            ]);
        }

        $education_program_id = (int)($this->request->getPost('education_program_id') ?? 0);
        if ($education_program_id > 0 && new EducationProgramModel()->find($education_program_id) === null) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['education_program_id' => 'Program Pendidikan tidak ditemukan.'],
            ]);
        }

        $admission_period_id = (int)$this->request->getPost('admission_period_id');
        $data = [
            'admission_period_id' => $admission_period_id,
            'branch_id' => $derived_branch_id,
            'entry_grade_id' => $entry_grade_id,
            'education_track_id' => (int)$this->request->getPost('education_track_id'),
            'education_program_id' => $education_program_id,
            'applicant_type_id' => (int)$this->request->getPost('applicant_type_id'),
            'admission_pathway_id' => (int)$this->request->getPost('admission_pathway_id'),
            'quota' => (int)$this->request->getPost('quota'),
            'sort_order' => (int)$this->request->getPost('sort_order'),
        ];
        $unique_id = 'admission_offering:' . $admission_period_id . ':' . $derived_branch_id . ':' . $entry_grade_id . ':' . $data['education_track_id'] . ':' . $data['education_program_id'] . ':' . $data['applicant_type_id'] . ':' . $data['admission_pathway_id'];

        if ($id > 0) {
            $existing = $model->where('unique_id', $unique_id)->where('id !=', $id)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'id' => 0,
                    'success' => false,
                    'message' => 'Unique identifier collision.',
                ]);
            }

            $data['unique_id'] = $unique_id;
            $data['updated_by'] = $actor;
            $model->update($id, $data);
            return $this->response->setJSON([
                'id' => $id,
                'success' => true,
                'message' => 'Updated successfully.',
            ]);
        }

        $existing = $model->where('unique_id', $unique_id)->first();
        if ($existing) {
            return $this->response->setJSON([
                'id' => 0,
                'success' => false,
                'message' => 'Unique identifier already exists.',
            ]);
        }

        $data['unique_id'] = $unique_id;
        $data['created_by'] = $actor;
        $insertId = $model->insert($data);
        return $this->response->setJSON([
            'id' => $insertId ? (int)$insertId : 0,
            'success' => (bool)$insertId,
            'message' => $insertId ? 'Saved successfully.' : 'Error occurred.',
        ]);
    }
}
