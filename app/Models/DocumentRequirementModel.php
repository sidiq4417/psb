<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;

class DocumentRequirementModel extends Model
{
    protected $table = 'offering_document_requirements';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'admission_offering_id', 'document_id', 'required',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';

    /**
     * Retrieve document requirements for a specific offering (including soft-deleted).
     *
     * @param int $admission_offering_id The offering ID to filter by.
     * @return BaseResult The database result object.
     */
    public function findAllByOffering(int $admission_offering_id): BaseResult
    {
        return $this->select('offering_document_requirements.id, offering_document_requirements.is_deleted, offering_document_requirements.modified_at, offering_document_requirements.document_id, admission_documents.name AS document_name, admission_documents.max_size, offering_document_requirements.required')
            ->join('admission_documents', 'offering_document_requirements.document_id = admission_documents.id', 'left')
            ->where('offering_document_requirements.admission_offering_id', $admission_offering_id)
            ->orderBy('offering_document_requirements.id')
            ->get();
    }

    /**
     * Retrieve active document requirements for a specific offering.
     *
     * @param int $admission_offering_id The offering ID to filter by.
     * @return BaseResult The database result object.
     */
    public function findByAdmissionOfferingId(int $admission_offering_id): BaseResult
    {
        return $this->select('offering_document_requirements.document_id, admission_documents.name AS document_name, admission_documents.max_size, offering_document_requirements.required')
            ->join('admission_documents', 'offering_document_requirements.document_id = admission_documents.id', 'left')
            ->where('offering_document_requirements.admission_offering_id', $admission_offering_id)
            ->where('offering_document_requirements.is_deleted', false)
            ->orderBy('1')
            ->get();
    }
}
