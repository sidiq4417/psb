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
            // The admission year is taken from the first four characters of the name (LEFT(name, 4)).
            'name' => [
                'label' => 'Tahun Ajaran',
                'rules' => ['required', 'max_length[20]', 'regex_match[/^\s*\d{4}\/\d{4}\s*$/]'],
                'errors' => ['regex_match' => 'Format Tahun Ajaran harus TTTT/TTTT, contoh: 2026/2027.'],
            ],
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
        $name = trim((string)$this->request->getVar('name'));
        $start_date = (string)$this->request->getVar('start_date');
        $end_date = (string)$this->request->getVar('end_date');
        $actor = (int)session()->get('actor');

        if ($start_date !== '' && $end_date !== '' && $end_date < $start_date) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['end_date' => 'Tanggal Akhir tidak boleh sebelum Tanggal Mulai.'],
            ]);
        }

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
                'message' => 'Data berhasil diperbarui.',
            ]);
        }

        $unique_id = 'academic_year:' . url_title(mb_strtolower($name));
        $existing = $this->model->where('unique_id', $unique_id)->first();
        if ($existing) {
            return $this->response->setJSON([
                'id' => 0,
                'success' => false,
                'message' => 'Data dengan kombinasi ini sudah ada.',
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
            'message' => $success ? 'Data berhasil disimpan.' : 'Terjadi kesalahan saat menyimpan data.',
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
            'message' => 'Data berhasil dihapus.',
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
            'message' => 'Data berhasil dipulihkan.',
        ]);
    }
}
