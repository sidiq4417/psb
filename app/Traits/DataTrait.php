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
     * Column-name patterns that must never be sent to the browser
     * (passwords, reset/remember-me tokens, SMTP and API secrets).
     *
     * @var list<string>
     */
    private static array $secret_column_patterns = [
        '/^password$/i',
        '/^password_reset_token/i',
        '/^(selector|hashed_validator|expiry)$/i',
        '/(^|_)(remember|token)(_|$)/i',
        '/secret/i',
        '/^smtp_pass/i',
    ];

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

        return $this->response->setJSON(self::withoutSecrets($vars));
    }

    /**
     * Remove credential and secret columns from a record before it is sent to the browser.
     *
     * @param array<string, mixed> $vars The record as an associative array.
     * @return array<string, mixed> The record without secret columns.
     */
    protected static function withoutSecrets(array $vars): array
    {
        foreach (array_keys($vars) as $column) {
            foreach (self::$secret_column_patterns as $pattern) {
                if (preg_match($pattern, (string)$column) === 1) {
                    unset($vars[$column]);
                    break;
                }
            }
        }

        return $vars;
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
