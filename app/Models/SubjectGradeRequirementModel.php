<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;

class SubjectGradeRequirementModel extends Model
{
    protected $table = 'offering_subject_grade_requirements';
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
     * Retrieve subject grade requirements for a specific offering (including soft-deleted).
     *
     * @param int $admission_offering_id The offering ID to filter by.
     * @return BaseResult The database result object.
     */
    public function findAllByOffering(int $admission_offering_id): BaseResult
    {
        return $this->select('offering_subject_grade_requirements.id, offering_subject_grade_requirements.is_deleted, offering_subject_grade_requirements.modified_at, subjects.name AS subject, offering_subject_grade_requirements.threshold_score')
            ->join('subjects', 'offering_subject_grade_requirements.subject_id = subjects.id', 'left')
            ->where('offering_subject_grade_requirements.admission_offering_id', $admission_offering_id)
            ->orderBy('offering_subject_grade_requirements.id')
            ->get();
    }

    /**
     * Retrieve active subject grade requirements for a specific offering.
     *
     * @param int $admission_offering_id The offering ID to filter by.
     * @return BaseResult The database result object.
     */
    public function findByAdmissionOfferingId(int $admission_offering_id): BaseResult
    {
        return $this->select('offering_subject_grade_requirements.subject_id, subjects.name AS subject_name, offering_subject_grade_requirements.threshold_score')
            ->join('subjects', 'offering_subject_grade_requirements.subject_id = subjects.id', 'left')
            ->where('offering_subject_grade_requirements.admission_offering_id', $admission_offering_id)
            ->where('offering_subject_grade_requirements.is_deleted', false)
            ->orderBy('offering_subject_grade_requirements.subject_id')
            ->get();
    }
}
