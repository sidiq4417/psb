<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Class Setting
 *
 * Application-wide authentication and security configuration.
 * Defines constants for password policies, "remember me" functionality,
 * and login attempt restrictions.
 */
class Setting extends BaseConfig
{
    // -------------------------------------------------------------------------
    // Password configurations
    // -------------------------------------------------------------------------

    /**
     * Minimum required password length.
     */
    public const int MIN_PASSWORD_LENGTH = 8;

    /**
     * Maximum password size in bytes.
     * Prevents excessively long inputs that could cause performance issues.
     */
    public const int MAX_PASSWORD_SIZE_BYTES = 4096;

    // -------------------------------------------------------------------------
    // "Remember me" login configurations
    // -------------------------------------------------------------------------

    /**
     * Whether the system allows the use of "remember me" functionality.
     */
    public const bool ALLOW_REMEMBERING = true;

    /**
     * Duration (in days) to remember a user login.
     * Default: 30 days.
     */
    public const int REMEMBER_LENGTH = 30;

    /**
     * Cookie name used for "remember me" functionality.
     */
    public const string REMEMBER_COOKIE_NAME = 'remember_me';

    // -------------------------------------------------------------------------
    // Login attempt configurations
    // -------------------------------------------------------------------------

    /**
     * Maximum number of failed login attempts before lockout.
     */
    public const int MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Lockout duration (in minutes) after exceeding max login attempts.
     */
    public const int LOCKOUT_TIME = 15;

    /**
     * Cost factor for password hashing (e.g., bcrypt).
     * Higher values increase security but also CPU usage.
     */
    public const int PASSWORD_COST = 12;
}
