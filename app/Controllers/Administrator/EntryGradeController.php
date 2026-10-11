<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\BranchModel;
use App\Models\EntryGradeModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class EntryGradeController
 *
 * Handles the management of entry grades for admission.
 *
 * @package App\Controllers\Administrator
 */
class EntryGradeController extends BaseController
{
    protected string $table = 'entry_grades';
    protected int $id = 0;
    use DataTrait;

    /**
     * EntryGradeController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
    }

    /**
     * Render the entry grade page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $branchModel = new BranchModel();
        $branches = [];
        foreach ($branchModel->findAll() as $row) {
            $branches[$row->id] = $row->name;
        }
        $vars = [
            'title' => 'Tingkat Masuk',
            'branches' => json_encode($branches, self::REQUIRED_FLAGS),
        ];
        return view('administrator/entry-grades', $vars);
    }

    /**
     * Return a list of entry grades in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new EntryGradeModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update an entry grade record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'branch_id' => ['label' => 'Cabang Sekolah', 'rules' => 'required|is_natural_no_zero'],
            'name' => ['label' => 'Nama Tingkat', 'rules' => 'required'],
            'grade_number' => ['label' => 'Nomor Urut Kelas', 'rules' => 'permit_empty|is_natural'],
            'sort_order' => ['label' => 'Urutan', 'rules' => 'permit_empty|is_natural'],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new EntryGradeModel();
        $actor = (int)session()->get('actor');
        $branch_id = (int)$this->request->getPost('branch_id');
        $name = (string)$this->request->getPost('name');
        $data = [
            'branch_id' => $branch_id,
            'name' => $name,
            'grade_number' => (int)$this->request->getPost('grade_number'),
            'sort_order' => (int)$this->request->getPost('sort_order'),
        ];
        $unique_id = 'entry_grade:' . $branch_id . ':' . url_title(mb_strtolower($name));

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
