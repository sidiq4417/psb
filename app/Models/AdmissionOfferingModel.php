<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Database\Query;
use CodeIgniter\Model;

/**
 * Handles database operations for admission offerings.
 *
 * @package App\Models
 */
class AdmissionOfferingModel extends Model
{
    protected $table = 'admission_offerings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'admission_period_id', 'branch_id', 'entry_grade_id',
        'education_track_id', 'education_program_id', 'applicant_type_id',
        'admission_pathway_id', 'quota', 'sort_order',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';

    /**
     * Retrieves all admission offerings with joined details and registrant counts.
     *
     * @param int $academic_year_id Filter by academic year ID.
     * @param int $admission_period_id Filter by admission period ID.
     * @param int $branch_id Filter by branch ID.
     * @return BaseResult|Query The database result object.
     */
    public function findAllWithDetails(int $academic_year_id = 0, int $admission_period_id = 0, int $branch_id = 0): BaseResult|Query
    {
        $db = db_connect();
        $conditions = [];
        $params = [];
        if ($academic_year_id > 0) {
            $conditions[] = 'x2.academic_year_id = ?';
            $params[] = $academic_year_id;
        }
        if ($admission_period_id > 0) {
            $conditions[] = 'x1.admission_period_id = ?';
            $params[] = $admission_period_id;
        }
        if ($branch_id > 0) {
            $conditions[] = 'x1.branch_id = ?';
            $params[] = $branch_id;
        }
        $where = count($conditions) > 0 ? ' AND ' . implode(' AND ', $conditions) : '';

        return $db->query("
        SELECT x1.id
            , x1.is_deleted
            , x1.modified_at
            , x1.admission_period_id
            , x1.branch_id
            , x1.entry_grade_id
            , x1.education_track_id
            , x1.education_program_id
            , x1.applicant_type_id
            , x1.admission_pathway_id
            , x1.quota
            , x1.sort_order
            , x2.name AS period_name
            , x3.name AS school_name
            , x4.name AS grade_name
            , x5.name AS track_name
            , COALESCE(x6.name, '-') AS program_name
            , x8.name AS applicant_type_name
            , COALESCE(x9.name, '-') AS pathway_name
            , COALESCE(agg.total_registrant, 0) AS total_registrant
            , COALESCE(agg.total_submitted, 0) AS total_submitted
        FROM admission_offerings x1
        LEFT JOIN admission_periods x2 ON x1.admission_period_id = x2.id
        LEFT JOIN branches x3 ON x1.branch_id = x3.id
        LEFT JOIN entry_grades x4 ON x1.entry_grade_id = x4.id
        LEFT JOIN education_tracks x5 ON x1.education_track_id = x5.id
        LEFT JOIN education_programs x6 ON x1.education_program_id = x6.id
        LEFT JOIN applicant_types x8 ON x1.applicant_type_id = x8.id
        LEFT JOIN admission_pathways x9 ON x1.admission_pathway_id = x9.id
        LEFT JOIN (
            SELECT admission_offering_id
                , COUNT(id) AS total_registrant
                , SUM(CASE WHEN submitted = TRUE THEN 1 ELSE 0 END) AS total_submitted
            FROM registrations
            GROUP BY admission_offering_id
        ) agg ON x1.id = agg.admission_offering_id
        WHERE 1=1
        $where
        ORDER BY x1.id
        ", $params);
    }

    /**
     * Finds a single admission offering by its ID with related names.
     *
     * @param int $id The offering ID.
     * @return object|false|null The offering object, false on failure, or null if not found.
     */
    public function findById(int $id): object|false|null
    {
        $db = db_connect();
        return $db->query("
        SELECT x1.id
            , x1.admission_period_id
            , x1.branch_id
            , x1.entry_grade_id
            , x1.education_track_id
            , x1.education_program_id
            , x1.applicant_type_id
            , x1.admission_pathway_id
            , x1.quota
            , x2.registration_fee
            , x2.name AS period_name
            , x3.name AS school_name
            , x4.name AS grade_name
            , x5.name AS track_name
            , COALESCE(x6.name, '-') AS program_name
            , x8.name AS applicant_type_name
            , COALESCE(x9.name, '-') AS pathway_name
            , x7.name AS academic_year_name
        FROM admission_offerings x1
        LEFT JOIN admission_periods x2 ON x1.admission_period_id = x2.id
        LEFT JOIN branches x3 ON x1.branch_id = x3.id
        LEFT JOIN entry_grades x4 ON x1.entry_grade_id = x4.id
        LEFT JOIN education_tracks x5 ON x1.education_track_id = x5.id
        LEFT JOIN education_programs x6 ON x1.education_program_id = x6.id
        LEFT JOIN applicant_types x8 ON x1.applicant_type_id = x8.id
        LEFT JOIN admission_pathways x9 ON x1.admission_pathway_id = x9.id
        LEFT JOIN academic_years x7 ON x2.academic_year_id = x7.id
        WHERE x1.id = ?
        ", [$id])->getRow();
    }

    /**
     * Retrieves active public offerings filtered by branch and/or admission period.
     *
     * @param int $branch_id Filter by branch ID.
     * @param int $admission_period_id Filter by admission period ID.
     * @return BaseResult|Query The database result object.
     */
    public function findPublicList(int $branch_id = 0, int $admission_period_id = 0): BaseResult|Query
    {
        $db = db_connect();
        $conditions = [];
        $params = [];
        if ($branch_id > 0) {
            $conditions[] = 'x1.branch_id = ?';
            $params[] = $branch_id;
        }
        if ($admission_period_id > 0) {
            $conditions[] = 'x1.admission_period_id = ?';
            $params[] = $admission_period_id;
        }
        $where = count($conditions) > 0 ? ' AND ' . implode(' AND ', $conditions) : '';
        // Today in the application time zone (App::$appTimezone), not the DB session time zone.
        array_unshift($params, date('Y-m-d'));

        return $db->query("
            SELECT x1.id
                , xperiod.name AS period_name
                , xbranch.name AS school_name
                , xentry.name AS grade_name
                , xtrack.name AS track_name
                , xprogram.name AS program_name
                , xapptype.name AS applicant_type_name
                , xpathway.name AS pathway_name
                , x1.quota
                , xperiod.registration_fee
                , x1.sort_order
            FROM admission_offerings x1
            LEFT JOIN admission_periods xperiod ON x1.admission_period_id = xperiod.id
            LEFT JOIN branches xbranch ON x1.branch_id = xbranch.id
            LEFT JOIN entry_grades xentry ON x1.entry_grade_id = xentry.id
            LEFT JOIN education_tracks xtrack ON x1.education_track_id = xtrack.id
            LEFT JOIN education_programs xprogram ON x1.education_program_id = xprogram.id
            LEFT JOIN applicant_types xapptype ON x1.applicant_type_id = xapptype.id
            LEFT JOIN admission_pathways xpathway ON x1.admission_pathway_id = xpathway.id
            WHERE x1.is_deleted = FALSE
            AND xperiod.is_deleted = FALSE
            AND ? BETWEEN xperiod.registration_start_at AND xperiod.registration_end_at
            $where
            ORDER BY x1.sort_order, x1.id
        ", $params);
    }

    /**
     * Find an offering that is currently open for registration.
     *
     * Uses the same conditions as findPublicList(): the offering and its
     * admission period are not deleted and today falls within the period's
     * registration_start_at..registration_end_at window. "Today" is taken
     * from PHP in the application time zone (App::$appTimezone) rather than
     * the database CURRENT_DATE, whose session time zone may differ (e.g. UTC).
     *
     * @param int $id The offering ID.
     * @return object|null The offering with its period data, or null when not open.
     */
    public function findOpenById(int $id): ?object
    {
        if ($id <= 0) {
            return null;
        }
        $db = db_connect();
        $row = $db->query('
            SELECT x1.id
                , x1.admission_period_id
                , xperiod.academic_year_id
                , xperiod.registration_fee
                , xperiod.registration_start_at
                , xperiod.registration_end_at
            FROM admission_offerings x1
            JOIN admission_periods xperiod ON x1.admission_period_id = xperiod.id
            WHERE x1.id = ?
            AND x1.is_deleted = FALSE
            AND xperiod.is_deleted = FALSE
            AND ? BETWEEN xperiod.registration_start_at AND xperiod.registration_end_at
            LIMIT 1
        ', [$id, date('Y-m-d')])->getRow();
        return is_object($row) ? $row : null;
    }

    /**
     * Check whether an offering is currently open for registration.
     *
     * @param int $id The offering ID.
     * @return bool True when the offering can be registered/edited today.
     */
    public function isOpen(int $id): bool
    {
        return $this->findOpenById($id) !== null;
    }

    /**
     * Get the academic year ID of an offering through its admission period.
     *
     * @param int $id The offering ID.
     * @return int The academic year ID, or 0 if not found.
     */
    public function getAcademicYearId(int $id): int
    {
        if ($id <= 0) {
            return 0;
        }
        $db = db_connect();
        $row = $db->query('
            SELECT xperiod.academic_year_id
            FROM admission_offerings x1
            JOIN admission_periods xperiod ON x1.admission_period_id = xperiod.id
            WHERE x1.id = ?
            LIMIT 1
        ', [$id])->getRow();
        return (int)($row->academic_year_id ?? 0);
    }

    /**
     * Get education level for a given offering.
     *
     * @param int $offering_id The offering ID.
     * @return string The education level, or empty string if not found.
     */
    public function getEducationLevel(int $offering_id): string
    {
        $db = db_connect();
        $row = $db->query('
            SELECT x3.education_level
            FROM admission_offerings x1
            JOIN branches x3 ON x1.branch_id = x3.id
            WHERE x1.id = ?
        ', [$offering_id])->getRow();
        return $row->education_level ?? '';
    }

    /**
     * Map an education level to its equivalency group.
     *
     * Levels within the same group are considered setara (equivalent).
     *
     * @param string $education_level Raw level (e.g. SD, MI, SMP, etc.)
     * @return string Group identifier (primary, junior, senior, or the raw value as fallback).
     */
    public static function getLevelGroup(string $education_level): string
    {
        $groups = [
            'primary' => ['SD', 'MI'],
            'junior'  => ['SMP', 'MTs'],
            'senior'  => ['SMA', 'MA', 'SMK'],
            'early'   => ['TK', 'RA'],
        ];

        foreach ($groups as $group => $levels) {
            if (in_array($education_level, $levels, true)) {
                return $group;
            }
        }

        return $education_level;
    }
}
