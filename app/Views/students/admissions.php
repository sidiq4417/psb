<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var array $branches */
/** @var array $academic_years */
/** @var array $admission_periods */
/** @var array $period_academic_year_map */
?>
<style>
    .admission-card {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 2px solid transparent;
        border-radius: 12px;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .admission-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        border-color: #3c4b64;
    }

    .admission-card.selected {
        border-color: #2eb85c;
        background-color: #f0fff4;
    }

    .admission-card.selected::before {
        content: '\2713';
        position: absolute;
        top: 10px;
        right: 15px;
        background: #2eb85c;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        z-index: 10;
    }

    .card-major {
        font-size: 1.1rem;
        font-weight: 700;
        color: #3c4b64;
        margin-bottom: 0.5rem;
    }

    .card-detail {
        font-size: 0.85rem;
        color: #768192;
        margin-bottom: 0.25rem;
    }

    .badge-degree {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 4px;
        margin-bottom: 10px;
        display: inline-block;
    }

    .alert-info-custom {
        background-color: #f0f7ff;
        border: none;
        border-left: 4px solid #3399ff;
        border-radius: 8px;
        padding: 1.25rem 1.5rem;
        color: #2c3e50;
    }

    .alert-info-custom h5 {
        color: #007bff;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .alert-info-custom h5 i {
        margin-right: 10px;
    }

    .summary-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
        z-index: 1030;
        padding: 15px 0;
        transform: translateY(100%);
        transition: transform 0.4s ease-in-out;
    }

    .summary-footer.show {
        transform: translateY(0);
    }

    .search-input-group {
        position: relative;
    }

    .search-input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #768192;
        z-index: 5;
    }

    .search-input-group .form-control {
        padding-left: 45px;
        border-radius: 50px;
        border: 2px solid #e4e7ea;
    }

    .search-input-group .form-control:focus {
        border-color: #3399ff;
        box-shadow: 0 0 0 0.2rem rgba(51, 153, 255, 0.1);
    }

    .cursor-default {
        cursor: default !important;
    }

    .cursor-default:hover {
        transform: none !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    #ADMISSIONS_CONTAINER {
        padding-bottom: 100px;
        /* Space for footer */
    }
</style>

<div class="alert alert-info-custom shadow-sm mb-4" role="alert">
    <h5><i class="cil-info"></i> Informasi Pendaftaran</h5>
    <p class="mb-0 opacity-75" id="infoText">Silakan pilih Pembukaan Pendaftaran yang tersedia di bawah ini. <?= ($allow_multiple ?? false) ? 'Anda dapat memilih beberapa program sekaligus dalam jenjang yang setara (SD/MI, SMP/MTs, SMA/MA/SMK).' : 'Anda hanya dapat memilih satu pilihan pendaftaran.' ?></p>
</div>

<div id="submittedBanner" class="alert alert-success shadow-sm mb-4 d-none" role="alert">
    <h5 class="mb-0"><i class="cil-check-circle"></i> Pendaftaran Terkonfirmasi</h5>
    <p class="mb-0 mt-2 opacity-75">Pendaftaran Anda telah berhasil dikonfirmasi. Pilihan program sudah final dan tidak dapat diubah lagi.</p>
</div>

