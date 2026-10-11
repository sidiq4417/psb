<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Database\Query;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unique_id', 'email', 'password', 'full_name', 'gender', 'birth_place',
        'birth_date', 'nik', 'npwp', 'citizenship', 'mother_name', 'religion_id',
        'kps_receiver', 'kps', 'mobile_phone', 'address', 'address_country_id', 'address_province_id',
        'address_district_id', 'address_subdistrict_id', 'street_address',
        'village', 'rt', 'rw',
        'urban_village', 'district_id', 'residence_id', 'transportation_id',
        'post_code', 'telephone', 'whatsapp', 'telegram', 'facebook', 'twitter',
        'instagram', 'tiktok', 'is_admin', 'is_student', 'is_operator',
        'account_verified', 'account_verified_at', 'account_verified_by',
        'password_reset_token', 'password_reset_token_request_at',
        'nisn', 'npsn', 'previous_school_id', 'school_name', 'school_major',
        'school_graduation_year', 'school_address',
        'father_nik', 'father_name', 'father_birth_date',
        'father_education_id', 'father_income_id', 'father_occupation_id',
        'mother_nik', 'mother_name', 'mother_birth_date',
        'mother_education_id', 'mother_income_id', 'mother_occupation_id',
        'guardian_nik', 'guardian_name', 'guardian_birth_date',
        'guardian_education_id', 'guardian_occupation_id', 'guardian_income_id',
        'is_deleted', 'created_by', 'updated_by', 'deleted_by', 'restored_by',
        'restored_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';
    protected $returnType = 'object';

    /**
     * Update a user record, automatically setting the updated_by field.
     *
     * @param int   $id   The user ID.
     * @param array $data The fields to update.
     * @return bool True on success.
     */
    public function updateUser(int $id, array $data): bool
    {
        $data['updated_by'] = session()->get('actor') ?? $id;
        return $this->db->table('users')
            ->where('id', $id)
            ->update($data);
    }

    /**
     * Find a user by email for authentication purposes.
     *
     * @param string $email The user email.
     * @return BaseResult|Query The query result containing id, password, is_student, account_verified.
     */
    public function findByCredentials(string $email): BaseResult|Query
    {
        $db = db_connect();
        return $db->table('users')
            ->select('id, password, is_student, account_verified')
            ->where('is_deleted', false)
            ->where('email', $email)
            ->orderBy('id')
            ->limit(1)
            ->get();
    }

    /**
     * Get the user ID for a non-deleted user matching the given email.
     *
     * @param string $email The user email.
     * @return int The user ID, or 0 if not found.
     */
    public function findUserIdByEmail(string $email): int
    {
        $db = db_connect();
        $row = $db->table('users')
            ->select('id')
            ->where('is_deleted', false)
            ->where('email', $email)
            ->get()
            ->getRow();
        return $row->id ?? 0;
    }

    /**
     * Find a user by verifying a password reset token.
     *
     * Only matches tokens requested within the last hour.
     *
     * @param string $password_reset_token The plaintext token to verify.
     * @return int The user ID, or 0 if not found or expired.
     */
    public function findUserIdByPasswordRecoveryToken(string $password_reset_token): int
    {
        $db = db_connect();
        $rows = $db->table('users')
            ->select('id, password_reset_token')
            ->where('is_deleted', false)
            ->where('password_reset_token IS NOT NULL')
            ->where('password_reset_token_request_at IS NOT NULL')
            ->where('password_reset_token_request_at >=', date('Y-m-d H:i:s', time() - 3600))
            ->get()
            ->getResult();
        foreach ($rows as $row) {
            if (password_verify($password_reset_token, $row->password_reset_token)) {
                return (int)$row->id;
            }
        }
        return 0;
    }

    /**
     * Check if an email is already used by a different non-deleted user.
     *
     * @param string $email     The email to check.
     * @param int    $exclude_id The user ID to exclude from the check.
     * @return bool True if the email exists for another user.
     */
    public function emailExistsForOtherUser(string $email, int $exclude_id): bool
    {
        $existing = $this->db->table('users')
            ->where('email', $email)
            ->where('id !=', $exclude_id)
            ->where('is_deleted', false)
            ->get()
            ->getRow();
        return (bool)$existing;
    }
}
