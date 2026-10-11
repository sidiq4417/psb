<?php
/**
 * Student Registration Form Template (PDF)
 *
 * @var object $student
 * @var object $registration
 * @var string $shared_number
 * @var array $details
 */
?>
<style>
    .label { font-size: 8pt; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
    .value { font-size: 10pt; color: #000; font-weight: bold; }
    .section-header { padding: 3px 0; margin-top: 15px; }
    .section-title { font-size: 10pt; font-weight: bold; color: #000; text-transform: uppercase; letter-spacing: 1px; }
    .header-uni { font-size: 16pt; font-weight: bold; color: #000; }
    .header-info { font-size: 9pt; color: #555; }
    .td-data { border-bottom: 1px solid #e0e0e0; padding: 5px 0; }
</style>

<!-- HEADER -->
<table style="width:100%;" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:15%; text-align:left; vertical-align:middle;">
            <img src="<?= ROOTPATH . 'public/img/logo.png' ?>" width="75">
        </td>
        <td style="width:70%; text-align:center; vertical-align:middle;">
            <span class="header-uni"><?= mb_strtoupper($app_settings->institution_name ?? 'NAMA INSTANSI') ?></span><br>
            <span style="font-size:12pt; font-weight:bold;">PANITIA PENERIMAAN MURID BARU <?= $registration->admission_year ?? date('Y') ?></span><br>
            <span class="header-info"><?= $app_settings->address ?? '' ?></span><br>
            <span class="header-info">Website: <?= $app_settings->website ?? '' ?> | Email: <?= $app_settings->email ?? '' ?></span>
        </td>
        <td style="width:15%;"></td>
    </tr>
</table>

<div style="text-align:center; margin-bottom:20px;">
    <span style="font-size:14pt; font-weight:bold;">FORMULIR PENDAFTARAN</span>
</div>

<!-- I. BIODATA PENDAFTAR -->
<div class="section-header">
    <span class="section-title">BIODATA PENDAFTAR</span>
</div>
<table style="width:100%;" cellpadding="5" cellspacing="0">
    <tr>
        <td style="width:50%;" class="td-data">
            <span class="label">Nama Lengkap</span><br>
            <span class="value" style="font-size:11pt;"><?= mb_strtoupper($student->full_name ?? '-') ?></span>
        </td>
        <td style="width:50%;" class="td-data">
            <span class="label">No. Registrasi</span><br>
            <span class="value" style="font-size:11pt;"><?= $shared_number ?></span>
        </td>
    </tr>
    <tr>
        <td style="width:50%;" class="td-data">
            <span class="label">NIK</span><br>
            <span class="value"><?= $student->nik ?? '-' ?></span>
        </td>
        <td style="width:50%;" class="td-data">
            <span class="label">Jenis Kelamin</span><br>
            <span class="value"><?= ($student->gender ?? '') === 'M' ? 'Laki-laki' : 'Perempuan' ?></span>
        </td>
    </tr>
    <tr>
        <td style="width:50%;" class="td-data">
            <span class="label">Tempat Lahir</span><br>
            <span class="value"><?= $student->birth_place ?? '-' ?></span>
        </td>
        <td style="width:50%;" class="td-data">
            <span class="label">Tanggal Lahir</span><br>
            <span class="value"><?= $student->birth_date ?? '-' ?></span>
        </td>
    </tr>
    <tr>
        <td style="width:50%;" class="td-data">
            <span class="label">Agama</span><br>
            <span class="value"><?= isset($student->religion_id) ? religions()[$student->religion_id] : '-' ?></span>
        </td>
        <td style="width:50%;" class="td-data">
            <span class="label">Kewarganegaraan</span><br>
            <span class="value"><?= isset($student->citizenship) ? countries()[$student->citizenship] : '-' ?></span>
        </td>
    </tr>
</table>

<div style="height:1px;"></div>

<!-- II. PILIHAN PENDAFTARAN -->
<div class="section-header">
    <span class="section-title">PILIHAN PENDAFTARAN</span>
</div>
<table cellpadding="4" cellspacing="0" style="width:100%; border-collapse:collapse; font-size:9pt;">
    <tr style="font-weight:bold;">
        <td style="width:5%; border:0.5px solid #000; text-align:center; padding:4px 6px;">No</td>
        <td style="width:19%; border:0.5px solid #000; padding:4px 6px;">Sekolah</td>
        <td style="width:14%; border:0.5px solid #000; padding:4px 6px;">Jenjang</td>
        <td style="width:18%; border:0.5px solid #000; padding:4px 6px;">Jenis Kelas</td>
        <td style="width:24%; border:0.5px solid #000; padding:4px 6px;">Program</td>
        <td style="width:20%; border:0.5px solid #000; padding:4px 6px;">Jenis Daftar</td>
    </tr>
    <?php $no = 1; foreach ($details as $row): ?>
    <tr>
        <td style="border:0.5px solid #000; padding:4px 6px; text-align:center;"><?= $no++ ?></td>
        <td style="border:0.5px solid #000; padding:4px 6px;"><?= $row->branch ?? '-' ?></td>
        <td style="border:0.5px solid #000; padding:4px 6px;"><?= $row->entry_grade ?? '-' ?></td>
        <td style="border:0.5px solid #000; padding:4px 6px;"><?= $row->education_track ?? '-' ?></td>
        <td style="border:0.5px solid #000; padding:4px 6px;"><?= $row->education_program ?? '-' ?></td>
        <td style="border:0.5px solid #000; padding:4px 6px;"><?= $row->applicant_type ?? '-' ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<div style="height:35px;"></div>

<!-- SIGNATURE -->
<table style="width:100%;">
    <tr>
        <td style="vertical-align:top; font-size:10pt;">Saya menyatakan bahwa semua data yang saya isi di atas adalah benar dan saya bertanggung jawab penuh atas kebenarannya.</td>
    </tr>
    <tr>
        <td style="text-align:right;">
            <span style="font-size:10pt;">Dicetak pada: <?= date('d/m/Y') ?></span><br>
            <div style="height:50px;"></div>
            <span style="font-size:11pt; font-weight:bold; text-decoration:underline;"><?= mb_strtoupper($student->full_name ?? '-') ?></span><br>
            <span style="font-size:9pt;">Pendaftar</span>
        </td>
    </tr>
</table>
