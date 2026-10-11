<?php

namespace App\Libraries;

use App\Models\UserModel;
use App\Services\AccountVerificationService;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Config\Setting;
use Random\RandomException;

/**
 * Class Auth
 *
 * Provides authentication utilities including:
 * - User login verification with password hashing
 * - Session management
 * - "Remember me" functionality
 * - Role checks (admin, operator, student)
 * - Login attempt tracking and lockout
 *
 * @package App\Libraries
 */
class Auth
{
    /**
     * Remember-me handler instance.
     *
     * @var Remember
     */
    public Remember $remember;

    /**
     * Database connection instance.
     *
     * @var BaseConnection
     */
    protected BaseConnection $db;

    /**
     * Bcrypt hash of a random string, used to equalize timing for unknown emails.
     */
    private const string DUMMY_PASSWORD_HASH = '$2y$12$DC3A9LdWxv629psPM3SeKOjHAYBPV30oi0lt2uAKEZUFdoEL70zyG';

    /**
     * Auth Constructor.
     *
     * Initializes the remember-me handler and loads cookie helper.
     */
    public function __construct()
    {
        $this->db = Database::connect();
        $this->remember = new Remember();
        helper('cookie');
    }

    /**
     * Verify user credentials.
     *
     * Flow:
     * - Ensure email and password are provided.
     * - Refuse while the email/IP pair is locked out (checked before the lookup so the
     *   response does not reveal whether the email exists).
     * - Find a user by email and verify the password against the stored hash
     *   (a dummy hash is checked for unknown emails to keep the timing similar).
     * - Record failed attempts; clear them on success.
     * - Refuse unverified student accounts and re-send the verification email
     *   (at most once per AccountVerificationService::RESEND_INTERVAL).
     * - Regenerate the session ID, set session data and update last login info.
     * - Handle "remember me" if enabled.
     *
     * @param string    $email       User email/username.
     * @param string    $password    Plain text password.
     * @param bool|null $remember_me Whether to enable "remember me".
     * @param string    $ip_address  Client IP address.
     * @return object Result object with success flag, errors, or user info.
     * @throws RandomException
     */
    public function verify(string $email, string $password, ?bool $remember_me, string $ip_address): object
    {
        if (empty($email) || empty($password)) {
            return (object)[
                'success' => false,
                'errors' => [
                    'email' => 'Email dan kata sandi wajib diisi.'
                ]
            ];
        }

        // No account can have a longer email (users.email and login_attempts.identity are 255 characters).
        if (mb_strlen($email) > 255) {
            return $this->invalidCredentials();
        }

        // check max login attempts using the dedicated table
        if ($this->isAccountLocked($ip_address, $email)) {
            return (object)[
                'success' => false,
                'errors' => [
                    'email' => 'Terlalu banyak percobaan masuk. Silakan coba lagi setelah ' . Setting::LOCKOUT_TIME . ' menit.'
                ]
            ];
        }

        $user = new UserModel();
        $query = $user->findByCredentials($email);
        $row = $query->getRow();

        if (!isset($row)) {
            // Spend the same hashing time as a real check so the response time does not reveal unknown emails.
            $this->verifyPassword($password, self::DUMMY_PASSWORD_HASH);
            $this->recordLoginAttempt($ip_address, $email);
            return $this->invalidCredentials();
        }

        $password_hash = $row->password;
        if (!$this->verifyPassword($password, $password_hash)) {
            // record failed attempt in the dedicated table
            $this->recordLoginAttempt($ip_address, $email);
            return $this->invalidCredentials();
        }

        // clear login attempts on success from the dedicated table
        $this->clearLoginAttempts($ip_address, $email);

        // Load full user data for session
        $user_data = new UserModel()->find((int)$row->id);

        $is_student = filter_var($row->is_student, FILTER_VALIDATE_BOOLEAN);
        $account_verified = filter_var($row->account_verified, FILTER_VALIDATE_BOOLEAN);

        if ($is_student && !$account_verified) {
            return (object)[
                'success' => false,
                'errors' => [
                    'email' => $this->resendVerificationEmail((int)$row->id, $user_data)
                ]
            ];
        }

        $actor = (int)$row->id;

        // Update last login in users' table
        $this->db->table('users')
            ->where('id', $actor)
            ->update([
                'last_logged_in' => time(),
                'last_logged_in_ip' => $ip_address,
            ]);

        // Regenerate session ID to prevent Session Fixation
        session()->regenerate(true);

        $this->setSessions($actor, $user_data, $ip_address);

        if (Setting::ALLOW_REMEMBERING && $remember_me) {
            $this->remember->rememberMe($actor);
        }

        return (object)[
            'success' => true,
            'is_student' => $is_student,
            'message' => 'Berhasil masuk.',
            'id' => $actor
        ];
    }

