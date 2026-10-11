<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\AcademicYearModel;
use App\Models\AdmissionDocumentModel;
use App\Models\DocumentRequirementModel;
use App\Models\GeneralDocumentRequirementModel;
use App\Models\RegistrationDocumentModel;
use App\Models\RegistrationModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

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

        $unique_document_requirements = $this->findDocumentRequirements($offering_ids);

        $reg_ids = array_map(fn($r) => (int)$r->id, $registrations);
        $existing = new RegistrationDocumentModel()->findByRegIds($reg_ids);

        foreach ($unique_document_requirements as &$item) {
            foreach ($existing as $ex) {
                if ((int)$ex->document_id === (int)$item['document_id']) {
                    $item['file_name'] = $ex->file ?? '';
                    break;
                }
            }
        }

        $res['records'] = array_map(fn($r) => array_merge($r, ['submitted' => $all_submitted]), $unique_document_requirements);
        return $this->response->setJSON($res);
    }

    private function findDocumentRequirements(array $offering_ids = []): array
    {
        $generalModel = new GeneralDocumentRequirementModel();
        $specificModel = new DocumentRequirementModel();
        $document_requirements = [];

        $academic_year_id = $this->getAcademicYearId($this->getAdmissionYear());

        if ($academic_year_id > 0) {
            $generalQuery = $generalModel->findAllByAcademicYear($academic_year_id);
            if ($generalQuery->getNumRows() > 0) {
                foreach ($generalQuery->getResult() as $row) {
                    $document_requirements[] = [
                        'document_id' => (int)$row->document_id,
                        'document_name' => $row->document_name ?? '',
                        'max_size' => $row->max_size,
                        'file_name' => '',
                    ];
                }
            }
        }

        foreach ($offering_ids as $oid) {
            if ($oid <= 0) continue;
            $specificQuery = $specificModel->findByAdmissionOfferingId($oid);
            if ($specificQuery->getNumRows() > 0) {
                foreach ($specificQuery->getResult() as $row) {
                    $document_requirements[] = [
                        'document_id' => (int)$row->document_id,
                        'document_name' => $row->document_name ?? '',
                        'max_size' => $row->max_size,
                        'file_name' => '',
                    ];
                }
            }
        }

        $unique_document_requirements = [];
        $seenDocumentIds = [];
        foreach ($document_requirements as $item) {
            if (!in_array($item['document_id'], $seenDocumentIds)) {
                $seenDocumentIds[] = $item['document_id'];
                $unique_document_requirements[] = $item;
            }
        }
        return $unique_document_requirements;
    }

    public function upload(): ResponseInterface
    {
        $document_id = (int)$this->request->getPost('document_id');
        $registrationModel = new RegistrationModel();
        $registrations = $registrationModel->findActiveByStudent((int)session()->get('actor'));
        if (empty($registrations)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['registration' => 'Registration not found.']
            ]);
        }

        $offering_ids = [];
        foreach ($registrations as $reg) {
            $oid = (int)($reg->admission_offering_id ?? 0);
            if ($oid > 0) $offering_ids[] = $oid;
        }
        $required_docs = $this->findDocumentRequirements($offering_ids);
        $valid_doc_ids = array_map(fn($d) => (int)$d['document_id'], $required_docs);
        if (!in_array($document_id, $valid_doc_ids)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['document' => 'Dokumen tidak tersedia untuk program yang dipilih.']
            ]);
        }

        $document = new AdmissionDocumentModel()->find($document_id);
        $max_size = (int)($document->max_size ?? 2048);
        $validated = $this->validate([
            'file' => [
                'uploaded[file]',
                'mime_in[file,image/jpg,image/jpeg,image/png,application/pdf]',
                'max_size[file,' . $max_size . ']',
            ],
        ]);
        if (!$validated) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $file = $this->request->getFile('file');
        if ($file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/';
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            $docModel = new RegistrationDocumentModel();
            foreach ($registrations as $reg) {
                if ((int)$reg->submitted === 1) continue;
                $reg_id = (int)$reg->id;
                $docModel->upsert($reg_id, $document_id, $newName);
            }

            return $this->response->setJSON([
                'success' => true,
                'errors' => [],
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'errors' => [
                    'code' => 500,
                    'message' => $file->getError()
                ]
            ]);
        }
    }

    public function download(string $file_name): ?ResponseInterface
    {
        $owns_file = new RegistrationDocumentModel()->ownsFile((int)session()->get('actor'), basename($file_name));
        if (!$owns_file) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = FCPATH . 'uploads/';
        $file_path = $path . basename($file_name);
        if (!file_exists($file_path)) {
            throw PageNotFoundException::forPageNotFound();
        }
        return $this->response->download($file_path, null)->setFileName($file_name);
    }

    private function getAdmissionYear(): int
    {
        return new RegistrationModel()->getAdmissionYearByStudent((int)session()->get('actor'));
    }

    private function getAcademicYearId(int $admission_year): int
    {
        return new AcademicYearModel()->findIdByNameLike((string)$admission_year);
    }
}
