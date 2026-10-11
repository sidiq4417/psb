<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class AcademicYearModel
 *
 * Handles database operations for academic years.
 *
 * @package App\Models
 */
class AcademicYearModel extends Model
{
    protected $table = 'academic_years';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $allowedFields = [
        'unique_id', 'foundation_id', 'name', 'start_date', 'end_date',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    /**
     * Find the first non-deleted academic year ID whose name contains the given string.
     *
     * @param string $name A substring to match against the academic year name.
     * @return int The academic year ID, or 0 if not found.
     */
    public function findIdByNameLike(string $name): int
    {
        $row = $this->like('name', $name)->where('is_deleted', false)->first();
        return $row ? (int)$row->id : 0;
    }

    /**
     * Get the academic year name for a given admission year.
     *
     * @param int $year The admission year (first 4 digits of the academic year name).
     * @return string The academic year name, or the year itself if not found.
     */
    public function getNameByAdmissionYear(int $year): string
    {
        $db = db_connect();
        $row = $db->query('
            SELECT name
            FROM academic_years
            WHERE LEFT(name, 4) = ?
            AND is_deleted = FALSE
            LIMIT 1
        ', [(string) $year])->getRow();
        return $row->name ?? (string) $year;
    }
}
