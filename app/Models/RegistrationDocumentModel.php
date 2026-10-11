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
        'registration_id', 'document_id', 'file', 'file_path', 'verified', 'uploaded_at',
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
     * @param string $file            The uploaded file path.
     * @param bool   $updateIfExists  Whether to update if the record already exists.
     * @return int|null The document record ID.
     */
    public function upsert(int $registration_id, int $document_id, string $file, bool $updateIfExists = true): ?int
    {
        $existing = $this->findByRegistrationIdAndDocument($registration_id, $document_id);
        if ($existing) {
            if ($updateIfExists) {
                $this->update($existing->id, [
                    'file' => $file,
                    'uploaded_at' => date('Y-m-d H:i:s'),
                ]);
            }
            return $existing->id;
        }
        return $this->insert([
            'registration_id' => $registration_id,
            'document_id' => $document_id,
            'file' => $file,
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]);
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
                    'file_path' => $d->file_path ?? null,
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
}
