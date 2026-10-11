<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AcademicYearModel;
use App\Models\AdmissionPeriodModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class AdmissionPeriodController
 *
 * Handles the management of registration periods for admission.
 *
 * @package App\Controllers\Administrator
 */
class AdmissionPeriodController extends BaseController
{
    protected string $table = 'admission_periods';
    protected int $id = 0;
    use DataTrait;

    /**
     * AdmissionPeriodController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
    }

    /**
     * Render the admission period page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $ayModel = new AcademicYearModel();
        $academic_years = [];
        foreach ($ayModel->findAll() as $row) {
            $academic_years[$row->id] = $row->name;
        }
        $vars = [
            'title' => 'Periode Pendaftaran',
            'academic_years' => json_encode($academic_years, self::REQUIRED_FLAGS),
        ];
        return view('administrator/admission-periods', $vars);
    }

    /**
     * Return a list of admission periods in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new AdmissionPeriodModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update an admission period record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'academic_year_id' => ['label' => 'Tahun Ajaran', 'rules' => 'required|is_natural_no_zero'],
            'code' => ['label' => 'Kode', 'rules' => 'required'],
            'name' => ['label' => 'Nama Periode', 'rules' => 'required'],
            'registration_start_at' => ['label' => 'Mulai', 'rules' => 'permit_empty'],
            'registration_end_at' => ['label' => 'Akhir', 'rules' => 'permit_empty'],
            'registration_fee' => ['label' => 'Biaya Pendaftaran', 'rules' => 'permit_empty|decimal'],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new AdmissionPeriodModel();
        $actor = (int)session()->get('actor');
        $academic_year_id = (int)$this->request->getPost('academic_year_id');
        $code = (string)$this->request->getPost('code');
        $data = [
            'academic_year_id' => $academic_year_id,
            'code' => $code,
            'name' => (string)$this->request->getPost('name'),
            'registration_start_at' => (string)$this->request->getPost('registration_start_at'),
            'registration_end_at' => (string)$this->request->getPost('registration_end_at'),
            'registration_fee' => (string)$this->request->getPost('registration_fee'),
        ];
        $unique_id = 'admission_period:' . $academic_year_id . ':' . url_title(mb_strtolower($code));

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
