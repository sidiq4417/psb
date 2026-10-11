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

    /**
     * List the required documents with the student's uploaded files.
     *
     * Documents are stored per registration, so a document is only editable when
     * it is required by a registration that has not been finalised yet and whose
     * admission period is still open. Editable documents show the latest file of
     * those registrations, locked documents the latest file of the locked ones.
     * The response also lists every registration with its lock status. An
     * optional registration_id limits the list to one of the student's
     * registrations (used by the confirmation summary).
     *
     * @return ResponseInterface JSON with records and registrations.
     */
    public function lists(): ResponseInterface
    {
        $registrations = new RegistrationModel()->findActiveByStudent((int)session()->get('actor'));

        $registration_id = (int)($this->request->getVar('registration_id') ?? 0);
        if ($registration_id > 0) {
            $registrations = array_values(array_filter($registrations, fn($reg) => (int)$reg->id === $registration_id));
        }
        if (empty($registrations)) {
            return $this->response->setJSON(['records' => [], 'registrations' => []]);
        }

        $states = $this->describeRegistrations($registrations);
        $editable_by_id = array_column($states, 'editable', 'id');
        $editable_offering_ids = array_column(array_filter($states, fn($state) => $state['editable']), 'offering_id');

        $docModel = new RegistrationDocumentModel();
        $editable_requirements = array_column($docModel->findRequirementList($editable_offering_ids), null, 'document_id');

        $latest_files = ['editable' => [], 'locked' => []];
        foreach ($docModel->findByRegIds(array_column($states, 'id')) as $ex) {
            $file = trim((string)($ex->file ?? ''));
            if ($file === '') continue;
            $group = ($editable_by_id[(int)$ex->registration_id] ?? false) ? 'editable' : 'locked';
            $document_id = (int)$ex->document_id;
            $current = $latest_files[$group][$document_id] ?? null;
            if ($current === null || (string)($ex->uploaded_at ?? '') > (string)($current->uploaded_at ?? '')) {
                $latest_files[$group][$document_id] = $ex;
            }
        }

        $records = [];
        foreach ($docModel->findRequirementList(array_column($states, 'offering_id')) as $item) {
            $document_id = $item['document_id'];
            $editable = isset($editable_requirements[$document_id]);
            if ($editable) {
                $item['required'] = $editable_requirements[$document_id]['required'];
                $item['max_size'] = $editable_requirements[$document_id]['max_size'];
            }
            $latest = $latest_files[$editable ? 'editable' : 'locked'][$document_id] ?? null;
            if ($latest !== null) {
                $item['file_name'] = $latest->file;
            }
            $item['editable'] = $editable;
            $records[] = $item;
        }

        return $this->response->setJSON([
            'records' => $records,
            'registrations' => $states,
        ]);
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
            $all_offering_ids = array_map(fn($reg) => (int)($reg->admission_offering_id ?? 0), $registrations);
            $locked_only = in_array($document_id, array_column($docModel->findRequirementList($all_offering_ids), 'document_id'), true);
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['document' => $locked_only
                    ? 'Dokumen ini sudah dikunci karena hanya dipersyaratkan oleh pendaftaran yang sudah dikirim final atau periodenya sudah ditutup.'
                    : 'Dokumen tidak tersedia untuk program yang dipilih.'],
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

    /**
     * Describe the student's registrations and whether each one can still be edited.
     *
     * A registration is locked once it has been finalised ("Kirim Final") or when
     * the admission period of its offering has closed.
     *
     * @param array<int, object> $registrations The student's active registrations.
     * @return array<int, array{id: int, offering_id: int, label: string, registration_number: string, submitted: bool, editable: bool, status: string}>
     */
    private function describeRegistrations(array $registrations): array
    {
        $registrationModel = new RegistrationModel();
        $offeringModel = new AdmissionOfferingModel();

        $states = [];
        foreach ($registrations as $reg) {
            $id = (int)$reg->id;
            $offering_id = (int)($reg->admission_offering_id ?? 0);
            $submitted = (int)($reg->submitted ?? 0) === 1;
            $open = !$submitted && $offering_id > 0 && $offeringModel->isOpen($offering_id);

            $details = $registrationModel->findDetailsById($id)->getRow();
            $parts = array_filter([
                (string)($details->branch ?? ''),
                (string)($details->entry_grade ?? ''),
                (string)($details->education_track ?? ''),
                (string)($details->education_program ?? ''),
            ], fn($v) => $v !== '');

            $states[] = [
                'id' => $id,
                'offering_id' => $offering_id,
                'label' => empty($parts) ? 'Pendaftaran #' . $id : implode(' - ', $parts),
                'registration_number' => $submitted ? (string)($reg->registration_number ?? '') : '',
                'submitted' => $submitted,
                'editable' => $open,
                'status' => match (true) {
                    $submitted => 'Sudah dikirim final',
                    !$open => 'Periode pendaftaran ditutup',
                    default => 'Dapat diubah',
                },
            ];
        }
        return $states;
    }
}
