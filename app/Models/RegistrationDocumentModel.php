<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class RegistrationDocumentModel extends Model
{
    protected $table = 'registration_documents';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'registration_id', 'document_id', 'file', 'verified', 'uploaded_at',
    ];
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    /**
     * Get all document records for a given registration.
     *
     * @param int $registration_id The registration ID.
     * @return array The document records.
     */
    public function findByRegistrationId(int $registration_id): array
    {
        return $this->where('registration_id', $registration_id)->findAll();
    }

    /**
     * Find a document record for a specific registration and document type.
     *
     * @param int $registration_id The registration ID.
     * @param int $document_id     The document type ID.
     * @return object|null The record, or null if not found.
     */
    private function findByRegistrationIdAndDocument(int $registration_id, int $document_id): ?object
    {
        return $this->where('registration_id', $registration_id)
            ->where('document_id', $document_id)
            ->first();
    }

    /**
     * Get all document records for multiple registration IDs.
     *
     * @param array $registration_ids The registration IDs to search.
     * @return array The document records.
     */
    public function findByRegIds(array $registration_ids): array
    {
        if (empty($registration_ids)) return [];
        return $this->whereIn('registration_id', $registration_ids)->findAll();
    }

    /**
     * Count all document records for a given registration.
     *
     * @param int $registration_id The registration ID.
     * @return int The total count.
     */
    public function countByRegistrationId(int $registration_id): int
    {
        return $this->where('registration_id', $registration_id)->countAllResults();
    }

    /**
     * Count document records that have been uploaded (non-null, non-empty file).
     *
     * @param int $registration_id The registration ID.
     * @return int The count of uploaded documents.
     */
    public function countUploadedByRegistrationId(int $registration_id): int
    {
        return $this->where('registration_id', $registration_id)
            ->where('file IS NOT NULL')
            ->where('file !=', '')
            ->countAllResults();
    }

    /**
     * Check if a registration has any documents with empty or missing files.
     *
     * @param int $registration_id The registration ID.
     * @return bool True if there are unuploaded documents.
     */
    public function hasEmptyFile(int $registration_id): bool
    {
        return $this->where('registration_id', $registration_id)
            ->groupStart()
                ->where('file IS NULL')
                ->orWhere('file', '')
            ->groupEnd()
            ->countAllResults() > 0;
    }

    /**
     * Insert or update a document record for a registration.
     *
     * @param int    $registration_id The registration ID.
     * @param int    $document_id     The document type ID.
     * @param string $file            The stored file name.
     * @param bool   $updateIfExists  Whether to update if the record already exists.
     * @return bool True when the record was inserted or updated.
     */
    public function upsert(int $registration_id, int $document_id, string $file, bool $updateIfExists = true): bool
    {
        $existing = $this->findByRegistrationIdAndDocument($registration_id, $document_id);
        if ($existing) {
            if (!$updateIfExists) {
                return false;
            }
            return $this->update($existing->id, [
                'file' => $file,
                'uploaded_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $insertId = $this->insert([
            'registration_id' => $registration_id,
            'document_id' => $document_id,
            'file' => $file,
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]);
        return $insertId !== false && (int)$insertId > 0;
    }

    /**
     * Delete all document records for a given registration.
     *
     * @param int $registration_id The registration ID.
     * @return void
     */
    public function deleteByRegistrationId(int $registration_id): void
    {
        $this->where('registration_id', $registration_id)->delete();
    }

    /**
     * Copy all document records from one registration to another.
     *
     * Skips documents that already exist in the target registration.
     *
     * @param int $from_registration_id The source registration ID.
     * @param int $to_registration_id   The target registration ID.
     * @return void
     */
    public function copyFromRegistration(int $from_registration_id, int $to_registration_id): void
    {
        $docs = $this->findByRegistrationId($from_registration_id);
        foreach ($docs as $d) {
            $existing = $this->findByRegistrationIdAndDocument($to_registration_id, (int)$d->document_id);
            if (!$existing) {
                $this->insert([
                    'registration_id' => $to_registration_id,
                    'document_id' => $d->document_id,
                    'file' => $d->file,
                    'verified' => $d->verified ?? 0,
                    'uploaded_at' => $d->uploaded_at,
                ]);
            }
        }
    }

    /**
     * Check if a student owns a specific document file.
     *
     * @param int    $student_id The student ID.
     * @param string $file_name  The document file name.
     * @return bool True if the student owns the file.
     */
    public function ownsFile(int $student_id, string $file_name): bool
    {
        $db = db_connect();
        $count = $db->table('registration_documents rd')
            ->join('registrations r', 'rd.registration_id = r.id')
            ->where('rd.file', $file_name)
            ->where('r.student_id', $student_id)
            ->where('r.is_deleted', false)
            ->countAllResults();
        return $count > 0;
    }

    /**
     * Build the document requirement list for a set of admission offerings.
     *
     * Combines the general requirements of each offering's academic year with
     * the offering-specific requirements (soft-deleted rows excluded), keeps
     * one entry per document and marks it required when any source requires it.
     * Used by the student document page and by the final submit check so both
     * work from the same list.
     *
     * @param array<int, int> $offering_ids The admission offering IDs.
     * @return array<int, array{document_id: int, document_name: string, max_size: mixed, required: bool, file_name: string}>
     */
    public function findRequirementList(array $offering_ids): array
    {
        $offering_ids = array_values(array_unique(array_filter(array_map('intval', $offering_ids), fn($id) => $id > 0)));
        if (empty($offering_ids)) {
            return [];
        }

        $offeringModel = new AdmissionOfferingModel();
        $academic_year_ids = [];
        foreach ($offering_ids as $oid) {
            $ay_id = $offeringModel->getAcademicYearId($oid);
            if ($ay_id > 0) {
                $academic_year_ids[$ay_id] = $ay_id;
            }
        }

        $rows = [];
        $generalModel = new GeneralDocumentRequirementModel();
        foreach ($academic_year_ids as $ay_id) {
            foreach ($generalModel->findAllByAcademicYear($ay_id)->getResult() as $row) {
                if ((int)($row->is_deleted ?? 0) === 1) continue;
                $rows[] = $row;
            }
        }

        $specificModel = new DocumentRequirementModel();
        foreach ($offering_ids as $oid) {
            foreach ($specificModel->findByAdmissionOfferingId($oid)->getResult() as $row) {
                $rows[] = $row;
            }
        }

        $requirements = [];
        foreach ($rows as $row) {
            $document_id = (int)$row->document_id;
            if ($document_id <= 0) continue;
            $required = $row->required === null || (int)$row->required === 1;
            if (isset($requirements[$document_id])) {
                $requirements[$document_id]['required'] = $requirements[$document_id]['required'] || $required;
                continue;
            }
            $requirements[$document_id] = [
                'document_id' => $document_id,
                'document_name' => $row->document_name ?? '',
                'max_size' => $row->max_size,
                'required' => $required,
                'file_name' => '',
            ];
        }
        return array_values($requirements);
    }

    /**
     * List the required documents of a registration that have no uploaded file.
     *
     * @param int $registration_id The registration ID.
     * @param int $offering_id     The registration's admission offering ID.
     * @return array<int, string> Names of the missing documents (empty when complete).
     */
    public function findMissingRequired(int $registration_id, int $offering_id): array
    {
        $uploaded = [];
        foreach ($this->findByRegistrationId($registration_id) as $doc) {
            if (trim((string)($doc->file ?? '')) !== '') {
                $uploaded[(int)$doc->document_id] = true;
            }
        }

        $missing = [];
        foreach ($this->findRequirementList([$offering_id]) as $req) {
            if ($req['required'] && !isset($uploaded[$req['document_id']])) {
                $missing[] = $req['document_name'] !== '' ? $req['document_name'] : ('Dokumen #' . $req['document_id']);
            }
        }
        return $missing;
    }

    /**
     * Get the stored file names of one document type across registrations.
     *
     * @param array<int, int> $registration_ids The registration IDs.
     * @param int             $document_id      The document type ID.
     * @return array<int, string> Distinct non-empty file names.
     */
    public function findFileNames(array $registration_ids, int $document_id): array
    {
        if (empty($registration_ids)) return [];
        $rows = $this->select('file')
            ->whereIn('registration_id', $registration_ids)
            ->where('document_id', $document_id)
            ->findAll();
        $files = [];
        foreach ($rows as $row) {
            $file = trim((string)($row->file ?? ''));
            if ($file !== '') {
                $files[$file] = $file;
            }
        }
        return array_values($files);
    }

    /**
     * Check whether a stored file is still referenced by any registration
     * (as a document or as a transfer proof).
     *
     * @param string $file_name The stored file name.
     * @return bool True if at least one record still points to the file.
     */
    public function isFileReferenced(string $file_name): bool
    {
        $db = db_connect();
        $documents = $db->table('registration_documents')->where('file', $file_name)->countAllResults();
        if ($documents > 0) {
            return true;
        }
        return $db->table('registrations')->where('transfer_proof', $file_name)->countAllResults() > 0;
    }

    /**
     * Delete stored applicant files that are no longer referenced by any record.
     *
     * @param array<int, string> $file_names The stored file names to check.
     * @return void
     */
    public function deleteUnreferencedFiles(array $file_names): void
    {
        foreach (array_unique($file_names) as $file_name) {
            $file_name = trim((string)$file_name);
            if ($file_name === '' || $this->isFileReferenced($file_name)) {
                continue;
            }
            $path = find_registration_file($file_name);
            if ($path !== null) {
                @unlink($path);
            }
        }
    }
}
