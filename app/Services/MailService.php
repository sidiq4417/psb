<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ApplicationSettingModel;
use Config\Services;
use Throwable;

/**
 * Class MailService
 *
 * Sends HTML emails through the SMTP account stored in the application
 * settings (Pengaturan Sistem).
 *
 * - Port 465 uses implicit SSL, port 25 uses no encryption, any other port uses STARTTLS.
 * - The sender address is the SMTP username when it is an email address, because
 *   most providers (Hostinger included) reject a different From address; the
 *   institution email is then used as Reply-To.
 *
 * @package App\Services
 */
class MailService
{
    /**
     * Send an HTML email.
     *
     * @param string $to      Recipient email address.
     * @param string $subject Email subject.
     * @param string $message HTML body.
     * @return bool True when the SMTP server accepted the message, false otherwise.
     */
    public function send(string $to, string $subject, string $message): bool
    {
        $setting = new ApplicationSettingModel()->getSetting();

        $smtp_host = trim((string) ($setting->smtp_host ?? ''));
        $smtp_user = trim((string) ($setting->smtp_user ?? ''));
        $smtp_port = (int) ($setting->smtp_port ?? 0) ?: 587;
        $institution_email = trim((string) ($setting->email ?? ''));
        $from = filter_var($smtp_user, FILTER_VALIDATE_EMAIL) ? $smtp_user : $institution_email;

        if ($smtp_host === '' || !filter_var($from, FILTER_VALIDATE_EMAIL) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            log_message('error', 'Mail: SMTP is not configured or an address is invalid; email not sent.');
            return false;
        }

        $crypto = match ($smtp_port) {
            465 => 'ssl',
            25 => '',
            default => 'tls',
        };

        $email = Services::email(null, false);
        $email->initialize([
            'protocol' => 'smtp',
            'SMTPHost' => $smtp_host,
            'SMTPUser' => $smtp_user,
            'SMTPPass' => (string) ($setting->smtp_pass ?? ''),
            'SMTPPort' => $smtp_port,
            'SMTPCrypto' => $crypto,
            'mailType' => 'html',
            'charset' => 'UTF-8',
            'wordWrap' => true,
        ]);

        $branding = config('Branding');
        $sender_name = $branding->shortName !== '' ? $branding->shortName : trim((string) ($setting->institution_name ?? ''));
        $email->setFrom($from, trim($branding->productName . ' ' . $sender_name));
        if ($institution_email !== '' && strcasecmp($institution_email, $from) !== 0 && filter_var($institution_email, FILTER_VALIDATE_EMAIL)) {
            $email->setReplyTo($institution_email);
        }
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);

        try {
            if ($email->send(false)) {
                return true;
            }
            log_message('error', 'Mail: failed to send. ' . $email->printDebugger(['headers', 'subject']));
        } catch (Throwable $e) {
            log_message('error', 'Mail: failed to send. ' . $e->getMessage());
        }

        return false;
    }
}
