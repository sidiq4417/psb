<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden"
            style="background: linear-gradient(135deg, #1b400d 0%, #2e6b12 100%); border-bottom: 4px solid #fff500 !important; min-height: 250px;">
            <div class="card-body p-5 d-flex align-items-center">
                <div class="text-white">
                    <h1 class="display-5 fw-bold mb-3">Halo, <?= esc((string) session()->get('full_name')) ?>!</h1>
                    <p class="fs-4 opacity-75 mb-4 text-light">Selamat Datang di Portal Penerimaan Santri Baru
                        <br><span class="fw-bold fs-3"><?= esc($institution_name) ?></span><?= $admission_year > 0 ? ' Tahun Akademik ' . esc($academic_year) : '' ?></p>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php if ($is_submitted): ?>
                            <a href="<?= base_url('students/confirmation') ?>" class="btn btn-light btn-lg px-4 fw-bold">
                                <i class="cil-check-alt me-2"></i> Lihat Pendaftaran
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('students/profile') ?>" class="btn btn-light btn-lg px-4 fw-bold">
                                <i class="cil-pencil me-2"></i> Lengkapi Pendaftaran
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('students/registration-terms') ?>"
                            class="btn btn-outline-light btn-lg px-4">
                            <i class="cil-cloud-download me-2"></i> Syarat & Ketentuan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards mb-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-purple-lt p-3 rounded-3 me-3">
                    <i class="cil-task text-purple fs-2"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-bold text-uppercase">Hasil Seleksi</div>
                    <div class="fs-5 fw-bold text-dark mt-1">
                        <?php
                        $selectionClass = match ($selection_status) {
                            '4' => 'text-success', // Lulus
                            '7' => 'text-success', // Daftar Ulang
                            '6' => 'text-danger',  // Tidak Lulus
                            '5' => 'text-warning', // Cadangan
                            default => 'text-info' // Ditinjau, Terverifikasi, Proses Seleksi
                        };
                        if (empty($selection_status_label)) {
                            $selection_status_label = 'Belum Diproses';
                            $selectionClass = 'text-secondary opacity-50';
                        }
                        ?>
                        <span class="<?= esc($selectionClass, 'attr') ?>"><?= esc($selection_status_label) ?></span>
                    </div>
                </div>
            </div>
            <div class="mt-auto border-top pt-3 small text-muted">
                Hasil penilaian resmi panitia PSB.
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary-lt p-3 rounded-3 me-3">
                    <i class="cil-wallet text-primary fs-2"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-bold text-uppercase">Status Pembayaran</div>
                    <div class="fs-5 fw-bold text-dark mt-1">
                        <?php
                        $statusClass = match ($transfer_proof_status) {
                            'Sudah Diverifikasi' => 'text-success',
                            'Belum Diverifikasi' => 'text-warning',
                            default => 'text-danger'
                        };
                        ?>
                        <span class="<?= esc($statusClass, 'attr') ?>"><?= esc($transfer_proof_status) ?></span>
                    </div>
                </div>
            </div>
            <div class="mt-auto border-top pt-3">
                <a href="<?= site_url('students/payment-method') ?>" class="btn btn-ghost-primary w-100 fw-bold">
                    <i class="cil-cloud-download me-2"></i> Panduan Bayar
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success-lt p-3 rounded-3 me-3">
                    <i class="cil-money text-success fs-2"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-bold text-uppercase">Biaya Pendaftaran</div>
                    <div class="fs-5 fw-bold text-dark mt-1">Rp
                        <?= number_format((int)($admission_fee ?? 0), 0, ',', '.') ?></div>
                </div>
            </div>
            <div class="mt-auto border-top pt-3 small text-muted">
                Berdasarkan jalur masuk & <?= esc(lcfirst($majorLabel)) ?>.
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-info-lt p-3 rounded-3 me-3">
                    <i class="cil-headphones text-info fs-2"></i>
                </div>
                <div>
                    <div class="text-secondary small fw-bold text-uppercase">Bantuan?</div>
                    <div class="fs-6 fw-bold text-dark mt-1"><?= esc($mobile_phone) ?></div>
                </div>
            </div>
            <div class="mt-auto border-top pt-3 d-flex justify-content-between">
                <?php if ($email !== ''): ?>
                <a href="mailto:<?= esc($email, 'attr') ?>" class="btn btn-ghost-info flex-fill fw-bold"><i
                        class="cil-envelope-closed me-1"></i> Email</a>
                <?php endif; ?>
                <?php if ($telephone !== ''): ?>
                <a href="tel:<?= esc($telephone, 'attr') ?>" class="btn btn-ghost-info flex-fill fw-bold"><i
                        class="cil-phone me-1"></i> Telp</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="card-title fw-bold mb-0">
                    <i class="cil-list me-2"></i> <?= esc($majorLabel) ?> Yang Masih Dibuka
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($study_programs)): ?>
                        <div class="p-5 text-center">
                            <i class="cil-ban fs-1 text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">Maaf, saat ini tidak ada <?= esc(lcfirst($majorLabel)) ?> yang dibuka.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="card-title fw-bold mb-0 d-flex align-items-center justify-content-between">
                    <span><i class="cil-task me-2"></i> Kelengkapan Pendaftaran</span>
                    <span class="badge bg-<?= $completion_pct == 100 ? 'success' : 'primary' ?> rounded-pill fs-6"><?= $completed_count ?>/<?= $total_count ?></span>
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="progress mb-4" style="height: 6px;">
                    <div class="progress-bar bg-<?= $completion_pct == 100 ? 'success' : ($completion_pct > 50 ? 'primary' : 'warning') ?>"
                        style="width: <?= $completion_pct ?>%" role="progressbar"></div>
                </div>

                <div class="checklist">
                    <?php $checklist = [
                        'profil' => ['label' => 'Data Pribadi', 'icon' => 'cil-user', 'link' => 'students/profile'],
                        'alamat' => ['label' => 'Alamat', 'icon' => 'cil-location-pin', 'link' => 'students/address'],
                        'kontak' => ['label' => 'Kontak', 'icon' => 'cil-phone', 'link' => 'students/contact'],
                        'orang_tua' => ['label' => 'Orang Tua', 'icon' => 'cil-people', 'link' => 'students/parents'],
                        'wali' => ['label' => 'Wali', 'icon' => 'cil-user-follow', 'link' => 'students/guardian'],
                        'sekolah' => ['label' => 'Asal Sekolah', 'icon' => 'cil-school', 'link' => 'students/school'],
                        'pilihan_prodi' => ['label' => 'Pilihan Pendaftaran', 'icon' => 'cil-education', 'link' => 'students/admissions'],
                        'nilai_rapor' => ['label' => 'Nilai Rapor', 'icon' => 'cil-pencil', 'link' => 'students/subject-grade-requirements'],
                        'dokumen' => ['label' => 'Dokumen Persyaratan', 'icon' => 'cil-cloud-upload', 'link' => 'students/document-requirements'],
                        'finalisasi' => ['label' => 'Finalisasi', 'icon' => 'cil-check-alt', 'link' => 'students/confirmation'],
                    ]; ?>
                    <?php foreach ($checklist as $key => $item): ?>
                        <?php $done = $steps[$key] ?? false; ?>
                        <a href="<?= site_url($item['link']) ?>" class="text-decoration-none">
                            <div class="d-flex align-items-center py-2 px-2 rounded-3 checklist-item <?= $done ? 'done' : '' ?>">
                                <div class="me-3 d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px;">
                                    <?php if ($done): ?>
                                        <i class="cil-check-circle text-success" style="font-size: 1.2rem;"></i>
                                    <?php else: ?>
                                        <i class="cil-circle text-secondary" style="font-size: 1.2rem; opacity: 0.4;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold small <?= $done ? 'text-success' : 'text-dark' ?>"><?= esc($item['label']) ?></div>
                                </div>
                                <i class="cil-chevron-right text-muted" style="font-size: 0.75rem;"></i>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-lt {
        background-color: rgba(32, 107, 196, 0.1) !important;
        color: #206bc4 !important;
    }

    .bg-success-lt {
        background-color: rgba(47, 179, 68, 0.1) !important;
        color: #2fb344 !important;
    }

    .bg-info-lt {
        background-color: rgba(66, 153, 225, 0.1) !important;
        color: #4299e1 !important;
    }

    .bg-blue-lt {
        background-color: #e8f1ff;
        color: #0054a6;
    }

    .bg-azure-lt {
        background-color: #f0faff;
        color: #0071bd;
    }

    .bg-purple-lt {
        background-color: rgba(111, 66, 193, 0.1) !important;
        color: #6f42c1 !important;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .bg-green-lt {
        background-color: #f0fff4;
        color: #276749;
    }

    .checklist-item {
        transition: all 0.15s;
    }

    .checklist-item:hover {
        background: #f8fafc;
    }

    .checklist-item.done:hover {
        background: #f0fdf4;
    }
</style>

<?= $this->endSection() ?>
