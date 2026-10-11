<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class RegistrationExamScoreModel extends Model
{
    protected $table = 'registration_exam_scores';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'registration_id', 'exam_scores_id', 'score',
    ];
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    /**
     * Delete all exam score records for a given registration.
     *
     * @param int $registration_id The registration ID.
     * @return void
     */
    public function deleteByRegistrationId(int $registration_id): void
    {
        $this->where('registration_id', $registration_id)->delete();
    }
}
