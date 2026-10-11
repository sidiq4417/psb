<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js" integrity="sha384-dug+JxfBvklEQdJ4AYuBBAIScUz0bVN73xpy273gcAwHjb3qI0fXmuYNaNfdyYJG" crossorigin="anonymous"></script>
<div class="row row-cards mb-4">
    <!-- Total Pendaftar -->
    <div class="col-sm-6 col-md-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar p-3 rounded-3">
                            <i class="cil-people fs-3"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Total Pendaftar</div>
                        <div class="text-secondary small">Tahun Akademik <?= esc((string) $academic_year) ?></div>
                    </div>
                    <div class="col-auto">
                        <div class="fs-2 fw-bold text-blue"><?= (int) ($stats->total ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Final -->
    <div class="col-sm-6 col-md-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar p-3 rounded-3">
                            <i class="cil-check fs-3"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Sudah Finalisasi</div>
                        <div class="text-secondary small">Siap Diverifikasi</div>
                    </div>
                    <div class="col-auto">
                        <div class="fs-2 fw-bold text-success"><?= (int) ($stats->finalized ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pembayaran Terverifikasi -->
    <div class="col-sm-6 col-md-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar p-3 rounded-3">
                            <i class="cil-wallet fs-3"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Bayar Terverifikasi</div>
                        <div class="text-secondary small">Sudah Sah</div>
                    </div>
                    <div class="col-auto">
                        <div class="fs-2 fw-bold text-primary"><?= (int) ($stats->verified ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menunggu Verifikasi -->
    <div class="col-sm-6 col-md-3">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar p-3 rounded-3">
                            <i class="cil-clock fs-3"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Menunggu Verifikasi</div>
                        <div class="text-secondary small">Butuh Pemeriksaan</div>
                    </div>
                    <div class="col-auto">
                        <div class="fs-2 fw-bold text-warning"><?= (int) ($stats->pending ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik & Grafik -->
<div class="row row-cards mb-4">
    <!-- Trend Pendaftaran -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title fw-bold mb-0">
                    <i class="cil-chart-line me-2"></i> Tren Pendaftaran (14 Hari Terakhir)
                </h6>
            </div>
            <div class="card-body">
                <canvas id="registrationTrendChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <!-- Popularitas Prodi -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="card-title fw-bold mb-0">
                    <i class="cil-chart-pie me-2"></i> Top 5 <?= esc($majorLabel) ?>
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="majorPopularityChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row row-cards">
    <!-- Tabel Pendaftar Hari Ini -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="card-title fw-bold mb-0">
                    <i class="cil-people me-2"></i> Pendaftar Hari Ini
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr class="bg-light">
                            <th>No. Pendaftaran</th>
                            <th>Nama</th>
                            <th class="text-center">Finalisasi Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registrations_today)): ?>
                            <tr>
                                <td colspan="3" class="text-center p-4 text-muted small">Belum ada pendaftar baru hari ini.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach($registrations_today as $row): ?>
                        <tr>
                            <td class="small fw-bold"><?= esc((string) ($row->registration_number ?: '-')) ?></td>
                            <td class="small text-dark"><?= esc((string) $row->full_name) ?></td>
                            <td class="text-center">
                                <?php if ((int)$row->submitted === 1): ?>
                                    <span class="badge bg-success-lt text-success fw-bold px-2 py-1">SUDAH</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-lt text-muted fw-bold px-2 py-1">BELUM</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabel Menunggu Verifikasi Pembayaran -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="card-title fw-bold mb-0">
                    <i class="cil-wallet me-2"></i> Verifikasi Pembayaran
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr class="bg-light">
                            <th>No. Pendaftaran</th>
                            <th>Nama</th>
                            <th>Waktu Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pending_payments)): ?>
                            <tr>
                                <td colspan="3" class="text-center p-4 text-muted small">Semua bukti pembayaran sudah diverifikasi.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach($pending_payments as $row): ?>
                        <tr>
                            <td class="small fw-bold"><?= esc((string) ($row->registration_number ?: '-')) ?></td>
                            <td class="small text-dark"><?= esc((string) $row->full_name) ?></td>
                            <td class="small text-muted">
                                <?= !empty($row->uploaded_at) ? esc(date('d/m/Y H:i', strtotime((string) $row->uploaded_at))) : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Registraton Trend Chart
    const trendCtx = document.getElementById('registrationTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: <?= toJson(array_column($trend_data, 'date_label')) ?>,
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: <?= toJson(array_map('intval', array_column($trend_data, 'total'))) ?>,
                borderColor: '#206bc4',
                backgroundColor: 'rgba(32, 107, 196, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#206bc4'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Major Popularity Chart
    const majorCtx = document.getElementById('majorPopularityChart').getContext('2d');
    new Chart(majorCtx, {
        type: 'doughnut',
        data: {
            labels: <?= toJson(array_column($major_stats, 'name')) ?>,
            datasets: [{
                data: <?= toJson(array_map('intval', array_column($major_stats, 'total'))) ?>,
                backgroundColor: ['#206bc4', '#2fb344', '#f59f00', '#d63939', '#64748b'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 15, font: { size: 11 } }
                }
            },
            cutout: '70%'
        }
    });
</script>

<style>
    .bg-blue-lt { background-color: rgba(32, 107, 196, 0.1) !important; color: #206bc4 !important; }
    .bg-success-lt { background-color: rgba(47, 179, 68, 0.1) !important; color: #2fb344 !important; }
    .bg-primary-lt { background-color: rgba(32, 107, 196, 0.1) !important; color: #206bc4 !important; }
    .bg-warning-lt { background-color: rgba(245, 159, 0, 0.1) !important; color: #f59f00 !important; }
    .bg-secondary-lt { background-color: rgba(100, 116, 139, 0.1) !important; color: #64748b !important; }
</style>

<?= $this->endSection() ?>
