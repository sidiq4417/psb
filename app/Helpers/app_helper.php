<?php

declare(strict_types=1);

if (!function_exists('toJson')) {
    /**
     * Safely encode a variable into a JSON string.
     *
     * Utilizes strict JSON encoding flags to prevent XSS (JSON_HEX_TAG, JSON_HEX_APOS, etc.)
     * and ensures robust error handling via JSON_THROW_ON_ERROR.
     *
     * @param mixed $arr The variable (array, object, etc.) to encode.
     * @return string|false JSON formatted string, or false on failure.
     * @throws JsonException If the encoding process fails.
     */
    function toJson(mixed $arr = null): false|string {
        return json_encode($arr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    }
}

if (!function_exists('private_upload_path')) {
    /**
     * Directory for applicant documents (KK, akta, ijazah, transfer proofs).
     *
     * Lives under WRITEPATH so the files are never reachable by URL; they are
     * only served through controllers that check login and ownership.
     *
     * @return string Absolute path with trailing slash.
     */
    function private_upload_path(): string {
        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'registrations' . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }
}

if (!function_exists('public_upload_path')) {
    /**
     * Directory for public application assets (logo, favicon, payment method
     * and registration terms PDFs).
     *
     * @return string Absolute path with trailing slash.
     */
    function public_upload_path(): string {
        return FCPATH . 'uploads' . DIRECTORY_SEPARATOR;
    }
}

if (!function_exists('find_registration_file')) {
    /**
     * Resolve a stored applicant document by name.
     *
     * Looks in the private directory first, then falls back to the legacy
     * public uploads directory used by earlier versions.
     *
     * @param string $file_name Stored file name (any directory part is ignored).
     * @return string|null Absolute path, or null when the file does not exist.
     */
    function find_registration_file(string $file_name): ?string {
        $safe_name = basename($file_name);
        if ($safe_name === '' || $safe_name === '.' || $safe_name === '..') {
            return null;
        }

        foreach ([private_upload_path(), public_upload_path()] as $dir) {
            if (is_file($dir . $safe_name)) {
                return $dir . $safe_name;
            }
        }

        return null;
    }
}
