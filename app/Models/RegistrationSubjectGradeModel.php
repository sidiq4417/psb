<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class RegistrationSubjectGradeModel extends Model
{
    protected $table = 'registration_subject_grades';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'registration_id', 'subject_id', 'score', 'threshold_score',
    ];
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    /**
     * Get all subject grade records for a given registration.
     *
     * @param int $registration_id The registration ID.
     * @return array The subject grade records.
     */
    public function findByRegistrationId(int $registration_id): array
    {
        return $this->where('registration_id', $registration_id)->findAll();
    }

    /**
     * Find a subject grade record for a specific registration and subject.
     *
     * @param int $registration_id The registration ID.
     * @param int $subject_id      The subject ID.
     * @return object|null The record, or null if not found.
     */
    private function findByRegistrationIdAndSubject(int $registration_id, int $subject_id): ?object
    {
        return $this->where('registration_id', $registration_id)
            ->where('subject_id', $subject_id)
            ->first();
    }

    /**
     * Count all subject grade records for a given registration.
     *
     * @param int $registration_id The registration ID.
     * @return int The total count.
     */
    public function countByRegistrationId(int $registration_id): int
    {
        return $this->where('registration_id', $registration_id)->countAllResults();
    }

    /**
     * Count subject grade records where the score is greater than zero.
     *
     * @param int $registration_id The registration ID.
     * @return int The count of graded subjects.
     */
    public function countWithScore(int $registration_id): int
    {
        return $this->where('registration_id', $registration_id)
            ->where('score >', 0)
            ->countAllResults();
    }

    /**
     * Check if a registration has any ungraded subjects (score <= 0).
     *
     * @param int $registration_id The registration ID.
     * @return bool True if there are empty grades.
     */
    public function hasEmptyGrade(int $registration_id): bool
    {
        return $this->where('registration_id', $registration_id)
            ->where('score <=', 0)
            ->countAllResults() > 0;
    }

    /**
     * Insert or update a subject grade record.
     *
     * @param int $registration_id The registration ID.
     * @param int $subject_id      The subject ID.
     * @param int $score           The score value.
     * @param int $threshold_score The threshold score value.
     * @return void
     */
    public function upsert(int $registration_id, int $subject_id, int $score, int $threshold_score): void
    {
        $existing = $this->findByRegistrationIdAndSubject($registration_id, $subject_id);
        if ($existing) {
            $this->update($existing->id, ['score' => $score]);
        } else {
            $this->insert([
                'registration_id' => $registration_id,
                'subject_id' => $subject_id,
                'score' => $score,
                'threshold_score' => $threshold_score,
            ]);
        }
    }

    /**
     * Delete all subject grade records for a given registration.
     *
     * @param int $registration_id The registration ID.
     * @return void
     */
    public function deleteByRegistrationId(int $registration_id): void
    {
        $this->where('registration_id', $registration_id)->delete();
    }

    /**
     * Copy all subject grade records from one registration to another.
     *
     * Skips subjects that already exist in the target registration.
     *
     * @param int $from_registration_id The source registration ID.
     * @param int $to_registration_id   The target registration ID.
     * @return void
     */
    public function copyFromRegistration(int $from_registration_id, int $to_registration_id): void
    {
        $grades = $this->findByRegistrationId($from_registration_id);
        foreach ($grades as $g) {
            $existing = $this->findByRegistrationIdAndSubject($to_registration_id, (int)$g->subject_id);
            if (!$existing) {
                $this->insert([
                    'registration_id' => $to_registration_id,
                    'subject_id' => $g->subject_id,
                    'score' => $g->score,
                    'threshold_score' => $g->threshold_score,
                ]);
            }
        }
    }

    /**
     * Transfer all subject grade records from one registration to another.
     *
     * Skips subjects that already exist in the target registration.
     *
     * @param int $from_registration_id The source registration ID.
     * @param int $to_registration_id   The target registration ID.
     * @return void
     */
    public function transferToRegistration(int $from_registration_id, int $to_registration_id): void
    {
        $grades = $this->findByRegistrationId($from_registration_id);
        foreach ($grades as $g) {
            $existing = $this->findByRegistrationIdAndSubject($to_registration_id, (int)$g->subject_id);
            if (!$existing) {
                $this->insert([
                    'registration_id' => $to_registration_id,
                    'subject_id' => $g->subject_id,
                    'score' => $g->score,
                    'threshold_score' => $g->threshold_score,
                ]);
            }
        }
    }
}
