<?php

declare(strict_types=1);

/**
 * Indonesian upload error messages (overrides system/Language/en/HTTP.php).
 *
 * Returned by UploadedFile::getErrorString(), which fills "%s" with the file
 * name through sprintf(). Keys that are not listed here fall back to English.
 *
 * @package App\Language
 */
return [
    'uploadErrOk'        => 'Berkas berhasil diunggah.',
    'uploadErrIniSize'   => 'Berkas "%s" melebihi batas ukuran unggahan server.',
    'uploadErrFormSize'  => 'Berkas "%s" melebihi batas ukuran unggahan formulir.',
    'uploadErrPartial'   => 'Berkas "%s" hanya terunggah sebagian. Silakan coba lagi.',
    'uploadErrNoFile'    => 'Tidak ada berkas yang diunggah.',
    'uploadErrCantWrite' => 'Berkas "%s" gagal disimpan di server.',
    'uploadErrNoTmpDir'  => 'Berkas gagal diunggah: direktori sementara server tidak tersedia.',
    'uploadErrExtension' => 'Unggahan berkas dihentikan oleh ekstensi PHP.',
    'uploadErrUnknown'   => 'Berkas "%s" gagal diunggah karena kesalahan yang tidak diketahui.',
];
