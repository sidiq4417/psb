<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ApplicationSettingModel;
use Config\Database;
use Config\Services;
use Throwable;

/**
 * Class AccountVerificationService
 *
 * Builds and checks account verification links and sends the verification email.
 *
 * The link token is the hex-encoded, encrypted JSON payload
 * {"id": <user id>, "email": <email>, "exp": <unix time>}. A token is accepted only
 * before "exp" and only while the account still has the same email address.
 *
 * @package App\Services
 */
class AccountVerificationService
{
    /**
     * Lifetime of a verification link in seconds (3 days).
     */
    public const int TOKEN_LIFETIME = 259200;

    /**
     * Minimum time in seconds between two verification emails for one account (10 minutes).
     */
    public const int RESEND_INTERVAL = 600;

    /**
     * Create an encrypted verification token.
     *
     * @param int    $user_id User ID.
     * @param string $email   Email address the link is sent to.
     * @return string Hex-encoded encrypted token.
     */
    public function createToken(int $user_id, string $email): string
    {
        $payload = json_encode([
            'id' => $user_id,
            'email' => $email,
            'exp' => time() + self::TOKEN_LIFETIME,
        ], JSON_THROW_ON_ERROR);

        return bin2hex(Services::encrypter()->encrypt($payload));
    }

    /**
     * Decrypt and validate a verification token.
     *
     * @param string $token Hex-encoded encrypted token from the link.
     * @return object|null The matching (non-deleted) user row, or null when the token is invalid or expired.
     */
    public function findUserByToken(string $token): ?object
    {
        if ($token === '' || strlen($token) % 2 !== 0 || !ctype_xdigit($token)) {
            return null;
        }

        try {
            $payload = json_decode(Services::encrypter()->decrypt((string) hex2bin($token)), true, 4, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return null;
        }

        if (!is_array($payload)
            || !is_int($payload['id'] ?? null) || $payload['id'] <= 0
            || !is_string($payload['email'] ?? null) || $payload['email'] === ''
            || !is_int($payload['exp'] ?? null) || $payload['exp'] < time()
        ) {
            return null;
        }

        $user = Database::connect()->table('users')
            ->where('id', $payload['id'])
            ->where('is_deleted', false)
            ->get()
            ->getRow();

        if (!$user || !hash_equals(mb_strtolower((string) $user->email), mb_strtolower($payload['email']))) {
            return null;
        }

        return $user;
    }

    /**
     * Send the verification email.
     *
     * Every attempt (successful or not) starts the resend interval for the account,
     * so a broken SMTP setup cannot be hammered through repeated logins.
     *
     * @param int    $user_id   User ID.
     * @param string $full_name Recipient name.
     * @param string $email     Recipient email address.
     * @return bool True when the email was accepted by the SMTP server.
     */
    public function send(int $user_id, string $full_name, string $email): bool
    {
        // Start the interval before the (slow) SMTP call so parallel logins do not send twice.
        cache()->save($this->cacheKey($user_id), 0, self::RESEND_INTERVAL);

        $setting = new ApplicationSettingModel()->getSetting();
        $school_name = (string) ($setting->institution_name ?? '');

        $message = view('emails/account-verification', [
            'full_name' => $full_name,
            'email' => $email,
            'school_name' => $school_name,
            'verification_link' => site_url('account-verification/' . $this->createToken($user_id, $email)),
        ]);

        $sent = new MailService()->send($email, 'Aktivasi Akun Penerimaan Santri Baru ' . $school_name, $message);
        cache()->save($this->cacheKey($user_id), $sent ? 1 : 0, self::RESEND_INTERVAL);

        return $sent;
    }

    /**
     * Result of the last send attempt within RESEND_INTERVAL.
     *
     * @param int $user_id User ID.
     * @return bool|null Null when another email may be sent now; otherwise whether the last attempt succeeded.
     */
    public function lastAttempt(int $user_id): ?bool
    {
        $value = cache($this->cacheKey($user_id));

        return $value === null ? null : (bool) $value;
    }

    /**
     * Cache key that remembers the result of the last verification email attempt.
     *
     * @param int $user_id User ID.
     * @return string
     */
    private function cacheKey(int $user_id): string
    {
        return 'account_verification_sent_' . $user_id;
    }
}
