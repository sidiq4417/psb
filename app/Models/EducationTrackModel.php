<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class EducationTrackModel
 *
 * Handles database operations for education tracks.
 *
 * @package App\Models
 */
class EducationTrackModel extends Model
{
    protected $table = 'education_tracks';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'branch_id', 'code', 'name', 'description', 'is_default',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';
}
