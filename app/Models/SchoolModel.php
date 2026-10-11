<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class SchoolModel
 *
 * Handles database operations for previous schools.
 *
 * @package App\Models
 */
class SchoolModel extends Model
{
    protected $table = 'previous_schools';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'school_name', 'school_address',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';
}
