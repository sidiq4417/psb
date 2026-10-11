<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;

/**
 * Handles database operations for offering-specific entrance exam scores.
 *
 * @package App\Models
 */
class EntranceExamScoreModel extends Model
{
    protected $table = 'offering_entrance_exam_scores';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'admission_offering_id', 'subject_id', 'threshold_score',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';

    /**
     * Retrieve entrance exam scores filtered by admission offering.
     *
     * @param int $admission_offering_id The offering ID to filter by.
     * @return BaseResult The database result object.
     */
    public function findAllByAdmissionOffering(int $admission_offering_id): BaseResult
    {
        return $this->select('offering_entrance_exam_scores.id, offering_entrance_exam_scores.is_deleted, offering_entrance_exam_scores.modified_at, offering_entrance_exam_scores.subject_id, subjects.name AS subject, offering_entrance_exam_scores.threshold_score')
            ->join('subjects', 'offering_entrance_exam_scores.subject_id = subjects.id', 'left')
            ->where('offering_entrance_exam_scores.admission_offering_id', $admission_offering_id)
            ->orderBy('offering_entrance_exam_scores.id')
            ->get();
    }
}
