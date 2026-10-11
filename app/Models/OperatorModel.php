<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class OperatorModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'full_name', 'email', 'password', 'is_operator',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';

    /**
     * Retrieve all operator users, filtered to only active operators.
     *
     * @param int|null $limit  Maximum number of records to return.
     * @param int      $offset Number of records to skip.
     * @return array The operator records.
     */
    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $this->select('id, unique_id, full_name, email, is_deleted');
        $this->where('is_operator', true);
        $this->orderBy('full_name');

        return parent::findAll($limit, $offset);
    }
}
