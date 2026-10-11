<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Config\Setting;
use Exception;
use Random\RandomException;

/**
 * Handles 'Remember Me' authentication token generation, validation, and management.
 *
 * @package App\Libraries
 */
class Remember
{
    protected BaseConnection $db;

    /**
     * Initializes database connection and loads cookie helper.
     */
    public function __construct()
    {
        $this->db = Database::connect();
        helper('cookie');
    }

    /**
     * Creates a "remember me" token for the given user and sets it as a cookie.
     *
     * @param int $actor
     *
     * @return void
     */
    public function rememberMe(int $actor): void
    {
        $day = Setting::REMEMBER_LENGTH;
        [$selector, $validator, $token] = $this->generateTokens();
        $this->deleteUserToken($actor);
        $hashed_validator = password_hash($validator, PASSWORD_BCRYPT);
        $expiry = date('Y-m-d H:i:s', time() + ($day * 86400));
        if ($this->updateRememberUserToken($actor, $selector, $hashed_validator, $expiry)) {
            set_cookie(Setting::REMEMBER_COOKIE_NAME, $token, 3600 * 24 * $day, '', '/', '', true, true, 'Lax');
        }
    }

    /**
     * Generates cryptographically secure selector, validator, and combined token.
     *
     * @return array [selector, validator, token]
     */
    private function generateTokens(): array
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));
        return [$selector, $validator, $selector . ':' . $validator];
    }

    /**
     * Splits a combined token into selector and validator parts.
     *
     * @param string $token
     *
     * @return array|null
     */
    private function parseToken(string $token): ?array
    {
        $parts = explode(':', $token);
        if ($parts && count($parts) == 2) {
            return [$parts[0], $parts[1]];
        }
        return null;
    }

    /**
     * Validates a remember token against the stored hash.
     *
     * @param string $token
     *
     * @return bool
     */
    public function isValidToken(string $token): bool
    {
        $parsed = $this->parseToken($token);
        if (!$parsed) {
            return false;
        }
        [$selector, $validator] = $parsed;

        $tokens = $this->findUserTokenBySelector($selector);
        if (is_null($tokens)) {
            return false;
        }
        return password_verify($validator, $tokens['hashed_validator']);
    }

    /**
     * Removes the remember token for a user from the database and cookie.
     *
     * @param int $actor
     *
     * @return void
     */
    public function deleteUserToken(int $actor): void
    {
        $this->db->table('users')
            ->where('id', $actor)
            ->update([
                'selector' => '',
                'hashed_validator' => '',
                'expiry' => null,
            ]);
        delete_cookie(Setting::REMEMBER_COOKIE_NAME);
    }

    /**
     * Finds a user ID by parsing and looking up the remember token.
     *
     * @param string $token
     *
     * @return int
     */
    public function findUserIdByToken(string $token): int
    {
        $parsed = $this->parseToken($token);
        if (!$parsed) {
            return 0;
        }
        return $this->findUserIdBySelector($parsed[0]);
    }

    /**
     * Queries the database for a user ID by selector.
     *
     * @param string $selector
     *
     * @return int
     */
    private function findUserIdBySelector(string $selector): int
    {
        $row = $this->db->table('users')
            ->select('id')
            ->where('selector', $selector)
            ->get()
            ->getRow();
        return $row->id ?? 0;
    }

    /**
     * Retrieves stored token data by selector.
     *
     * @param string $selector
     *
     * @return array|null
     */
    private function findUserTokenBySelector(string $selector): ?array
    {
        $row = $this->db->table('users')
            ->select('id, selector, hashed_validator, expiry')
            ->where('selector', $selector)
            ->where('expiry >=', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    /**
     * Stores a new remember token for a user.
     *
     * @param int    $actor
     * @param string $selector
     * @param string $hashed_validator
     * @param string $expiry
     *
     * @return bool
     */
    private function updateRememberUserToken(int $actor, string $selector, string $hashed_validator, string $expiry): bool
    {
        $this->db->table('users')
            ->where('id', $actor)
            ->update([
                'selector' => $selector,
                'hashed_validator' => $hashed_validator,
                'expiry' => $expiry,
            ]);
        return $this->db->affectedRows() > 0;
    }
}
