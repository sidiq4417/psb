<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\BranchModel;
use App\Models\EducationProgramModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class EducationProgramController
 *
 * Handles the management of education programs (majors/specializations).
 *
 * @package App\Controllers\Administrator
 */
class EducationProgramController extends BaseController
{
    protected string $table = 'education_programs';
    protected int $id = 0;
    use DataTrait;

    /**
     * EducationProgramController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
    }

    /**
     * Render the education program page.
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
            'title' => 'Program Pendidikan',
            'branches' => json_encode($branches, self::REQUIRED_FLAGS),
        ];
        return view('administrator/education-programs', $vars);
    }

    /**
     * Return a list of education programs in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new EducationProgramModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update an education program record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'branch_id' => [
                'label' => 'Cabang Sekolah',
                'rules' => 'required|is_natural_no_zero|is_not_unique[branches.id]',
                'errors' => ['is_not_unique' => 'Cabang Sekolah tidak ditemukan.'],
            ],
            'code' => ['label' => 'Kode', 'rules' => 'required|max_length[50]'],
            'name' => ['label' => 'Nama Program', 'rules' => 'required|max_length[255]'],
            'category' => ['label' => 'Kategori', 'rules' => 'required|in_list[MAJOR,SPECIALIZATION,VOCATIONAL_PROGRAM,SPECIAL_PROGRAM]'],
            'display_label' => ['label' => 'Label Tampilan', 'rules' => 'permit_empty|max_length[100]'],
            'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[5000]'],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new EducationProgramModel();
        $actor = (int)session()->get('actor');
        $branch_id = (int)$this->request->getPost('branch_id');
        $code = (string)$this->request->getPost('code');
        $data = [
            'branch_id' => $branch_id,
            'code' => $code,
            'name' => (string)$this->request->getPost('name'),
            'category' => (string)$this->request->getPost('category'),
            'display_label' => (string)$this->request->getPost('display_label'),
            'description' => (string)$this->request->getPost('description'),
        ];
        $unique_id = 'education_program:' . $branch_id . ':' . url_title(mb_strtolower($code));

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
