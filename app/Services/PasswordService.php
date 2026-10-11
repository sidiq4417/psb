<?php

namespace App\Services;

use Config\Setting;

/**
 * Class PasswordService
 *
 * Provides utilities for secure password handling, including hashing,
 * rehashing checks, and validation against complexity rules.
 *
 * This service centralizes password operations so that registration,
 * login, and password reset flows can consistently enforce security
 * standards defined in the application settings.
 */
class PasswordService
{
    /**
     * Generate a secure hash for a given password.
     *
     * Uses the BCRYPT algorithm with a configurable cost factor.
     *
     * @param string $password The plain text password to hash.
     * @return string The hashed password string.
     */
    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => Setting::PASSWORD_COST,
        ]);
    }

    /**
     * Determine if a stored password hash needs to be rehashed.
     *
     * This is useful when the cost factor or algorithm changes,
     * ensuring that older hashes are upgraded to current standards.
     *
     * @param string $hash The existing password hash.
     * @return bool True if the hash should be rehashed, false otherwise.
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, [
            'cost' => Setting::PASSWORD_COST,
        ]);
    }

    /**
     * Validate a plain text password against complexity rules.
     *
     * Rules enforced:
     * - Minimum length defined in Setting::MIN_PASSWORD_LENGTH
     * - Maximum length defined in Setting::MAX_PASSWORD_SIZE_BYTES
     * - Must contain at least one uppercase letter
     * - Must contain at least one lowercase letter
     * - Must contain at least one digit
     * - Must contain at least one symbol (non-word character)
     *
     * @param string $password The plain text password to validate.
     * @return bool True if the password meets all requirements, false otherwise.
     */
    public function isValid(string $password): bool
    {
        if (strlen($password) < Setting::MIN_PASSWORD_LENGTH) {
            return false;
        }
        if (strlen($password) > Setting::MAX_PASSWORD_SIZE_BYTES) {
            return false;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return false; // must contain uppercase
        }
        if (!preg_match('/[a-z]/', $password)) {
            return false; // must contain lowercase
        }
        if (!preg_match('/[0-9]/', $password)) {
            return false; // must contain digit
        }
        if (!preg_match('/[\W]/', $password)) {
            return false; // must contain symbol
        }
        return true;
    }
}
