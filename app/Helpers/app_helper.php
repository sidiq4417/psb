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

if (!function_exists('registration_verification_changes')) {
    /**
     * Columns to update when an administrator saves the "Konfirmasi Pendaftaran"
     * and "Verifikasi Biaya Pendaftaran" checkboxes of a registration.
     *
     * The admin forms only carry a date, so the timestamps are never taken from
     * them: a timestamp is set to now when its checkbox is switched on, cleared
     * when it is switched off, and left untouched while it stays the same.
     *
     * No registration number is assigned here, so callers must refuse to switch
     * "Konfirmasi Pendaftaran" on for a registration whose registration_number is empty.
     *
     * @param object $registration Current registrations row (submitted, submitted_at, transfer_proof_verified).
     * @param bool   $submitted    New "Konfirmasi Pendaftaran" value.
     * @param bool   $verified     New "Verifikasi Biaya Pendaftaran" value.
     * @return array<string, int|string|null> Columns for the registrations table.
     */
    function registration_verification_changes(object $registration, bool $submitted, bool $verified): array {
        $now = date('Y-m-d H:i:s');
        $data = [
            'submitted' => $submitted ? 1 : 0,
            'transfer_proof_verified' => $verified ? 1 : 0,
        ];

        if ($submitted !== ((int) ($registration->submitted ?? 0) === 1)) {
            // Unconfirming reopens the registration, which the applicant pages detect by an empty submitted_at.
            $stored = (string) ($registration->submitted_at ?? '');
            $data['submitted_at'] = $submitted ? ($stored !== '' ? $stored : $now) : null;
        }
        if ($verified !== ((int) ($registration->transfer_proof_verified ?? 0) === 1)) {
            $data['transfer_proof_verified_at'] = $verified ? $now : null;
        }

        return $data;
    }
}

if (!function_exists('district_display_name')) {
    /**
     * Find a readable district (kabupaten/kota) name in the location data.
     *
     * @param string $province_id The selected province ID.
     * @param string $district_id The selected district ID.
     * @return string|null The name without the "Kab."/"Kota" prefix (e.g. "Bekasi"), or null if not found.
     */
    function district_display_name(string $province_id, string $district_id): ?string {
        if ($province_id === '' || $district_id === '' || $district_id === '99') {
            return null;
        }

        $file_path = WRITEPATH . 'districts.json';
        if (!is_file($file_path) || !is_readable($file_path)) {
            return null;
        }

        $districts = json_decode((string) file_get_contents($file_path));
        foreach ((array) $districts as $district) {
            if ((string) ($district->district_id ?? '') === $district_id && (string) ($district->province_id ?? '') === $province_id) {
                $name = trim((string) preg_replace('/^(kabupaten|kab\.|kota\.?)\s*/i', '', (string) ($district->district_name ?? '')));
                return $name !== '' ? $name : null;
            }
        }

        return null;
    }
}

if (!function_exists('resolve_birth_place')) {
    /**
     * Resolve the birth place columns of the users table from form input.
     *
     * Indonesian birth places are picked from the province/district lists and
     * stored with a readable district name in birth_place (e.g. "Bekasi");
     * other countries (or "Lainnya") use the free-text place, falling back to
     * the country name.
     *
     * @param array<string, mixed> $input Form data with birth_place_country_id, birth_place_province_id,
     *                                    birth_place_district_id and the free-text birth_place.
     * @return array<string, string|null> Columns for the users table.
     */
    function resolve_birth_place(array $input): array {
        $text = static fn(string $key): string => is_scalar($input[$key] ?? null) ? trim((string) $input[$key]) : '';
        $country_id = $text('birth_place_country_id');
        $free_text = $text('birth_place');

        if ($country_id === 'ID') {
            $province_id = $text('birth_place_province_id');
            $district_id = $text('birth_place_district_id');
            $district_name = district_display_name($province_id, $district_id);
            return [
                'birth_place_country_id' => 'ID',
                'birth_place_province_id' => $province_id !== '' && $province_id !== '99' ? $province_id : null,
                'birth_place_district_id' => $district_name !== null ? $district_id : null,
                'birth_place' => $district_name ?? ($free_text !== '' ? $free_text : null),
            ];
        }

        $countries = countries();
        $country_id = $country_id !== '' && isset($countries[$country_id]) ? $country_id : null;
        return [
            'birth_place_country_id' => $country_id,
            'birth_place_province_id' => null,
            'birth_place_district_id' => null,
            'birth_place' => $free_text !== '' ? $free_text : ($country_id !== null ? $countries[$country_id] : null),
        ];
    }
}