    /**
     * Generic response for an unknown email or a wrong password.
     *
     * @return object Result object with success=false.
     */
    private function invalidCredentials(): object
    {
        return (object)[
            'success' => false,
            'errors' => [
                'email' => 'Email atau kata sandi salah.'
            ]
        ];
    }

    /**
     * Re-send the verification email for an unverified account (rate limited)
     * and build the message shown on the login form.
     *
     * @param int         $user_id   User ID.
     * @param object|null $user_data Full user row.
     * @return string Message for the user.
     */
    private function resendVerificationEmail(int $user_id, ?object $user_data): string
    {
        $email = (string) ($user_data->email ?? '');
        $verification = new AccountVerificationService();

        $last_attempt = $verification->lastAttempt($user_id);
        if ($last_attempt === true) {
            return 'Akun Anda belum diaktifkan. Email verifikasi sudah dikirim ke ' . $email
                . ' kurang dari 10 menit yang lalu. Silakan periksa kotak masuk atau folder spam, lalu klik tautan verifikasi.';
        }
        if ($last_attempt === false) {
            return 'Akun Anda belum diaktifkan dan email verifikasi gagal dikirim. Silakan hubungi panitia PSB atau coba lagi dalam 10 menit.';
        }

        if ($verification->send($user_id, (string) ($user_data->full_name ?? ''), $email)) {
            return 'Akun Anda belum diaktifkan. Kami telah mengirim ulang email verifikasi ke ' . $email
                . '. Silakan periksa kotak masuk atau folder spam, lalu klik tautan verifikasi.';
        }

        return 'Akun Anda belum diaktifkan dan email verifikasi gagal dikirim. Silakan hubungi panitia PSB.';
    }

    /**
     * Verify password against stored hash.
     *
     * Includes safety checks for:
     * - Empty values
     * - Null characters
     * - Excessive length (DoS protection)
     *
     * @param string $password Plain text password.
     * @param string|null $password_hash Stored password hash.
     * @return bool True if password matches, false otherwise.
     */
    private function verifyPassword(string $password, ?string $password_hash): bool
    {
        if (empty($password) || empty($password_hash) || str_contains($password, "\0") || strlen($password) > Setting::MAX_PASSWORD_SIZE_BYTES) {
            return false;
        }
        return password_verify($password, $password_hash);
    }

