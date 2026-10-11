<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Model;
use Config\Database;

/**
 * Provides statistical data queries for admission reports.
 *
 * @package App\Models
 */
class AdmissionStatisticModel extends Model
{
    protected BaseConnection $dbs;

    protected $table = 'registrations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'student_id', 'admission_offering_id', 'admission_year', 'submitted',
        'selection_status', 'is_deleted',
    ];
    protected $returnType = 'object';

    /**
     * Constructor. Initializes the custom database connection.
     */
    public function __construct()
    {
        parent::__construct();
        $this->dbs = Database::connect();
    }

    /**
     * Builds the base JOIN clause used by multiple statistics queries.
     *
     * @return string The SQL JOIN fragment.
     */
    private function getBaseJoin(): string
    {
        return '
            FROM registrations x1
            LEFT JOIN admission_offerings xoffering ON x1.admission_offering_id = xoffering.id
            LEFT JOIN branches xbranch ON xoffering.branch_id = xbranch.id
            LEFT JOIN entry_grades xentry ON xoffering.entry_grade_id = xentry.id
            LEFT JOIN education_tracks xtrack ON xoffering.education_track_id = xtrack.id
            LEFT JOIN admission_pathways xpathway ON xoffering.admission_pathway_id = xpathway.id
        ';
    }

    /**
     * Returns registration counts grouped by campus (branch).
     *
     * @param int $year The admission year.
     * @return array The result rows.
     */
    public function getStatsByCampus(int $year): array
    {
        return $this->dbs->query("
            SELECT COALESCE(xbranch.name, 'Tidak Diketahui') AS label, COUNT(x1.id) AS total
            " . $this->getBaseJoin() . '
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY xbranch.name
            ORDER BY total DESC
        ', [$year])->getResult();
    }

    /**
     * Returns registration counts grouped by selection status.
     *
     * @param int $year The admission year.
     * @return array The result rows.
     */
    public function getStatsByStatus(int $year): array
    {
        return $this->dbs->query("
            SELECT COALESCE(x1.selection_status, '0') AS label, COUNT(x1.id) AS total
            " . $this->getBaseJoin() . '
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY x1.selection_status
            ORDER BY total DESC
        ', [$year])->getResult();
    }

    /**
     * Returns registration counts grouped by major (entry grade).
     *
     * @param int $year The admission year.
     * @return array The result rows.
     */
    public function getStatsByMajor(int $year): array
    {
        return $this->dbs->query("
            SELECT COALESCE(xentry.name, 'Tidak Memilih') AS label, COUNT(x1.id) AS total
            " . $this->getBaseJoin() . '
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY xentry.name
            ORDER BY total DESC
        ', [$year])->getResult();
    }

    /**
     * Returns registration counts grouped by class (education track).
     *
     * @param int $year The admission year.
     * @return array The result rows.
     */
    public function getStatsByClass(int $year): array
    {
        return $this->dbs->query("
            SELECT COALESCE(xtrack.name, 'Tidak Diketahui') AS label, COUNT(x1.id) AS total
            " . $this->getBaseJoin() . '
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY xtrack.name
            ORDER BY total DESC
        ', [$year])->getResult();
    }

    /**
     * Returns registration counts grouped by admission path.
     *
     * @param int $year The admission year.
     * @return array The result rows.
     */
    public function getStatsByPath(int $year): array
    {
        return $this->dbs->query("
            SELECT COALESCE(xpathway.name, 'Tidak Diketahui') AS label, COUNT(x1.id) AS total
            " . $this->getBaseJoin() . '
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY xpathway.name
            ORDER BY total DESC
        ', [$year])->getResult();
    }

    /**
     * Returns registration counts grouped by applicant type.
     *
     * @param int $year The admission year.
     * @return array The result rows.
     */
    public function getStatsByType(int $year): array
    {
        return $this->dbs->query("
            SELECT COALESCE(xapptype.name, 'Tidak Diketahui') AS label, COUNT(x1.id) AS total
            FROM registrations x1
            LEFT JOIN admission_offerings xoffering ON x1.admission_offering_id = xoffering.id
            LEFT JOIN applicant_types xapptype ON xoffering.applicant_type_id = xapptype.id
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY xapptype.name
            ORDER BY total DESC
        ", [$year])->getResult();
    }

    /**
     * Returns registration counts grouped by school of origin.
     *
     * @param int $year The admission year.
     * @param int $limit Maximum number of results (default 10).
     * @return array The result rows.
     */
    public function getStatsBySchool(int $year, int $limit = 10): array
    {
        return $this->dbs->query("
            SELECT COALESCE(x2.school_name, 'Tidak Diketahui') AS label, COUNT(x1.id) AS total
            FROM registrations x1
            LEFT JOIN users x2 ON x1.student_id = x2.id
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY x2.school_name
            ORDER BY total DESC
            LIMIT ?
        ", [$year, $limit])->getResult();
    }

    /**
     * Returns registration counts grouped by province (district).
     *
     * @param int $year The admission year.
     * @return array The result rows.
     */
    public function getStatsByProvince(int $year): array
    {
        return $this->dbs->query("
            SELECT COALESCE(x2.district_id, '0') AS province_id, COUNT(x1.id) AS total
            FROM registrations x1
            LEFT JOIN users x2 ON x1.student_id = x2.id
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY x2.district_id
        ", [$year])->getResult();
    }

    /**
     * Returns monthly registration counts for a given year.
     *
     * @param int $year The admission year.
     * @return array The result rows.
     */
    public function getStatsByMonth(int $year): array
    {
        return $this->dbs->query('
            SELECT months.m AS month_index, COUNT(r.id) AS total
            FROM (
                SELECT 1 AS m UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
            ) AS months
            LEFT JOIN registrations r ON MONTH(r.created_at) = months.m
                AND YEAR(r.created_at) = ?
                AND r.is_deleted = FALSE
            GROUP BY months.m
            ORDER BY months.m ASC
        ', [$year])->getResult();
    }

    /**
     * Return registration counts grouped by student gender.
     *
     * @param int $year The admission year.
     * @return array The result rows with label and total.
     */
    public function getStatsByGender(int $year): array
    {
        return $this->dbs->query("
            SELECT COALESCE(x2.gender, 'M') AS label, COUNT(x1.id) AS total
            FROM registrations x1
            LEFT JOIN users x2 ON x1.student_id = x2.id
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY x2.gender
        ", [$year])->getResult();
    }

    /**
     * Return registration counts grouped by father's income level.
     *
     * @param int $year The admission year.
     * @return array The result rows with label and total.
     */
    public function getStatsByIncome(int $year): array
    {
        return $this->dbs->query("
            SELECT COALESCE(x2.father_income_id, '0') AS label, COUNT(x1.id) AS total
            FROM registrations x1
            LEFT JOIN users x2 ON x1.student_id = x2.id
            WHERE x1.admission_year = ? AND x1.is_deleted = FALSE
            GROUP BY x2.father_income_id
            ORDER BY total DESC
        ", [$year])->getResult();
    }
}
