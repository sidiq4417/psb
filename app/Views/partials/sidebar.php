<?php
$sidebar_institution = trim((string) (session()->get('institution_name') ?? ''));
$sidebar_branding = config('Branding');
$sidebar_name = $sidebar_branding->shortName !== '' ? $sidebar_branding->shortName : $sidebar_institution;
$sidebar_logo = basename((string) (session()->get('logo') ?? 'logo.png'));
$sidebar_logo_path = ($sidebar_logo === '' || $sidebar_logo === 'logo.png') ? base_url('logo.png') : base_url('upload/' . rawurlencode($sidebar_logo));
?>
<div class="sidebar sidebar-fixed sidebar-dark sidebar-narrow-unfoldable border-end" id="sidebar">
    <div class="sidebar-header border-bottom">
        <div class="sidebar-brand">
            <div class="sidebar-brand-full">
                <div class="d-flex align-items-center gap-2">
                    <img src="<?= esc($sidebar_logo_path, 'attr') ?>" alt="<?= esc('Logo ' . $sidebar_institution, 'attr') ?>"
                         width="36" height="36" class="flex-shrink-0" style="object-fit: contain;">
                    <span class="lh-sm" style="max-width: 170px;" title="<?= esc($sidebar_institution, 'attr') ?>">
                        <span class="d-block fw-bold small"><?= esc($sidebar_name !== '' ? $sidebar_name : $sidebar_branding->productName) ?></span>
                        <?php if ($sidebar_name !== ''): ?>
                            <span class="d-block sidebar-brand-tagline"><?= esc($sidebar_branding->productName) ?></span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <img class="sidebar-brand-narrow" src="<?= esc($sidebar_logo_path, 'attr') ?>" alt="" width="28" height="28"
                 style="object-fit: contain;">
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close"
            onclick="coreui.Sidebar.getInstance(document.querySelector(&quot;#sidebar&quot;)).toggle()"></button>
    </div>
    <ul class="sidebar-nav simplebar-scrollable-y" data-coreui="navigation" data-simplebar="">
        <?php if (session()->get('is_admin') || session()->get('is_operator')) { ?>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/dashboard') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-speedometer') ?>"></use>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li class="nav-title">Akun</li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/profile') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-user') ?>"></use>
                    </svg>
                    Profil
                </a>
            </li>
            <li class="nav-item">
                <?php // Logging out changes state, so it is a POST request carrying the CSRF token. ?>
                <form action="<?= site_url('logout') ?>" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="nav-link rounded-0 w-100 text-start">
                        <svg class="nav-icon">
                            <use xlink:href="<?php echo base_url('svg/free.svg#cil-account-logout') ?>"></use>
                        </svg>
                        Keluar
                    </button>
                </form>
            </li>
            <li class="nav-title">Data Akademik</li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/branches') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-building') ?>"></use>
                    </svg>
                    Sekolah
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/academic-years') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-calendar') ?>"></use>
                    </svg>
                    Tahun Ajaran
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/entry-grades') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-puzzle') ?>"></use>
                    </svg>
                    Tingkat Masuk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/education-tracks') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-sun') ?>"></use>
                    </svg>
                    Jenis Kelas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/education-programs') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-stream') ?>"></use>
                    </svg>
                    Program Pendidikan
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/subjects') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-book') ?>"></use>
                    </svg>
                    Mata Pelajaran
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/schools') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-school') ?>"></use>
                    </svg>
                    Asal Sekolah
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/provinces') ?>"
                   data-coreui-url-match="administrator/provinces,administrator/districts,administrator/subdistricts">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-location-pin') ?>"></use>
                    </svg>
                    Wilayah
                </a>
            </li>
            <li class="nav-title">Penerimaan Santri Baru</li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/admission-pathways') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-graph') ?>"></use>
                    </svg>
                    Jalur Masuk
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/applicant-types') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-graph') ?>"></use>
                    </svg>
                    Jenis Pendaftaran
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/documents') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-library') ?>"></use>
                    </svg>
                    Dokumen Persyaratan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/admission-offerings') ?>"
                   data-coreui-url-match="administrator/admission-offerings,administrator/specific-subject-grade-requirements,administrator/specific-document-requirements,administrator/specific-entrance-exam-scores">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-address-book') ?>"></use>
                    </svg>
                    Pembukaan Pendaftaran
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/admission-periods') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-calendar') ?>"></use>
                    </svg>
                    Periode Pendaftaran
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('administrator/general-requirements') ?>"
                   data-coreui-url-match="administrator/general-subject-grade-requirements,administrator/general-document-requirements,administrator/general-entrance-exam-scores">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-library') ?>"></use>
                    </svg>
                    Persyaratan Umum
                </a>
            </li>
            <?php if (session()->get('is_admin')) { ?>
                <li class="nav-title">Laporan</li>
                <li class="nav-item">
                    <a class="nav-link rounded-0" href="<?= site_url('administrator/registrants') ?>">
                        <svg class="nav-icon">
                            <use xlink:href="<?php echo base_url('svg/free.svg#cil-people') ?>"></use>
                        </svg>
                        Data Pendaftar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-0" href="<?= site_url('administrator/admission-statistic-report') ?>">
                        <svg class="nav-icon">
                            <use xlink:href="<?php echo base_url('svg/free.svg#cil-chart-pie') ?>"></use>
                        </svg>
                        Statistik Pendaftaran
                    </a>
                </li>
                <li class="nav-title">Pengaturan</li>
                <li class="nav-item">
                    <a class="nav-link rounded-0" href="<?= site_url('administrator/operators') ?>">
                        <svg class="nav-icon">
                            <use xlink:href="<?php echo base_url('svg/free.svg#cil-user-plus') ?>"></use>
                        </svg>
                        Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-0" href="<?= site_url('administrator/application-settings') ?>">
                        <svg class="nav-icon">
                            <use xlink:href="<?php echo base_url('svg/free.svg#cil-settings') ?>"></use>
                        </svg>
                        Pengaturan Sistem
                    </a>
                </li>
            <?php } ?>
        <?php } ?>
        <?php if (session()->get('is_student')) { ?>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/dashboard') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-speedometer') ?>"></use>
                    </svg>
                    Dashboard
                </a>
            </li>

            <li class="nav-title">Biodata Diri</li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/profile') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-info') ?>"></use>
                    </svg>
                    Profil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/address') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-location-pin') ?>"></use>
                    </svg>
                    Alamat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/contact') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-phone') ?>"></use>
                    </svg>
                    Kontak
                </a>
            </li>

            <li class="nav-title">Data Keluarga</li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/parents') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-people') ?>"></use>
                    </svg>
                    Ayah dan Ibu Kandung
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/guardian') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-user') ?>"></use>
                    </svg>
                    Wali
                </a>
            </li>

            <li class="nav-title">Data Akademik</li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/school') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-building') ?>"></use>
                    </svg>
                    Asal Sekolah
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/admissions') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-stream') ?>"></use>
                    </svg>
                    Pilihan Pendaftaran
                </a>
            </li>

            <li class="nav-title">Dokumen & Nilai</li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/subject-grade-requirements') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-library') ?>"></use>
                    </svg>
                    Persyaratan Nilai
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/document-requirements') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-library') ?>"></use>
                    </svg>
                    Persyaratan Dokumen
                </a>
            </li>

            <li class="nav-title">Finalisasi</li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/confirmation') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-check-alt') ?>"></use>
                    </svg>
                    Konfirmasi Pendaftaran
                </a>
            </li>

            <li class="nav-title">Akun</li>
            <li class="nav-item">
                <a class="nav-link rounded-0" href="<?= site_url('students/account') ?>">
                    <svg class="nav-icon">
                        <use xlink:href="<?php echo base_url('svg/free.svg#cil-lock-locked') ?>"></use>
                    </svg>
                    Akun Saya
                </a>
            </li>
            <li class="nav-item">
                <?php // Logging out changes state, so it is a POST request carrying the CSRF token. ?>
                <form action="<?= site_url('logout') ?>" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="nav-link rounded-0 w-100 text-start">
                        <svg class="nav-icon">
                            <use xlink:href="<?php echo base_url('svg/free.svg#cil-account-logout') ?>"></use>
                        </svg>
                        Keluar
                    </button>
                </form>
            </li>
        <?php } ?>
    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button"></button>
    </div>
</div>
