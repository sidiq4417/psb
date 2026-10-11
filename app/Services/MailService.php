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
        $email->setAltMessage(self::toPlainText($message));

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

    /**
     * Convert an HTML email body to its plain-text alternative.
     *
     * CodeIgniter's own fallback only strips the tags, which leaves HTML
     * entities (&nbsp;, &#x3A; ...) and the template indentation in the text
     * part. This keeps the <body> only, renders whitespace like a browser,
     * shows each link as "text (URL)", breaks lines at <br> and block
     * elements, then decodes entities and tidies the whitespace.
     *
     * @param string $html HTML body.
     * @return string Plain-text body.
     */
    private static function toPlainText(string $html): string
    {
        if (preg_match('/<body\b[^>]*>(.*)<\/body>/is', $html, $match)) {
            $html = $match[1];
        }
        $html = (string) preg_replace(['/<!--.*?-->/s', '/<(head|title|style|script)\b[^>]*>.*?<\/\1\s*>/is'], '', $html);

        // Whitespace in markup is not significant.
        $html = (string) preg_replace('/[ \t\r\n\f]+/', ' ', $html);

        // Keep link targets readable: "text (URL)", or only the URL when the text is the URL.
        $html = (string) preg_replace_callback(
            '/<a\b[^>]*?\shref\s*=\s*(["\'])(.*?)\1[^>]*>(.*?)<\/a\s*>/is',
            static function (array $m): string {
                $url = trim(html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $text = trim(html_entity_decode(strip_tags($m[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if (!preg_match('#^https?://#i', $url) || $text === $url) {
                    return $m[3];
                }
                $href = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

                return $text === '' ? $href : $m[3] . ' (' . $href . ')';
            },
            $html
        );

        $html = (string) preg_replace('/<br\b[^>]*>/i', "\n", $html);
        $html = (string) preg_replace(['/<li\b[^>]*>/i', '/<\/li\s*>/i'], ["\n- ", ''], $html);
        $html = (string) preg_replace('/<\/?(?:p|div|h[1-6]|table|thead|tbody|tfoot|tr|ul|ol|blockquote|hr)\b[^>]*>/i', "\n\n", $html);

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = (string) preg_replace('/(?:[^\S\n]|\x{00A0})+/u', ' ', $text);
        $text = (string) preg_replace('/^ +| +$/m', '', $text);
        $text = (string) preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }
}
