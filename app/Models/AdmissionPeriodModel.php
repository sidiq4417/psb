<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class AdmissionPeriodModel
 *
 * Handles database operations for admission periods.
 *
 * @package App\Models
 */
class AdmissionPeriodModel extends Model
{
    protected $table = 'admission_periods';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'foundation_id', 'academic_year_id', 'code', 'name',
        'registration_start_at', 'registration_end_at',
        'registration_fee',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';

    /**
     * Get the current active admission year.
     *
     * The admission year is derived from the first 4 digits of the linked
     * academic year name (e.g. "2027/2028" -> 2027).
     *
     * @return int The active admission year, or current year if none active.
     */
    public function getActiveAdmissionYear(): int
    {
        $db = db_connect();
        $activeAdmission = $db->query('
            SELECT LEFT(x2.name, 4) AS admission_year
            FROM admission_periods x1
            JOIN academic_years x2 ON x1.academic_year_id = x2.id
            WHERE CURRENT_DATE BETWEEN x1.registration_start_at AND x1.registration_end_at
            LIMIT 1
        ')->getRow();
        return (int) ($activeAdmission->admission_year ?? date('Y'));
    }

    /**
     * Get the admission year of a specific admission period.
     *
     * Derived from the first 4 digits of the linked academic year name.
     *
     * @param int $period_id The admission period ID.
     * @return int The admission year, or current year if period not found.
     */
    public function getAdmissionYearByPeriodId(int $period_id): int
    {
        $db = db_connect();
        $row = $db->query('
            SELECT LEFT(x2.name, 4) AS admission_year
            FROM admission_periods x1
            JOIN academic_years x2 ON x1.academic_year_id = x2.id
            WHERE x1.id = ?
            LIMIT 1
        ', [$period_id])->getRow();
        return (int) ($row->admission_year ?? date('Y'));
    }

    /**
     * Get all distinct admission years that have an admission period.
     *
     * @return array<int, int> Distinct admission years sorted descending.
     */
    public function getAllAdmissionYears(): array
    {
        $db = db_connect();
        $rows = $db->query('
            SELECT DISTINCT LEFT(x2.name, 4) AS admission_year
            FROM admission_periods x1
            JOIN academic_years x2 ON x1.academic_year_id = x2.id
            WHERE x1.is_deleted = FALSE
            ORDER BY admission_year DESC
        ')->getResult();
        return array_map(fn($row) => (int) $row->admission_year, $rows);
    }

    /**
     * Get the academic year name of the current active admission period.
     *
     * @return string The active academic year name, or current year if none active.
     */
    public function getActiveAcademicYearName(): string
    {
        $db = db_connect();
        $activeAcademicYear = $db->query('
            SELECT x2.name AS academic_year_name
            FROM admission_periods x1
            JOIN academic_years x2 ON x1.academic_year_id = x2.id
            WHERE CURRENT_DATE BETWEEN x1.registration_start_at AND x1.registration_end_at
            LIMIT 1
        ')->getRow();
        return $activeAcademicYear->academic_year_name ?? (string) date('Y');
    }
}
