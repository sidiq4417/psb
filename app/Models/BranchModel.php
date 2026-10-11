<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class BranchModel
 *
 * Handles database operations for school branches.
 *
 * @package App\Models
 */
class BranchModel extends Model
{
    protected $table = 'branches';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'foundation_id', 'code', 'name', 'education_level',
        'school_type', 'address', 'phone', 'email',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';
}
