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
     * @param int   $registration_id The registration ID.
     * @param int   $subject_id      The subject ID.
     * @param float $score           The score value (0-100, up to 2 decimals).
     * @param float $threshold_score The threshold score value.
     * @return void
     */
    public function upsert(int $registration_id, int $subject_id, float $score, float $threshold_score): void
    {
        $existing = $this->findByRegistrationIdAndSubject($registration_id, $subject_id);
        if ($existing) {
            $this->update($existing->id, ['score' => $score, 'threshold_score' => $threshold_score]);
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

    /**
     * Build the subject grade requirement list for a set of admission offerings.
     *
     * Combines the general requirements of each offering's academic year with
     * the offering-specific requirements (soft-deleted rows excluded) and keeps
     * one entry per subject. Used by the student grade page, the grade save
     * check and the final submit check so all of them use the same list.
     *
     * @param array<int, int> $offering_ids The admission offering IDs.
     * @return array<int, array{subject_id: int, subject_name: string, score: float, threshold_score: float}>
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
        $generalModel = new GeneralSubjectGradeRequirementModel();
        foreach ($academic_year_ids as $ay_id) {
            foreach ($generalModel->findAllByAcademicYear($ay_id)->getResult() as $row) {
                if ((int)($row->is_deleted ?? 0) === 1) continue;
                $rows[] = $row;
            }
        }

        $specificModel = new SubjectGradeRequirementModel();
        foreach ($offering_ids as $oid) {
            foreach ($specificModel->findByAdmissionOfferingId($oid)->getResult() as $row) {
                $rows[] = $row;
            }
        }

        $requirements = [];
        foreach ($rows as $row) {
            $subject_id = (int)$row->subject_id;
            if ($subject_id <= 0 || isset($requirements[$subject_id])) continue;
            $requirements[$subject_id] = [
                'subject_id' => $subject_id,
                'subject_name' => $row->subject_name ?? '',
                'score' => 0.0,
                'threshold_score' => (float)($row->threshold_score ?? 0),
            ];
        }
        return array_values($requirements);
    }

    /**
     * List the required subjects of a registration that have no score yet.
     *
     * A subject counts as filled when its score is greater than zero, the same
     * rule the grade page uses for its progress indicator.
     *
     * @param int $registration_id The registration ID.
     * @param int $offering_id     The registration's admission offering ID.
     * @return array<int, string> Names of the subjects without a score (empty when complete).
     */
    public function findMissingScores(int $registration_id, int $offering_id): array
    {
        $scored = [];
        foreach ($this->findByRegistrationId($registration_id) as $grade) {
            if ($grade->score !== null && (float)$grade->score > 0) {
                $scored[(int)$grade->subject_id] = true;
            }
        }

        $missing = [];
        foreach ($this->findRequirementList([$offering_id]) as $req) {
            if (!isset($scored[$req['subject_id']])) {
                $missing[] = $req['subject_name'] !== '' ? $req['subject_name'] : ('Mapel #' . $req['subject_id']);
            }
        }
        return $missing;
    }
}
