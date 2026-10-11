<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js" integrity="sha384-dug+JxfBvklEQdJ4AYuBBAIScUz0bVN73xpy273gcAwHjb3qI0fXmuYNaNfdyYJG" crossorigin="anonymous"></script>
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="form-floating border-light-subtle shadow-sm mb-2">
                <select class="form-select form-select-sm" id="admission_year" name="admission_year">
                    <?php foreach ($admission_years as $year) { ?>
                        <option value="<?= (int) $year ?>" <?= (int) $year === (int) ($admission_year ?? date('Y')) ? 'selected' : '' ?>><?= (int) $year ?>
                        </option>
                    <?php } ?>
                </select>
                <label class="form-label fw-bolder text-dark caps-lock">Tahun Penerimaan</label>
            </div>
        </div>
    </div>

    <div class="row row-cards mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-chart-line me-2 text-primary"></i> Tren Pendaftaran Per Bulan
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMonth" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-building me-2 text-primary"></i> Statistik Pendaftar Per Sekolah
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartCampus" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-check-circle me-2 text-success"></i> Statistik Pendaftar Per Status Seleksi
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartStatus" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-library me-2 text-info"></i> Statistik Pendaftar Per Program Studi
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMajor" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-sun me-2 text-warning"></i> Statistik Pendaftar Per Jenis Kelas
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartClass" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-task me-2 text-danger"></i> Statistik Pendaftar Per Jalur Masuk
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartPath" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-list me-2 text-secondary"></i> Statistik Pendaftar Per Jenis Pendaftaran
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartType" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-school me-2 text-dark"></i> Top 10 Asal Sekolah
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartSchool" height="400"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-location-pin me-2 text-danger"></i> Distribusi Provinsi
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartProvince" height="400"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-people me-2 text-primary"></i> Sebaran Jenis Kelamin
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartGender" height="400"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title fw-bold mb-0">
                        <i class="cil-money me-2 text-success"></i> Penghasilan Orang Tua
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartIncome" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        let charts = {};

        const chartConfigs = {
            month: {id: 'chartMonth', type: 'line', color: '#206bc4', fill: true},
            campus: {id: 'chartCampus', type: 'bar', color: '#206bc4', indexAxis: 'x'},
            status: {
                id: 'chartStatus',
                type: 'doughnut',
                colors: ['#2fb344', '#f59f00', '#206bc4', '#d63939', '#64748b', '#4299e1', '#b8a2e3']
            },
            major: {id: 'chartMajor', type: 'bar', color: '#4299e1', indexAxis: 'x'},
            class: {id: 'chartClass', type: 'pie', colors: ['#f59f00', '#2fb344', '#206bc4', '#d63939']},
            path: {id: 'chartPath', type: 'doughnut', colors: ['#d63939', '#206bc4', '#2fb344', '#f59f00', '#64748b']},
            type: {id: 'chartType', type: 'pie', colors: ['#64748b', '#4299e1', '#f59f00', '#2fb344', '#206bc4']},
            school: {id: 'chartSchool', type: 'bar', color: '#64748b', indexAxis: 'y'},
            province: {id: 'chartProvince', type: 'bar', color: '#d63939', indexAxis: 'y'},
            gender: {id: 'chartGender', type: 'doughnut', colors: ['#206bc4', '#f66d9b']},
            income: {id: 'chartIncome', type: 'bar', color: '#2fb344', indexAxis: 'y'}
        };

        function initChart(key, labels, values) {
            const config = chartConfigs[key];
            const ctx = document.getElementById(config.id).getContext('2d');

            if (charts[key]) {
                charts[key].destroy();
            }

            const chartData = {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pendaftar',
                    data: values,
                    backgroundColor: (config.type === 'bar' || config.type === 'line') ? config.color : config.colors,
                    borderColor: (config.type === 'bar' || config.type === 'line') ? (config.type === 'line' ? config.color : '#ffffff') : '#ffffff',
                    borderWidth: (config.type === 'line' || config.type === 'bar') ? 2 : 2,
                    fill: config.fill || false,
                    tension: config.type === 'line' ? 0.4 : 0,
                    pointRadius: config.type === 'line' ? 4 : 0
                }]
            };

            const chartOptions = {
                indexAxis: config.indexAxis || 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: (config.type !== 'bar' && config.type !== 'line'),
                        position: 'bottom',
                        labels: {boxWidth: 12, padding: 15, font: {size: 11}}
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) label += ': ';
                                if (context.formattedValue !== null) label += context.formattedValue;
                                return label;
                            }
                        }
                    }
                }
            };

            if (config.type === 'bar' || config.type === 'line') {
                chartOptions.scales = {
                    y: {beginAtZero: true, ticks: {precision: 0}},
                    x: {grid: {display: false}}
                };
            }

            charts[key] = new Chart(ctx, {
                type: config.type,
                data: chartData,
                options: chartOptions
            });
        }

        function loadData() {
            const year = document.getElementById('admission_year').value;
            H.Loading(true);

            fetch(_BASE_URL + 'administrator/admission-statistic-report/data?admission_year=' + encodeURIComponent(year), {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
                .then(response => response.json())
                .then(data => {
                    H.Loading(false);
                    for (const key in data) {
                        const labels = data[key].map(item => item.label);
                        const values = data[key].map(item => item.total);
                        initChart(key, labels, values);
                    }
                })
                .catch(error => {
                    H.Loading(false);
                    Alert.error('Gagal memuat data statistik');
                    console.error('Error:', error);
                });
        }

        document.getElementById('admission_year').addEventListener('change', loadData);

        document.addEventListener('DOMContentLoaded', loadData);
    </script>

    <style>
        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, .05);
        }

        .card-title {
            font-size: 0.95rem;
        }
    </style>

<?= $this->endSection() ?>
