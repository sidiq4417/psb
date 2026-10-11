<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class AdmissionPathwayModel
 *
 * Handles database operations for admission pathways.
 *
 * @package App\Models
 */
class AdmissionPathwayModel extends Model
{
    protected $table = 'admission_pathways';
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
