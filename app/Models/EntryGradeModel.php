<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class EntryGradeModel
 *
 * Handles database operations for entry grades.
 *
 * @package App\Models
 */
class EntryGradeModel extends Model
{
    protected $table = 'entry_grades';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'branch_id', 'code', 'name', 'grade_number', 'sort_order',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';
}
