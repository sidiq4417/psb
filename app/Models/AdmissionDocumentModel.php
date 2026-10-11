<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class AdmissionDocumentModel
 *
 * Handles database operations for admission document types.
 *
 * @package App\Models
 */
class AdmissionDocumentModel extends Model
{
    protected $table = 'admission_documents';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'name', 'max_size', 'is_deleted',
        'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';
}
