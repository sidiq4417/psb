<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Database\Query;
use CodeIgniter\Model;

class RegistrationModel extends Model
{
    protected $table = 'registrations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'student_id', 'admission_offering_id', 'admission_period_id',
        'admission_year', 'registration_number', 'submitted', 'submitted_at',
        'submitted_by', 'selection_status', 'selection_notes', 'admission_fee',
        'transfer_proof', 'transfer_proof_uploaded_at', 'transfer_proof_verified',
        'transfer_proof_verified_at',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by', 'restored_by',
        'restored_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';

    /**
     * Find registrations by admission year with optional filters.
     *
     * @param int $admission_year The admission year to filter by.
     * @param string $selection_status The selection status to filter by.
     * @param int $branch_id The branch ID to filter by.
     * @param int $entry_grade_id The entry grade ID to filter by.
     * @param int $education_track_id The education track ID to filter by.
     * @param int $education_program_id The education program ID to filter by.
     * @return BaseResult|Query The query result.
     */
    public function findByAdmissionYear(int $admission_year = 0, string $selection_status = '', int $branch_id = 0, int $entry_grade_id = 0, int $education_track_id = 0, int $education_program_id = 0): BaseResult|Query
    {
        $db = db_connect();
        $conds = [];
        $params = [];
        if ($admission_year > 0) {
            $conds[] = 'x1.admission_year = ?';
            $params[] = $admission_year;
        }
        if ($selection_status !== '') {
            $conds[] = "COALESCE(x1.selection_status, '0') = ?";
            $params[] = $selection_status;
        }
        if ($branch_id > 0) {
            $conds[] = 'xoffering.branch_id = ?';
            $params[] = $branch_id;
        }
        if ($entry_grade_id > 0) {
            $conds[] = 'xoffering.entry_grade_id = ?';
            $params[] = $entry_grade_id;
        }
        if ($education_track_id > 0) {
            $conds[] = 'xoffering.education_track_id = ?';
            $params[] = $education_track_id;
        }
        if ($education_program_id > 0) {
            $conds[] = 'xoffering.education_program_id = ?';
            $params[] = $education_program_id;
        }

        $where = count($conds) > 0 ? ' WHERE ' . implode(' AND ', $conds) : '';

        return $db->query("--
        SELECT x1.id
            , x1.is_deleted
            , x1.created_at
            , x1.student_id
            , x1.registration_number
            , x1.admission_year
            , x1.submitted
            , x1.submitted_at
            , x1.submitted_by
            , x1.transfer_proof_verified
            , x1.transfer_proof_verified_at
            , x1.selection_status
            , x2.full_name
            , x2.nik
            , x2.npwp
            , CASE WHEN x2.gender = 'M' THEN 'L' ELSE 'P' END AS gender
            , x2.citizenship
            , x2.birth_place
            , x2.birth_date
            , x2.religion_id
            , x2.kps_receiver
            , x2.kps
            , x2.nisn
            , x2.npsn
            , x2.school_address
            , x2.school_name
            , x2.school_major
            , x2.school_graduation_year
            , x2.address
            , x2.village
            , x2.rt
            , x2.rw
            , x2.urban_village
            , x2.district_id
            , x2.residence_id
            , x2.transportation_id
            , x2.post_code
            , x2.email
            , x2.telephone
            , x2.mobile_phone
            , x2.whatsapp
            , x2.telegram
            , x2.facebook
            , x2.twitter
            , x2.instagram
            , x2.tiktok
            , x2.father_nik
            , x2.father_name
            , x2.father_birth_date
            , x2.father_education_id
            , x2.father_income_id
            , x2.mother_nik
            , x2.mother_name
            , x2.mother_birth_date
            , x2.mother_education_id
            , x2.mother_income_id
            , x2.guardian_nik
            , x2.guardian_name
            , x2.guardian_birth_date
            , x2.guardian_education_id
            , x2.guardian_occupation_id
            , x2.guardian_income_id
            , xbranch.name AS branch
            , xentry.name AS entry_grade
            , xtrack.name AS education_track
            , xprogram.name AS education_program
            , xperiod.name AS admission_period
            , (SELECT COUNT(*) FROM registration_subject_grades WHERE registration_id = x1.id) AS grade_count
            , (SELECT COUNT(*) FROM registration_documents WHERE registration_id = x1.id AND file IS NOT NULL AND file != '') AS document_count
        FROM registrations x1
        LEFT JOIN users x2 ON x1.student_id = x2.id
        LEFT JOIN admission_offerings xoffering ON x1.admission_offering_id = xoffering.id
        LEFT JOIN branches xbranch ON xoffering.branch_id = xbranch.id
        LEFT JOIN entry_grades xentry ON xoffering.entry_grade_id = xentry.id
        LEFT JOIN education_tracks xtrack ON xoffering.education_track_id = xtrack.id
        LEFT JOIN education_programs xprogram ON xoffering.education_program_id = xprogram.id
        LEFT JOIN admission_periods xperiod ON xoffering.admission_period_id = xperiod.id
        $where
        ORDER BY x1.created_at
        ", $params);
    }

    /**
     * Find registrations by admission offering ID.
     *
     * @param int $admission_offering_id The admission offering ID.
     * @return BaseResult|Query The query result.
     */
    public function findByAdmissionOfferingId(int $admission_offering_id): BaseResult|Query
    {
        $db = db_connect();
        return $db->query("--
        SELECT x2.id AS record_id
             , x2.is_deleted
             , x2.created_at
             , x2.student_id
             , x2.registration_number
             , x2.admission_fee
             , x2.submitted
             , x2.submitted_at
             , x2.admission_year
             , x2.transfer_proof
             , x2.transfer_proof_verified
             , x2.transfer_proof_verified_at
             , x3.full_name
             , x3.nik
             , x3.npwp
             , CASE WHEN x3.gender = 'M' THEN 'L' ELSE 'P' END AS gender
             , x3.citizenship
             , x3.birth_place
             , x3.birth_date
             , x3.religion_id
             , x3.kps_receiver
             , x3.kps
             , x3.nisn
             , x3.npsn
             , x3.school_address
             , x3.school_name
             , x3.school_major
             , x3.school_graduation_year
             , x3.address
             , x3.village
             , x3.rt
             , x3.rw
             , x3.urban_village
             , x3.district_id
             , x3.residence_id
             , x3.transportation_id
             , x3.post_code
             , x3.email
             , x3.telephone
             , x3.mobile_phone
             , x3.whatsapp
             , x3.telegram
             , x3.facebook
             , x3.twitter
             , x3.instagram
             , x3.tiktok
             , x3.father_nik
             , x3.father_name
             , x3.father_birth_date
             , x3.father_education_id
             , x3.father_income_id
             , x3.mother_nik
             , x3.mother_name
             , x3.mother_birth_date
             , x3.mother_education_id
             , x3.mother_income_id
             , x3.guardian_nik
             , x3.guardian_name
             , x3.guardian_birth_date
             , x3.guardian_education_id
             , x3.guardian_occupation_id
             , x3.guardian_income_id
             , x5.name AS school
             , x6.name AS entry_grade
             , x7.name AS track
             , xperiod.registration_fee
             , xoffering.quota
             , x2.admission_offering_id
        FROM registrations x2
        LEFT JOIN users x3 ON x2.student_id = x3.id
        LEFT JOIN admission_offerings xoffering ON x2.admission_offering_id = xoffering.id
        LEFT JOIN admission_periods xperiod ON xoffering.admission_period_id = xperiod.id
        LEFT JOIN branches x5 ON xoffering.branch_id = x5.id
        LEFT JOIN entry_grades x6 ON xoffering.entry_grade_id = x6.id
        LEFT JOIN education_tracks x7 ON xoffering.education_track_id = x7.id
        WHERE x2.is_deleted = FALSE
        AND x2.admission_offering_id = ?
        ORDER BY x2.id
        ", [$admission_offering_id]);
    }

    /**
     * Find registrations by student ID, optionally filtered by admission year.
     *
     * @param int $student_id The student ID.
     * @param int $admission_year Optional admission year filter.
     * @return BaseResult|Query The query result.
     */
    public function findByStudentId(int $student_id, int $admission_year = 0): BaseResult|Query
    {
        $db = db_connect();
        $conditions = ['x1.student_id = ?'];
        $params = [$student_id];
        if ($admission_year > 0) {
            $conditions[] = 'x1.admission_year = ?';
            $params[] = $admission_year;
        }
        return $db->query("
        SELECT x1.id
            , x1.admission_year
            , x1.registration_number
            , x1.admission_fee
            , x1.submitted
            , x1.submitted_at
            , x1.transfer_proof
            , x1.transfer_proof_verified_at
            , CASE WHEN x1.admission_offering_id IS NOT NULL THEN 1 ELSE 0 END AS count
            , xperiod.name AS admission_period
            , (SELECT COUNT(*) FROM registration_subject_grades WHERE registration_id = x1.id) AS grade_count
            , (SELECT COUNT(*) FROM registration_documents WHERE registration_id = x1.id AND file IS NOT NULL AND file != '') AS document_count
        FROM registrations x1
        LEFT JOIN admission_offerings xoffering ON x1.admission_offering_id = xoffering.id
        LEFT JOIN admission_periods xperiod ON xoffering.admission_period_id = xperiod.id
        WHERE x1.is_deleted = FALSE
        AND " . implode(' AND ', $conditions) . '
        ORDER BY x1.id DESC
        ', $params);
    }

    /**
     * Retrieves registration details for multiple registration IDs.
     *
     * @param array $ids Array of registration IDs.
     * @return array The result rows.
     */
    public function findDetailsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $db = db_connect();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        return $db->query("
            SELECT x1.id AS registration_id
                , xbranch.name AS branch
                , xentry.name AS entry_grade
                , xtrack.name AS education_track
                , xprogram.name AS education_program
                , xapptype.name AS applicant_type
            FROM registrations x1
            LEFT JOIN admission_offerings xoffering ON x1.admission_offering_id = xoffering.id
            LEFT JOIN branches xbranch ON xoffering.branch_id = xbranch.id
            LEFT JOIN entry_grades xentry ON xoffering.entry_grade_id = xentry.id
            LEFT JOIN education_tracks xtrack ON xoffering.education_track_id = xtrack.id
            LEFT JOIN education_programs xprogram ON xoffering.education_program_id = xprogram.id
            LEFT JOIN applicant_types xapptype ON xoffering.applicant_type_id = xapptype.id
            WHERE x1.id IN ({$placeholders})
            AND x1.is_deleted = FALSE
        ", $ids)->getResult();
    }

    /**
     * Retrieve detailed registration info for a single registration ID.
     *
     * @param int $id The registration ID.
     * @return BaseResult|Query The query result.
     */
    public function findDetailsById(int $id): BaseResult|Query
    {
        $db = db_connect();
        return $db->query('
        SELECT x1.id AS record_id
            , x1.admission_offering_id
            , x1.submitted
            , x1.submitted_at
            , x1.admission_year
            , xbranch.name AS branch
            , xentry.name AS entry_grade
            , xtrack.name AS education_track
            , xprogram.name AS education_program
            , xapptype.name AS applicant_type
            , xperiod.registration_fee
            , xoffering.quota
        FROM registrations x1
        LEFT JOIN admission_offerings xoffering ON x1.admission_offering_id = xoffering.id
        LEFT JOIN admission_periods xperiod ON xoffering.admission_period_id = xperiod.id
        LEFT JOIN branches xbranch ON xoffering.branch_id = xbranch.id
        LEFT JOIN entry_grades xentry ON xoffering.entry_grade_id = xentry.id
        LEFT JOIN education_tracks xtrack ON xoffering.education_track_id = xtrack.id
        LEFT JOIN education_programs xprogram ON xoffering.education_program_id = xprogram.id
        LEFT JOIN applicant_types xapptype ON xoffering.applicant_type_id = xapptype.id
        WHERE x1.id = ?
        AND x1.is_deleted = FALSE
        ', [$id]);
    }

    /**
     * Generate a unique registration number for a given admission year.
     *
     * Uses a MySQL GET_LOCK to ensure sequential numbering.
     *
     * @param int $admission_year The admission year.
     * @return string The generated registration number.
     */
    public function generateRegistrationNumber(int $admission_year = 0): string
    {
        $db = db_connect();
        $lockName = 'registration_number_' . $admission_year;

        $db->query('SELECT GET_LOCK(?, 10)', [$lockName]);

        try {
            $row = $db->query("
            SELECT COALESCE((
                SELECT MAX(RIGHT(registration_number, 6))
                FROM registrations
                WHERE LEFT(registration_number, 4) = ?
                AND COALESCE(registration_number, '') <> ''
                AND is_deleted = FALSE
            ), '0') AS max_number
            ", [(string)$admission_year])->getRow();
            $number = ((int)$row->max_number) + 1;
            return $admission_year . sprintf('%06s', $number);
        } finally {
            $db->query('SELECT RELEASE_LOCK(?)', [$lockName]);
        }
    }

    /**
     * Find all active (non-deleted) registrations for a student.
     *
     * @param int $student_id The student ID.
     * @param bool $exclude_zero_offering Whether to exclude registrations with no offering.
     * @return array The result rows.
     */
    public function findActiveByStudent(int $student_id, bool $exclude_zero_offering = true): array
    {
        $builder = $this->where('student_id', $student_id)
            ->where('is_deleted', false)
            ->orderBy('id', 'DESC');
        if ($exclude_zero_offering) {
            $builder->where('admission_offering_id >', 0);
        }
        return $builder->findAll();
    }

    /**
     * Find an active registration for a student in a specific admission year.
     *
     * @param int $student_id The student ID.
     * @param int $admission_year The admission year.
     * @param bool $exclude_zero_offering Whether to exclude registrations with no offering.
     * @return object|null The registration or null if not found.
     */
    public function findActiveByStudentAndYear(int $student_id, int $admission_year, bool $exclude_zero_offering = true): ?object
    {
        $builder = $this->where('student_id', $student_id)
            ->where('admission_year', $admission_year)
            ->where('is_deleted', false)
            ->orderBy('id', 'DESC');
        if ($exclude_zero_offering) {
            $builder->where('admission_offering_id >', 0);
        }
        return $builder->first();
    }

    /**
     * Find all unsubmitted active registrations for a student.
     *
     * @param int $student_id The student ID.
     * @return array The result rows.
     */
    public function findUnsubmittedByStudent(int $student_id): array
    {
        return $this->where('student_id', $student_id)
            ->where('is_deleted', false)
            ->where('submitted', 0)
            ->where('admission_offering_id >', 0)
            ->findAll();
    }

    /**
     * Find all submitted registrations for a student.
     *
     * @param int $student_id The student ID.
     * @return array The result rows.
     */
    public function findSubmittedByStudent(int $student_id): array
    {
        return $this->where('student_id', $student_id)
            ->where('is_deleted', false)
            ->where('submitted', 1)
            ->where('admission_offering_id >', 0)
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Find a single submitted registration for a student.
     *
     * @param int $student_id The student ID.
     * @return object|null The registration or null if not found.
     */
    public function findSubmittedByStudentSingle(int $student_id): ?object
    {
        return $this->where('student_id', $student_id)
            ->where('submitted', 1)
            ->where('is_deleted', false)
            ->first();
    }

    /**
     * Find an active unsubmitted registration for a student and offering.
     *
     * @param int $student_id The student ID.
     * @param int $admission_offering_id The admission offering ID.
     * @return object|null The registration or null if not found.
     */
    public function findActiveByOffering(int $student_id, int $admission_offering_id): ?object
    {
        return $this->where('student_id', $student_id)
            ->where('admission_offering_id', $admission_offering_id)
            ->where('is_deleted', false)
            ->where('submitted', 0)
            ->first();
    }

    /**
     * Find an active registration by its ID.
     *
     * @param int $id The registration ID.
     * @return object|null The registration or null if not found.
     */
    public function findActiveById(int $id): ?object
    {
        return $this->where('id', $id)->where('is_deleted', false)->first();
    }

    /**
     * Find unsubmitted registrations for a student excluding a specific ID.
     *
     * @param int $student_id The student ID.
     * @param int $exclude_id The registration ID to exclude.
     * @return array The result rows.
     */
    public function findUnsubmittedSiblings(int $student_id, int $exclude_id): array
    {
        return $this->where('student_id', $student_id)
            ->where('is_deleted', false)
            ->where('id !=', $exclude_id)
            ->where('admission_offering_id >', 0)
            ->findAll();
    }

    /**
     * Find the latest unsubmitted registration for a student excluding a specific ID.
     *
     * @param int $student_id The student ID.
     * @param int $exclude_id The registration ID to exclude.
     * @return object|null The registration or null if not found.
     */
    public function findLatestUnsubmitted(int $student_id, int $exclude_id): ?object
    {
        return $this->where('student_id', $student_id)
            ->where('is_deleted', false)
            ->where('submitted', 0)
            ->where('id !=', $exclude_id)
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Get the latest admission year for a student's registrations.
     *
     * @param int $student_id The student ID.
     * @return int The latest admission year.
     */
    public function getLatestAdmissionYearByStudent(int $student_id): int
    {
        $rows = $this->findByStudentId($student_id)->getResult();
        if (!empty($rows)) {
            $latest = $rows[0];
            foreach ($rows as $r) {
                if ((int)$r->admission_year > (int)$latest->admission_year) {
                    $latest = $r;
                }
            }
            return (int)$latest->admission_year;
        }
        return 0;
    }

    /**
     * Get the admission year for a student's first registration.
     *
     * Falls back to the current year if no registrations exist.
     *
     * @param int $student_id The student ID.
     * @return int The admission year or current year.
     */
    public function getAdmissionYearByStudent(int $student_id): int
    {
        $rows = $this->findByStudentId($student_id)->getResult();
        if (!empty($rows)) {
            return (int)$rows[0]->admission_year;
        }
        return (int)date('Y');
    }

    /**
     * Check if a student owns a registration with the given transfer proof filename.
     *
     * @param int $student_id The student ID.
     * @param string $file_name The transfer proof filename.
     * @return bool True if the student owns the file.
     */
    public function ownsTransferProof(int $student_id, string $file_name): bool
    {
        return $this->where('student_id', $student_id)
            ->where('is_deleted', false)
            ->where('transfer_proof', $file_name)
            ->countAllResults() > 0;
    }

    /**
     * Get all admission offering IDs selected by a student in unsubmitted registrations.
     *
     * @param int $student_id The student ID.
     * @return array Array of offering IDs.
     */
    public function getSelectedOfferingIds(int $student_id): array
    {
        $rows = $this->select('admission_offering_id')
            ->where('student_id', $student_id)
            ->where('is_deleted', false)
            ->where('submitted', 0)
            ->where('admission_offering_id >', 0)
            ->findAll();
        return array_map(fn($r) => (int)$r->admission_offering_id, $rows);
    }

    /**
     * Check if a student has a submitted registration in the latest admission year.
     *
     * @param int $student_id The student ID.
     * @return bool True if registration is locked (already submitted).
     */
    public function isRegistrationLocked(int $student_id): bool
    {
        $rows = $this->findByStudentId($student_id)->getResult();
        if (empty($rows)) return false;
        $latestYear = max(array_map(fn($r) => (int)$r->admission_year, $rows));
        foreach ($rows as $r) {
            if ((int)$r->admission_year === $latestYear && (int)$r->submitted === 1) return true;
        }
        return false;
    }
}
