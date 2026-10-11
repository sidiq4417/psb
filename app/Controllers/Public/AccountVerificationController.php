<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Services\AccountVerificationService;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

/**
 * Class AccountVerificationController
 *
 * Handles user account verification via encrypted token links.
 *
 * @package App\Controllers\Public
 */
class AccountVerificationController extends BaseController
{
    /**
     * Verify an account using an encrypted token.
     *
     * The token carries the user ID, email and expiry time (see AccountVerificationService).
     * It is rejected when it cannot be decrypted, has expired, or the account email has
     * changed since the link was sent.
     *
     * @param string $encrypted The encrypted verification token.
     * @return ResponseInterface|string Redirect to login on success or unauthorized view on failure.
     */
    public function index(string $encrypted = ''): ResponseInterface|string
    {
        $user_data = new AccountVerificationService()->findUserByToken($encrypted);
        if (!$user_data) {
            return $this->unauthorized('Tautan verifikasi tidak valid atau sudah kedaluwarsa. Silakan masuk dengan email dan kata sandi Anda untuk menerima tautan verifikasi yang baru.');
        }

        if (filter_var($user_data->account_verified, FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->to('/')->with('success', 'Akun Anda sudah aktif. Silakan masuk.');
        }

        $user_id = (int) $user_data->id;
        Database::connect()->table('users')
            ->where('id', $user_id)
            ->update([
                'account_verified' => true,
                'account_verified_at' => date('Y-m-d H:i:s'),
                'account_verified_by' => $user_id,
            ]);

        return redirect()->to('/')->with('success', 'Akun Anda berhasil diaktifkan. Silakan masuk.');
    }

    /**
     * Render unauthorized view with error details.
     *
     * @param string $message The error message.
     * @param string $code The HTTP error code.
     * @return string The rendered unauthorized view.
     */
    private function unauthorized(string $message, string $code = '400 Bad Request'): string
    {
        return view('public/unauthorized', [
            'title' => 'Verifikasi Gagal',
            'error_code' => $code,
            'error_message' => $message
        ]);
    }
}
