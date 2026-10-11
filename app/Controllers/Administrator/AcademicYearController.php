<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\AcademicYearModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class AcademicYearController
 *
 * Handles the management of academic years.
 *
 * @package App\Controllers\Administrator
 */
class AcademicYearController extends BaseController
{
    protected string $table = 'academic_years';
    protected int $id = 0;
    protected AcademicYearModel $model;

    /**
     * AcademicYearController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
        $this->model = new AcademicYearModel();
    }

    /**
     * Render the academic year management page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $vars = [
            'title' => 'Tahun Ajaran',
        ];
        return view('administrator/academic-years', $vars);
    }

    /**
     * Return a list of academic years in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $records = $this->model->findAll();
        return $this->response->setJSON([
            'records' => $records
        ]);
    }

    /**
     * Return the form data for an academic year record.
     *
     * @return ResponseInterface JSON response containing record data.
     */
    public function form(): ResponseInterface
    {
        $vars = ['id' => 0, 'unique_id' => '', 'is_deleted' => 0];

        if ($this->id > 0) {
            $row = $this->model->find($this->id);
            if ($row) {
                $vars = (array)$row;
                $vars['is_deleted'] = (int)($vars['is_deleted'] ?? 0);
            }
        }

        return $this->response->setJSON($vars);
    }

    /**
     * Save or update an academic year record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'name' => ['label' => 'Tahun Ajaran', 'rules' => 'required'],
            'start_date' => ['label' => 'Tanggal Mulai', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
            'end_date' => ['label' => 'Tanggal Akhir', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $name = $this->request->getVar('name');
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $actor = (int)session()->get('actor');

        if ($id > 0) {
            $this->model->update($id, [
                'name' => $name,
                'start_date' => $start_date ?: null,
                'end_date' => $end_date ?: null,
                'updated_by' => $actor,
            ]);
            return $this->response->setJSON([
                'id' => $id,
                'success' => true,
                'message' => 'Updated successfully.',
            ]);
        }

        $unique_id = 'academic_year:' . url_title(mb_strtolower($name));
        $existing = $this->model->where('unique_id', $unique_id)->first();
        if ($existing) {
            return $this->response->setJSON([
                'id' => 0,
                'success' => false,
                'message' => 'Unique identifier already exists.',
            ]);
        }

        $insert_id = $this->model->insert([
            'unique_id' => $unique_id,
            'name' => $name,
            'start_date' => $start_date ?: null,
            'end_date' => $end_date ?: null,
            'created_by' => $actor,
        ]);

        $success = $insert_id !== false;

        return $this->response->setJSON([
            'id' => $success ? (int)$insert_id : 0,
            'success' => $success,
            'message' => $success ? 'Saved successfully.' : 'Error occurred.',
            'errors' => $success ? null : $this->model->errors(),
        ]);
    }

    /**
     * Soft-delete an academic year record.
     *
     * @return ResponseInterface JSON response indicating success.
     * @throws ReflectionException
     */
    public function delete(): ResponseInterface
    {
        $actor = (int)session()->get('actor');
        $this->model->update($this->id, [
            'is_deleted' => 1,
            'deleted_by' => $actor,
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Data has been marked as deleted.',
        ]);
    }

    /**
     * Restore a previously soft-deleted academic year record.
     *
     * @return ResponseInterface JSON response indicating success.
     * @throws ReflectionException
     */
    public function restore(): ResponseInterface
    {
        $this->model->update($this->id, [
            'is_deleted' => 0,
            'deleted_by' => 0,
            'deleted_at' => null,
        ]);
        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Data has been restored successfully.',
        ]);
    }
}
