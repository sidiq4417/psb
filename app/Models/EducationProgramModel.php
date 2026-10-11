<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class EducationProgramModel
 *
 * Handles database operations for education programs.
 *
 * @package App\Models
 */
class EducationProgramModel extends Model
{
    protected $table = 'education_programs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'branch_id', 'code', 'name', 'category',
        'display_label', 'description',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';
}
