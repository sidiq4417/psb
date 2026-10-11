<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Config\Services;
use Exception;

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
     * @param string $encrypted The encrypted verification token.
     * @return ResponseInterface|string Redirect to login on success or unauthorized view on failure.
     */
    public function index(string $encrypted = ''): ResponseInterface|string
    {
        $encrypter = Services::encrypter();

        // Validate token format
        if ($encrypted === '' || !ctype_xdigit($encrypted)) {
            return $this->unauthorized('Invalid verification link.');
        }

        try {
            // Decrypt token to get user ID
            $decrypted = $encrypter->decrypt(hex2bin($encrypted));
            $user_id = (int)$decrypted;

            if ($user_id <= 0) {
                return $this->unauthorized('Invalid verification link.');
            }

            // Load user data
            $db = Database::connect();
            $user_data = $db->table('users')->where('id', $user_id)->get()->getRow();
            if (!$user_data) {
                return $this->unauthorized('User not found.', '404 Not Found');
            }
            if (!$user_data->account_verified) {
                $db->table('users')
                    ->where('id', $user_id)
                    ->update([
                        'account_verified' => true,
                        'account_verified_at' => date('Y-m-d H:i:s'),
                        'account_verified_by' => $user_id,
                    ]);
            }
            return redirect()->to('/');
        } catch (Exception) {
            // Handle decryption or other errors
            return $this->unauthorized('Verification failed or link is invalid.');
        }
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
            'title' => 'Unauthorized',
            'error_code' => $code,
            'error_message' => $message
        ]);
    }
}
