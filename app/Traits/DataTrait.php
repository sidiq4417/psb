<?php

declare(strict_types=1);

namespace App\Traits;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * Trait DataTrait
 *
 * Provides common CRUD helper methods (form, delete, restore)
 * for controllers that work with a single database table.
 *
 * @property int    $id
 * @property string $table
 * @property ResponseInterface $response
 *
 * @package App\Traits
 */
trait DataTrait
{
    /**
     * Returns JSON response with form data for a given record.
     *
     * @return ResponseInterface
     */
    public function form(): ResponseInterface
    {
        $db = db_connect();
        $vars = ['id' => 0, 'unique_id' => '', 'is_deleted' => 0];

        if ($this->id > 0) {
            if (session()->get('is_student') && $this->table === 'users' && $this->id !== (int)session()->get('actor')) {
                return $this->response->setJSON($vars);
            }
            $row = $db->table($this->table)->where('id', $this->id)->get()->getRow();
            if ($row) {
                $vars = (array)$row;
                $vars['is_deleted'] = (int)($row->is_deleted ?? 0);
            }
        }

        unset($vars['password']);

        return $this->response->setJSON($vars);
    }

    /**
     * Marks a record as deleted (soft delete) and returns a JSON response.
     *
     * @return ResponseInterface
     */
    public function delete(): ResponseInterface
    {
        $db = db_connect();
        $actor = (int)session()->get('actor');
        $db->table($this->table)
            ->where('id', $this->id)
            ->update([
                'is_deleted' => 1,
                'deleted_by' => $actor,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);
        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Data has been marked as deleted.',
        ]);
    }

    /**
     * Restores a soft-deleted record and returns a JSON response.
     *
     * @return ResponseInterface
     */
    public function restore(): ResponseInterface
    {
        $db = db_connect();
        $db->table($this->table)
            ->where('id', $this->id)
            ->update([
                'is_deleted' => 0,
                'deleted_by' => 0,
                'deleted_at' => null,
            ]);
        return $this->response->setJSON([
            'id' => $this->id,
            'success' => true,
            'message' => 'Data has been restored successfully.',
        ]);
    }
}
