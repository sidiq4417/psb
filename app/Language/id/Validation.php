<?php

declare(strict_types=1);

/**
 * Indonesian validation messages (overrides system/Language/en/Validation.php).
 *
 * Placeholders: {field} is the field label, {param} the rule parameter (or the
 * label of the field it refers to) and {value} the submitted value. Messages are
 * formatted with ICU MessageFormat, so do not use apostrophes. Keys that are not
 * listed here (e.g. the developer-facing core messages) fall back to English.
 *
 * @package App\Language
 */
return [
    // Rule Messages
    'alpha'                 => '{field} hanya boleh berisi huruf.',
    'alpha_dash'            => '{field} hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
    'alpha_numeric'         => '{field} hanya boleh berisi huruf dan angka.',
    'alpha_numeric_punct'   => '{field} hanya boleh berisi huruf, angka, spasi, dan karakter ~ ! # $ % & * - _ + = | : .',
    'alpha_numeric_space'   => '{field} hanya boleh berisi huruf, angka, dan spasi.',
    'alpha_space'           => '{field} hanya boleh berisi huruf dan spasi.',
    'decimal'               => '{field} harus berupa angka desimal.',
    'differs'               => '{field} harus berbeda dengan {param}.',
    'equals'                => '{field} harus sama dengan: {param}.',
    'exact_length'          => '{field} harus tepat {param} karakter.',
    'field_exists'          => '{field} wajib dikirim.',
    'greater_than'          => '{field} harus berupa angka lebih dari {param}.',
    'greater_than_equal_to' => '{field} harus berupa angka lebih dari atau sama dengan {param}.',
    'hex'                   => '{field} hanya boleh berisi karakter heksadesimal.',
    'in_list'               => '{field} harus salah satu dari: {param}.',
    'integer'               => '{field} harus berupa bilangan bulat.',
    'is_natural'            => '{field} hanya boleh berisi angka.',
    'is_natural_no_zero'    => '{field} hanya boleh berisi angka dan harus lebih dari nol.',
    'is_not_unique'         => '{field} tidak ditemukan.',
    'is_unique'             => '{field} sudah digunakan.',
    'less_than'             => '{field} harus berupa angka kurang dari {param}.',
    'less_than_equal_to'    => '{field} harus berupa angka kurang dari atau sama dengan {param}.',
    'matches'               => '{field} tidak cocok dengan {param}.',
    'max_length'            => '{field} maksimal {param} karakter.',
    'min_length'            => '{field} minimal {param} karakter.',
    'not_equals'            => '{field} tidak boleh bernilai: {param}.',
    'not_in_list'           => '{field} tidak boleh salah satu dari: {param}.',
    'numeric'               => '{field} harus berupa angka.',
    'regex_match'           => 'Format {field} tidak valid.',
    'required'              => '{field} wajib diisi.',
    'required_with'         => '{field} wajib diisi jika {param} diisi.',
    'required_without'      => '{field} wajib diisi jika {param} tidak diisi.',
    'string'                => '{field} harus berupa teks yang valid.',
    'timezone'              => '{field} harus berupa zona waktu yang valid.',
    'valid_base64'          => '{field} harus berupa teks base64 yang valid.',
    'valid_email'           => '{field} harus berisi alamat email yang valid.',
    'valid_emails'          => 'Semua alamat email pada {field} harus valid.',
    'valid_ip'              => '{field} harus berisi alamat IP yang valid.',
    'valid_url'             => '{field} harus berisi URL yang valid.',
    'valid_url_strict'      => '{field} harus berisi URL yang valid.',
    'valid_date'            => '{field} harus berisi tanggal yang valid.',
    'valid_json'            => '{field} harus berisi JSON yang valid.',

    // Credit Cards
    'valid_cc_number' => '{field} bukan nomor kartu kredit yang valid.',

    // Files
    'uploaded' => '{field} gagal diunggah. Pastikan berkas sudah dipilih dan ukurannya tidak melebihi batas.',
    'max_size' => 'Ukuran berkas {field} terlalu besar.',
    'is_image' => '{field} harus berupa berkas gambar yang valid.',
    'mime_in'  => 'Jenis berkas {field} tidak diizinkan.',
    'ext_in'   => 'Ekstensi berkas {field} tidak diizinkan.',
    'max_dims' => '{field} bukan gambar, atau dimensinya terlalu lebar atau terlalu tinggi.',
    'min_dims' => '{field} bukan gambar, atau dimensinya kurang lebar atau kurang tinggi.',
];
