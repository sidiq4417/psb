<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;

/**
 * Handles database operations for general document requirements.
 *
 * @package App\Models
 */
class GeneralDocumentRequirementModel extends Model
{
    protected $table = 'general_document_requirements';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'academic_year_id', 'document_id', 'required',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';

    /**
     * Retrieve document requirements filtered by academic year.
     *
     * @param int $academic_year_id The academic year ID to filter by.
     * @return BaseResult The database result object.
     */
    public function findAllByAcademicYear(int $academic_year_id): BaseResult
    {
        return $this->select('general_document_requirements.id, general_document_requirements.is_deleted, general_document_requirements.modified_at, general_document_requirements.document_id, admission_documents.name AS document_name, admission_documents.max_size, general_document_requirements.required')
            ->join('admission_documents', 'general_document_requirements.document_id = admission_documents.id', 'left')
            ->where('general_document_requirements.academic_year_id', $academic_year_id)
            ->orderBy('general_document_requirements.id')
            ->get();
    }
}
