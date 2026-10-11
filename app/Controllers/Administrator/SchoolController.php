<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\SchoolModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class SchoolController
 *
 * Handles the management of schools and universities of origin for applicants.
 *
 * @package App\Controllers\Administrator
 */
class SchoolController extends BaseController
{
    /**
     * Database table name used by QBase.
     *
     * @var string
     */
    protected string $table = 'previous_schools';

    /**
     * Primary key identifier for the current school.
     *
     * @var int
     */
    protected int $id = 0;

    use DataTrait;

    /**
     * SchoolController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
    }

    /**
     * Render the school page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $vars = [
            'title' => 'Asal Sekolah',
        ];

        return view('administrator/schools', $vars);
    }

    /**
     * Return a list of schools in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new SchoolModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update a school record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $validation = service('validation');

        $validation->setRules([
            'school_name' => [
                'label' => 'School/University Name',
                'rules' => 'required',
            ],
            'school_address' => [
                'label' => 'Address',
                'rules' => 'required',
            ],
        ]);

        // Defensive: getJsonVar returns single value, not array. Use getJSON(true).
        if (!$validation->run($this->request->getVar())) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new SchoolModel();
        $actor = (int)session()->get('actor');
        $school_name = (string)$this->request->getPost('school_name');
        $school_address = (string)$this->request->getPost('school_address');
        $unique_id = 'school:' . url_title(mb_strtolower($school_name));

        if ($id > 0) {
            $existing = $model->where('unique_id', $unique_id)->where('id !=', $id)->first();
            if ($existing) {
                return $this->response->setJSON([
                    'id' => 0,
                    'success' => false,
                    'message' => 'Unique identifier collision.',
                ]);
            }

            $model->update($id, [
                'school_name' => $school_name,
                'school_address' => $school_address,
                'unique_id' => $unique_id,
                'updated_by' => $actor,
            ]);
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

        $insertId = $model->insert([
            'school_name' => $school_name,
            'school_address' => $school_address,
            'unique_id' => $unique_id,
            'created_by' => $actor,
        ]);
        return $this->response->setJSON([
            'id' => $insertId ? (int)$insertId : 0,
            'success' => (bool)$insertId,
            'message' => $insertId ? 'Saved successfully.' : 'Error occurred.',
        ]);
    }
}