    /**
     * Record a failed login attempt in the dedicated table.
     *
     * @param string $ip_address Client IP address.
     * @param string $identity User identity (email).
     * @return void
     */
    private function recordLoginAttempt(string $ip_address, string $identity): void
    {
        $this->db->table('login_attempts')->insert([
            'ip_address' => $ip_address,
            'identity' => $identity,
            'attempt_time' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Clear login attempts for a user in the dedicated table.
     *
     * @param string $ip_address Client IP address.
     * @param string $identity User identity (email).
     * @return void
     */
    private function clearLoginAttempts(string $ip_address, string $identity): void
    {
        $this->db->table('login_attempts')
            ->where('ip_address', $ip_address)
            ->where('identity', $identity)
            ->delete();
    }

    /**
     * Check if the account is locked due to too many failed attempts.
     *
     * @param string $ip_address Client IP address.
     * @param string $identity User identity (email).
     * @return bool True if account is locked, false otherwise.
     */
    private function isAccountLocked(string $ip_address, string $identity): bool
    {
        $lockoutTime = date('Y-m-d H:i:s', time() - (Setting::LOCKOUT_TIME * 60));
        $attempts = $this->db->table('login_attempts')
            ->where('ip_address', $ip_address)
            ->where('identity', $identity)
            ->where('attempt_time >=', $lockoutTime)
            ->countAllResults();

        return $attempts >= Setting::MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Set session data for an authenticated user.
     *
     * Stores user information and roles in session.
     *
     * @param int $actor User ID.
     * @param object|null $user_data Optional user data object.
     * @param string $ip_address Client IP address.
     * @return void
     */
    private function setSessions(int $actor, ?object $user_data = null, string $ip_address = ''): void
    {
        if (!$user_data) {
            $user_data = new UserModel()->find($actor);
        }

        $setting_data = $this->db->table('application_settings')->where('id', 1)->get()->getRow();
        $session = session();
        $session->set('actor', $actor);
        $session->set('logged_in', true);
        $session->set('last_logged_in', time());
        self::cacheBranding($setting_data);
        if (!empty($ip_address ?? '')) $session->set('last_logged_in_ip', $ip_address);
        if (!empty($user_data->full_name ?? '')) $session->set('full_name', $user_data->full_name);
        if (!empty($user_data->office_mail ?? '')) $session->set('office_mail', $user_data->office_mail);
        if (!empty($user_data->gmail ?? '')) $session->set('gmail', $user_data->gmail);
        if (!empty($user_data->yahoo ?? '')) $session->set('yahoo', $user_data->yahoo);
        if (!empty($user_data->email ?? '')) $session->set('email', $user_data->email);
        $session->set('is_admin', (bool)($user_data->is_admin ?? false));
        $session->set('is_operator', (bool)($user_data->is_operator ?? false));
        $session->set('is_student', (bool)($user_data->is_student ?? false));
    }

    /**
     * Cache the branding used by the layout and sidebar (institution name, logo, favicon) in the session.
     *
     * Called on login and whenever the application settings are saved, so the
     * sidebar logo follows the uploaded one. A value that is not set is removed,
     * so the layout falls back to the bundled logo.png / favicon.ico.
     *
     * @param object|null $setting_data The application_settings row.
     * @return void
     */
    public static function cacheBranding(?object $setting_data): void
    {
        $session = session();
        foreach (['institution_name', 'logo', 'favicon'] as $key) {
            $value = trim((string) ($setting_data->$key ?? ''));
            // Logo and favicon are file names inside the public uploads folder.
            if ($key !== 'institution_name' && in_array(basename($value), ['.', '..'], true)) {
                $value = '';
            }
            if ($value !== '') {
                $session->set($key, $key === 'institution_name' ? $value : basename($value));
            } else {
                $session->remove($key);
            }
        }
    }

    /**
     * Log out user.
     *
     * - Deletes remember-me token.
     * - Clears session data (including the cached branding).
     * - Regenerates session ID.
     *
     * @return bool True always.
     */
    public function logout(): bool
    {
        if ($this->isLoggedIn()) {
            $session = session();
            $actor = $this->getUserId();
            $this->remember->deleteUserToken($actor);
            foreach (['actor', 'logged_in', 'last_logged_in', 'last_logged_in_ip', 'full_name', 'office_mail', 'gmail', 'yahoo', 'email', 'is_admin', 'is_operator', 'is_student', 'institution_name', 'school_type', 'logo', 'favicon'] as $key) {
                $session->remove($key);
            }
            $session->regenerate(true);
            return true;
        }
        return true;
    }

    /**
     * Check if a user is logged in.
     *
     * - Validates a session actor.
     * - Supports auto-login via remember-me token: the session ID is regenerated
     *   and the token is rotated (a new selector/validator replaces the used one).
     *
     * @return bool True if logged in, false otherwise.
     * @throws RandomException
     */
    public function isLoggedIn(): bool
    {
        if (session()->has('actor') && session()->get('actor') > 0) {
            return true;
        }

        if (!Setting::ALLOW_REMEMBERING) {
            return false;
        }

        $token = $_COOKIE[Setting::REMEMBER_COOKIE_NAME] ?? null;
        if (is_string($token) && $token !== '' && $this->remember->isValidToken($token)) {
            $actor = $this->remember->findUserIdByToken($token);
            if ($actor > 0) {
                session()->regenerate(true);
                $this->setSessions($actor, null, service('request')->getIPAddress());
                $this->remember->rememberMe($actor);
                return true;
            }
        }
        return false;
    }

    /**
     * Get the current user ID from the session.
     *
     * @return int|null User ID or null if not logged in.
     */
    public function getUserId(): ?int
    {
        return session()->get('actor') ?? null;
    }

    /**
     * Check if the current user is an admin.
     *
     * @return bool|null True if admin, false if not, null if unknown.
     */
    public function isAdmin(): ?bool
    {
        $v = session()->get('is_admin');
        $v = filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return is_bool($v) ? $v : null;
    }

    /**
     * Check if the current user is an operator.
     *
     * @return bool|null True if operator, false if not, null if unknown.
     */
    public function isOperator(): ?bool
    {
        $v = session()->get('is_operator');
        $v = filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return is_bool($v) ? $v : null;
    }

    /**
     * Check if the current user is a student.
     *
     * @return bool|null True if student, false if not, null if unknown.
     */
    public function isStudent(): ?bool
    {
        $v = session()->get('is_student');
        $v = filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return is_bool($v) ? $v : null;
    }

}
