<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class ApplicantTypeModel
 *
 * Handles database operations for applicant types.
 *
 * @package App\Models
 */
class ApplicantTypeModel extends Model
{
    protected $table = 'applicant_types';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'name', 'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';
}
