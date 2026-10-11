<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;

/**
 * Handles database operations for general subject grade requirements.
 *
 * @package App\Models
 */
class GeneralSubjectGradeRequirementModel extends Model
{
    protected $table = 'general_subject_grade_requirements';
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
     * Retrieve subject grade requirements filtered by academic year.
     *
     * @param int $academic_year_id The academic year ID to filter by.
     * @return BaseResult The database result object.
     */
    public function findAllByAcademicYear(int $academic_year_id): BaseResult
    {
        return $this->select('general_subject_grade_requirements.id, general_subject_grade_requirements.is_deleted, general_subject_grade_requirements.modified_at, general_subject_grade_requirements.subject_id, subjects.name AS subject_name, general_subject_grade_requirements.threshold_score')
            ->join('subjects', 'general_subject_grade_requirements.subject_id = subjects.id', 'left')
            ->where('general_subject_grade_requirements.academic_year_id', $academic_year_id)
            ->orderBy('general_subject_grade_requirements.id')
            ->get();
    }
}
