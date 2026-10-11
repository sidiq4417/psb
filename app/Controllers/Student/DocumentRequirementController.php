<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\AdmissionOfferingModel;
use App\Models\RegistrationDocumentModel;
use App\Models\RegistrationModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;
use Throwable;

class DocumentRequirementController extends BaseController
{
    protected int $actor = 0;

    use DataTrait;

    public function __construct()
    {
        $this->actor = session()->get('actor') ?? 0;
    }

    public function index(): string
    {
        $vars['title'] = 'Persyaratan Dokumen';
        $vars['breadcrumb'] = 'Persyaratan Dokumen';
        return view('students/document-requirements', $vars);
    }

    public function lists(): ResponseInterface
    {
        $registrationModel = new RegistrationModel();
        $registrations = $registrationModel->findActiveByStudent((int)session()->get('actor'));
        if (empty($registrations)) {
            return $this->response->setJSON(['records' => []]);
        }

        $offering_ids = [];
        $all_submitted = true;
        foreach ($registrations as $reg) {
            $oid = (int)($reg->admission_offering_id ?? 0);
            if ($oid > 0) $offering_ids[] = $oid;
            if ((int)($reg->submitted ?? 0) !== 1) $all_submitted = false;
        }

        $docModel = new RegistrationDocumentModel();
        $unique_document_requirements = $docModel->findRequirementList($offering_ids);

        $reg_ids = array_map(fn($r) => (int)$r->id, $registrations);
        $latest_files = [];
        foreach ($docModel->findByRegIds($reg_ids) as $ex) {
            $file = trim((string)($ex->file ?? ''));
            if ($file === '') continue;
            $document_id = (int)$ex->document_id;
            $current = $latest_files[$document_id] ?? null;
            if ($current === null || (string)($ex->uploaded_at ?? '') > (string)($current->uploaded_at ?? '')) {
                $latest_files[$document_id] = $ex;
            }
        }

        foreach ($unique_document_requirements as &$item) {
            if (isset($latest_files[$item['document_id']])) {
                $item['file_name'] = $latest_files[$item['document_id']]->file;
            }
        }
        unset($item);

        $res['records'] = array_map(fn($r) => array_merge($r, ['submitted' => $all_submitted]), $unique_document_requirements);
        return $this->response->setJSON($res);
    }

    /**
     * Upload (or replace) a required document for all of the student's
     * unsubmitted registrations whose admission period is still open.
     *
     * @throws ReflectionException
     */
    public function upload(): ResponseInterface
    {
        $document_id = (int)$this->request->getPost('document_id');
        $registrationModel = new RegistrationModel();
        $registrations = $registrationModel->findActiveByStudent((int)session()->get('actor'));
        if (empty($registrations)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['registration' => 'Data pendaftaran tidak ditemukan.']
            ]);
        }

        $unsubmitted = array_values(array_filter($registrations, fn($reg) => (int)($reg->submitted ?? 0) !== 1));
        if (empty($unsubmitted)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['registration' => 'Data pendaftaran sudah disubmit dan tidak dapat diubah lagi.']
            ]);
        }

        $offeringModel = new AdmissionOfferingModel();
        $targets = array_values(array_filter($unsubmitted, fn($reg) => $offeringModel->isOpen((int)($reg->admission_offering_id ?? 0))));
        if (empty($targets)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['registration' => 'Periode pendaftaran sudah ditutup. Dokumen tidak dapat diunggah lagi.']
            ]);
        }

        $docModel = new RegistrationDocumentModel();
        $offering_ids = array_map(fn($reg) => (int)$reg->admission_offering_id, $targets);
        $document = null;
        foreach ($docModel->findRequirementList($offering_ids) as $req) {
            if ($req['document_id'] === $document_id) {
                $document = $req;
                break;
            }
        }
        if ($document === null) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['document' => 'Dokumen tidak tersedia untuk program yang dipilih.']
            ]);
        }

        $max_size = (int)($document['max_size'] ?? 0);
        if ($max_size <= 0) {
            $max_size = 2048;
        }
        $validated = $this->validate([
            'file' => [
                'label' => 'Dokumen',
                'rules' => [
                    'uploaded[file]',
                    'mime_in[file,image/jpg,image/jpeg,image/png,application/pdf]',
                    'max_size[file,' . $max_size . ']',
                ],
            ],
        ]);
        if (!$validated) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => [
                    'code' => 500,
                    'message' => $file ? $file->getErrorString() : 'Tidak ada file yang diunggah.'
                ]
            ]);
        }

        $target_ids = array_map(fn($reg) => (int)$reg->id, $targets);
        $old_files = $docModel->findFileNames($target_ids, $document_id);

        $uploadPath = private_upload_path();
        $newName = $file->getRandomName();
        try {
            $file->move($uploadPath, $newName);
        } catch (Throwable $e) {
            log_message('error', 'Document upload failed: {message}', ['message' => $e->getMessage()]);
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['upload' => 'Gagal menyimpan file. Silakan coba lagi.']
            ]);
        }

        $updated = 0;
        foreach ($target_ids as $reg_id) {
            if ($docModel->upsert($reg_id, $document_id, $newName)) {
                $updated++;
            }
        }

        if ($updated === 0) {
            @unlink($uploadPath . $newName);
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['document' => 'Dokumen gagal disimpan. Silakan coba lagi.']
            ]);
        }

        $docModel->deleteUnreferencedFiles(array_filter($old_files, fn($f) => $f !== $newName));

        return $this->response->setJSON([
            'success' => true,
            'errors' => [],
            'file_name' => $newName,
        ]);
    }

    public function download(string $file_name): ?ResponseInterface
    {
        $safe_name = basename($file_name);
        $owns_file = new RegistrationDocumentModel()->ownsFile((int)session()->get('actor'), $safe_name);
        if (!$owns_file) {
            throw PageNotFoundException::forPageNotFound();
        }

        $file_path = find_registration_file($safe_name);
        if ($file_path === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        return $this->response->download($file_path, null)->setFileName($safe_name);
    }
}
