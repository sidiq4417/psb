<?php

declare(strict_types=1);

namespace App\Controllers\Administrator;

use App\Models\EducationTrackModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

/**
 * Class EducationTrackController
 *
 * Handles the management of education tracks (class types).
 *
 * @package App\Controllers\Administrator
 */
class EducationTrackController extends BaseController
{
    protected string $table = 'education_tracks';
    protected int $id = 0;
    use DataTrait;

    /**
     * EducationTrackController Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = (int)(service('request')->getVar('id') ?? 0);
    }

    /**
     * Render the education track page.
     *
     * @return string Rendered HTML view.
     */
    public function index(): string
    {
        $vars = [
            'title' => 'Jenis Kelas',
        ];
        return view('administrator/education-tracks', $vars);
    }

    /**
     * Return a list of education tracks in JSON format.
     *
     * @return ResponseInterface JSON response containing records.
     */
    public function lists(): ResponseInterface
    {
        $model = new EducationTrackModel();
        return $this->response->setJSON([
            'records' => $model->findAll()
        ]);
    }

    /**
     * Save or update an education track record.
     *
     * @return ResponseInterface JSON response indicating success or failure.
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $rules = [
            'code' => ['label' => 'Kode', 'rules' => 'required'],
            'name' => ['label' => 'Nama Jenis Kelas', 'rules' => 'required'],
            'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty'],
            'is_default' => ['label' => 'Default', 'rules' => 'permit_empty|in_list[true,false,1,0]'],
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $id = (int)($this->request->getVar('id') ?? 0);
        $model = new EducationTrackModel();
        $actor = (int)session()->get('actor');
        $data = [
            'code' => (string)$this->request->getPost('code'),
            'name' => (string)$this->request->getPost('name'),
            'description' => (string)$this->request->getPost('description'),
            'is_default' => (string)$this->request->getPost('is_default'),
        ];
        $unique_id = 'education_track:' . url_title(mb_strtolower($data['code']));

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
