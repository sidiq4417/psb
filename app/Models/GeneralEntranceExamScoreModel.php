<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;

/**
 * Handles database operations for general entrance exam scores.
 *
 * @package App\Models
 */
class GeneralEntranceExamScoreModel extends Model
{
    protected $table = 'general_entrance_exam_scores';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'academic_year_id', 'subject_id', 'threshold_score',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';

    /**
     * Retrieve entrance exam scores filtered by academic year.
     *
     * @param int $academic_year_id The academic year ID to filter by.
     * @return BaseResult The database result object.
     */
    public function findAllByAcademicYear(int $academic_year_id): BaseResult
    {
        return $this->select('general_entrance_exam_scores.id, general_entrance_exam_scores.is_deleted, general_entrance_exam_scores.modified_at, general_entrance_exam_scores.subject_id, subjects.name AS subject, general_entrance_exam_scores.threshold_score')
            ->join('subjects', 'general_entrance_exam_scores.subject_id = subjects.id', 'left')
            ->where('general_entrance_exam_scores.academic_year_id', $academic_year_id)
            ->orderBy('general_entrance_exam_scores.id')
            ->get();
    }
}