<div class="alert alert-response d-none" role="alert"></div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-end">
            <div class="col-md-4 mb-3 mb-md-0">
                <label class="form-label fw-bold text-uppercase small">Tahun Ajaran <sup class="text-danger">*</sup></label>
                <select class="form-select border-2" name="academic_year_id" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach ($academic_years as $k => $v) { ?>
                        <option value="<?= esc((string)$k, 'attr') ?>"><?= esc($v) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <label class="form-label fw-bold text-uppercase small">Periode Pendaftaran</label>
                <select class="form-select border-2" name="admission_period_id">
                    <option value="0">Semua Periode</option>
                    <?php foreach ($admission_periods as $k => $v) { ?>
                        <option value="<?= esc((string)$k, 'attr') ?>"><?= esc($v) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-uppercase small">Cari Program Studi</label>
                <div class="search-input-group">
                    <i class="cil-search"></i>
                    <input type="text" class="form-control" id="searchMajor" placeholder="Tulis nama jurusan..."
                        onkeyup="liveSearch()">
                </div>
            </div>
        </div>
    </div>
</div>

<div id="ADMISSIONS_CONTAINER" class="row">
    <!-- Cards will be rendered here -->
</div>

<!-- Sticky Summary Footer -->
<div class="summary-footer" id="summaryFooter">
    <div class="container container-lg">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0 text-center text-md-start">
                <span class="text-muted small text-uppercase fw-bold">Pilihan Anda:</span>
                <h5 class="mb-0 text-primary fw-bold" id="selectedMajorName">-</h5>
            </div>
            <div id="footerAction">
                <a href="<?php echo base_url('students/subject-grade-requirements') ?>"
                    class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                    Lanjut ke Nilai Rapor <i class="cil-arrow-right ms-2 font-weight-bold"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    DS.AdmissionPeriods = <?= toJson((object) $admission_periods) ?>;
    DS.PeriodAcademicYearMap = <?= toJson((object) $period_academic_year_map) ?>;
    let allRecords = [];
    let selectedOfferingIds = [];
    let isSubmitted = false;
    let allowMultiple = <?= toJson((bool)($allow_multiple ?? false)) ?>;

    async function fetchData(url, params = null) {
        const res = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: params,
            cache: "no-cache",
            mode: "cors"
        });
        if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
        return res.json();
    }

    async function saveRegistration(offering_id) {
        const res = await fetchData(_BASE_URL + "students/admissions/save", JSON.stringify({ admission_offering_id: offering_id }));
        const elementId = document.querySelector(".alert-response");
        elementId.classList.remove('d-none');
        elementId.classList.remove('alert-success', 'alert-danger');
        elementId.classList.add("alert-" + (res.success ? "success" : "danger"));
        elementId.textContent = res.message || "";

        if (res.success) {
            if (res.allow_multiple) {
                selectedOfferingIds = res.admission_offering_ids || [];
            } else {
                selectedOfferingIds = res.admission_offering_id ? [res.admission_offering_id] : [];
            }
            isSubmitted = res.submitted === true;
            await lists();
        }

        setTimeout(() => {
            elementId.innerHTML = "";
            elementId.classList.add("d-none");
        }, 4000);
    }

    async function removeRegistration(offering_id) {
        const res = await fetchData(_BASE_URL + "students/admissions/remove", JSON.stringify({ admission_offering_id: offering_id }));
        const elementId = document.querySelector(".alert-response");
        elementId.classList.remove('d-none');
        elementId.classList.remove('alert-success', 'alert-danger');
        elementId.classList.add("alert-" + (res.success ? "success" : "danger"));
        elementId.textContent = res.message || "";

        if (res.success) {
            selectedOfferingIds = res.admission_offering_ids || [];
            await lists();
        }

        setTimeout(() => {
            elementId.innerHTML = "";
            elementId.classList.add("d-none");
        }, 4000);
    }

    function liveSearch() {
        const query = document.getElementById("searchMajor").value.toLowerCase();
        const filteredRecords = allRecords.filter(row =>
            (row.school_name + ' ' + row.grade_name + ' ' + row.track_name + ' ' + (row.program_name || '') + ' ' + (row.applicant_type_name || '')).toLowerCase().includes(query)
        );
        renderCards(filteredRecords);
    }

    function renderCards(records) {
        const container = document.getElementById("ADMISSIONS_CONTAINER");
        if (records.length === 0) {
            container.innerHTML = '<div class="col-12 text-center p-5 text-muted"><h5>Tidak ada Pembukaan Pendaftaran yang ditemukan.</h5></div>';
            return;
        }

        let html = '';
        let selectedCount = 0;
        let selectedNames = [];
        records.forEach(row => {
            const isSelected = selectedOfferingIds.includes(parseInt(row.id));

            if (isSelected) {
                selectedCount++;
                selectedNames.push(row.school_name + ' - ' + row.track_name + (row.program_name ? ' - ' + row.program_name : '') + (row.applicant_type_name ? ' (' + row.applicant_type_name + ')' : ''));
            }

            const fee = row.registration_fee ? 'Rp ' + parseInt(row.registration_fee).toLocaleString('id-ID') : 'Gratis';
            const offeringId = parseInt(row.id) || 0;
            let clickHandler = '';
            if (!isSubmitted) {
                if (allowMultiple) {
                    clickHandler = isSelected
                        ? `onclick="removeRegistration(${offeringId})"`
                        : `onclick="saveRegistration(${offeringId})"`;
                } else {
                    clickHandler = `onclick="saveRegistration(${offeringId})"`;
                }
            }
            const cursorClass = isSubmitted || clickHandler === '' ? ' cursor-default' : '';

            html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm admission-card${isSelected ? ' selected' : ''}${cursorClass}" ${clickHandler}>
                    <div class="card-body p-4">
                        <span class="badge badge-degree ${isSelected ? 'bg-success text-white' : 'bg-info text-dark'} text-uppercase">${H.Esc(row.school_name)}</span>
                        ${isSubmitted && isSelected ? '<span class="badge bg-success position-absolute top-0 end-0 m-3"><i class="cil-check"></i> Terkonfirmasi</span>' : ''}
                        <h5 class="card-major">${H.Esc(row.grade_name)}</h5>
                        <div class="mt-3">
                            <div class="card-detail">
                                <i class="cil-sun small"></i> Jenis Kelas: <strong>${H.Esc(row.track_name)}</strong>
                            </div>
                            <div class="card-detail">
                                <i class="cil-stream small"></i> Program Pendidikan: <strong>${H.Esc(row.program_name || '-')}</strong>
                            </div>
                            <div class="card-detail">
                                <i class="cil-user small"></i> Jenis Pendaftaran: <strong>${H.Esc(row.applicant_type_name || '-')}</strong>
                            </div>
                            <div class="card-detail">
                                <i class="cil-people small"></i> Kuota: <strong>${H.Esc(row.quota || '-')}</strong>
                            </div>
                            <div class="card-detail">
                                <i class="cil-credit-card small"></i> Biaya: <strong>${H.Esc(fee)}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        });

        if (selectedCount > 0) {
            const label = allowMultiple
                ? selectedCount + ' Pendaftaran Dipilih'
                : selectedNames[0];
            document.getElementById("selectedMajorName").innerText = label;
            document.getElementById("summaryFooter").classList.add("show");
        } else {
            document.getElementById("summaryFooter").classList.remove("show");
        }

        container.innerHTML = html;
    }

    function populatePeriodOptions(academicYearId) {
        const periodSelect = document.querySelector('select[name="admission_period_id"]');
        if (!periodSelect) return;
        const currentVal = periodSelect.value;
        periodSelect.innerHTML = '<option value="0">Semua Periode</option>';
        Object.keys(DS.AdmissionPeriods).forEach(id => {
            if (!academicYearId || DS.PeriodAcademicYearMap[id] == academicYearId) {
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = DS.AdmissionPeriods[id];
                periodSelect.appendChild(opt);
            }
        });
        if (periodSelect.querySelector(`option[value="${currentVal}"]`)) {
            periodSelect.value = currentVal;
        } else {
            periodSelect.value = "0";
        }
    }

    async function lists() {
        const ayEl = document.querySelector('[name="academic_year_id"]');
        const ayVal = ayEl?.value || '';
        if (!ayVal) {
            const container = document.getElementById("ADMISSIONS_CONTAINER");
            container.innerHTML = '<div class="col-12 text-center p-5 text-warning"><h5>Silakan pilih Tahun Ajaran terlebih dahulu.</h5></div>';
            return;
        }

        const params = {
            admission_period_id: document.querySelector(`[name="admission_period_id"]`).value,
        };

        const container = document.getElementById("ADMISSIONS_CONTAINER");
        container.innerHTML = '<div class="col-12 text-center p-5"><div class="spinner-border text-primary" role="status"></div><p>Memuat Pembukaan Pendaftaran...</p></div>';

        try {
            const res = await fetchData(_BASE_URL + "students/admissions/lists", JSON.stringify(params));
            allRecords = res.records;
            allowMultiple = res.allow_multiple === true;
            selectedOfferingIds = res.admission_offering_ids || [];
            if (res.admission_offering_id && selectedOfferingIds.length === 0) {
                selectedOfferingIds = [res.admission_offering_id];
            }
            isSubmitted = res.submitted === true;

            if (isSubmitted) {
                document.getElementById("submittedBanner").classList.remove("d-none");
                document.getElementById("infoText").classList.add("d-none");
                document.getElementById("footerAction").innerHTML = `
                    <div class="text-center text-md-end">
                        <span class="badge bg-success fs-6 p-2 px-3 rounded-pill">
                            <i class="cil-check-circle"></i> Sudah Terkonfirmasi
                        </span>
                        <a href="${_BASE_URL}students/confirmation" class="btn btn-outline-primary btn-lg rounded-pill px-4 shadow ms-2">
                            Lihat Detail <i class="cil-arrow-right ms-1"></i>
                        </a>
                    </div>
                `;
            } else {
                document.getElementById("submittedBanner").classList.add("d-none");
                if (allowMultiple) {
                    document.getElementById("infoText").innerHTML = 'Silakan pilih Pembukaan Pendaftaran yang tersedia di bawah ini. Anda dapat memilih beberapa program sekaligus dalam jenjang yang setara (SD/MI, SMP/MTs, SMA/MA/SMK).';
                    document.getElementById("infoText").classList.remove("d-none");
                } else {
                    document.getElementById("infoText").innerHTML = 'Silakan pilih Pembukaan Pendaftaran yang tersedia di bawah ini. Anda hanya dapat memilih satu pilihan pendaftaran.';
                    document.getElementById("infoText").classList.remove("d-none");
                }
                document.getElementById("footerAction").innerHTML = `
                    <a href="${_BASE_URL}students/subject-grade-requirements"
                        class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                        Lanjut ke Nilai Rapor <i class="cil-arrow-right ms-2 font-weight-bold"></i>
                    </a>
                `;
            }

            if (selectedOfferingIds.length === 0) {
                document.getElementById("summaryFooter").classList.remove("show");
            }

            renderCards(allRecords);
        } catch (error) {
            container.innerHTML = '<div class="col-12 text-center p-5 text-danger"><h5>Terjadi kesalahan saat memuat data.</h5></div>';
            console.error(error);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const ayFilter = document.querySelector('select[name="academic_year_id"]');
        if (ayFilter) {
            ayFilter.addEventListener("change", function () {
                populatePeriodOptions(this.value);
                lists();
            });
        }

        ['branch_id', 'admission_period_id'].forEach(v => {
            const el = document.querySelector(`[name="${v}"]`);
            if (el) el.addEventListener("change", lists);
        });
    });
</script>
<?= $this->endSection() ?>
