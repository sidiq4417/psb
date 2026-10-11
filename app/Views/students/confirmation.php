<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var array|null $summary */
?>
<style>

    .btn-submit-final {
        background: #2eb85c;
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(46, 184, 92, 0.3);
    }

    .btn-submit-final:hover {
        background: #28a745;
        box-shadow: 0 6px 20px rgba(46, 184, 92, 0.4);
        color: white;
    }
</style>

<div class="alert alert-response d-none" role="alert"></div>


<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="cil-history me-2"></i> Riwayat Pendaftaran</h6>
    </div>
    <div class="card-body p-0">
        <div id="REGISTRATION_HISTORIES" class="border-0"></div>
    </div>
</div>

<div class="modal" tabindex="-1" id="MODAL_CANCEL">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold"><i class="cil-trash text-danger me-2"></i> Batalkan Pendaftaran</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="cil-warning text-danger" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mt-3">Yakin ingin membatalkan pendaftaran?</h5>
                <p class="text-muted mb-0">Semua data nilai rapor, dokumen yang diunggah, dan bukti pembayaran akan dihapus. Anda dapat memilih program lain setelahnya.</p>
                <p class="text-warning fw-semibold mt-2 mb-0"><i class="cil-info me-1"></i> Pembatalan tidak dapat dilakukan jika pembayaran sudah diverifikasi oleh admin.</p>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-link text-dark text-decoration-none me-auto" data-coreui-dismiss="modal">Tidak, Kembali</button>
                <button type="button" class="btn btn-danger rounded-pill px-4" id="confirmCancelBtn">
                    <i class="cil-trash me-1"></i> Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="MODAL_CONFIRMATION">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold">Ringkasan Pendaftaran</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 mb-4" id="alert-confirmation-warning">
                    <i class="cil-info me-2"></i> Harap periksa kembali semua data sebelum menekan tombol Submit Final.
                    Data tidak dapat diubah setelah disubmit.
                </div>

                <h6 class="fw-bold text-uppercase small text-muted mb-3">Informasi Pendaftaran</h6>
                <div id="DETAIL_REGISTRATION_HISTORIES" class="mb-4"></div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Ringkasan Nilai</h6>
                        <div id="SUBJECT_GRADE_REQUIREMENTS" class="mb-4"></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Berkas Persyaratan</h6>
                        <div id="DOCUMENT_REQUIREMENTS" class="mb-4"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-link text-dark text-decoration-none me-auto"
                    data-coreui-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-submit-final confirmation-button d-none">
                    Saya Yakin & Submit Final <i class="cil-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    DS.ApplicantTypes = H.StrToObject('<?= $applicant_types ?>');
    DS.AdmissionPathways = H.StrToObject('<?= $admission_pathways ?>');
    DS.Educations = H.StrToObject('<?= $educations ?>');
    const _grid = "REGISTRATION_HISTORIES";
    const _grid2 = "DETAIL_REGISTRATION_HISTORIES";
    const _grid3 = "SUBJECT_GRADE_REQUIREMENTS";
    const _grid4 = "DOCUMENT_REQUIREMENTS";

    async function fetchData(url, params = null) {
        const isFormData = params instanceof FormData;
        const options = {
            method: "POST",
            body: params,
            cache: "no-cache",
            mode: "cors"
        };
        if (!isFormData) {
            options.headers = { "Content-Type": "application/json" };
        }
        const res = await fetch(url, options);
        if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
        return res.json();
    }

    new DataGrid(_grid, {
        title: "Daftar Registrasi",
        url: _BASE_URL + "students/confirmation/lists",
        columns: [
            {
                title: "Aksi",
                field: "A",
                formatter: function (row) {
                    let btn = `<div class="p-1 hstack gap-1 justify-content-center">`;
                    if (row.submitted_at) btn += H.BtnIcon("search", "Lihat Detail", "showDetails(" + row.id + ")");
                    if (row.submitted_at) btn += H.LinkIcon("print", "Cetak", _BASE_URL + "students/confirmation/print", "right");
                    if (!row.submitted_at) btn += H.BtnIcon("trash", "Batalkan", "cancelRegistration(" + row.id + ")");
                    btn += `</div>`;
                    return btn;
                }
            },
            { title: "Tahun", field: "admission_year", styles: 'text-center' },
            { title: "No. Registrasi", field: "registration_number", styles: 'text-center' },
            { title: "Biaya", field: "admission_fee", formatter: 'number' },
            { title: "Disubmit Pada", field: "submitted_at" },
            { title: "Nilai", field: "grade_count", styles: 'text-center' },
            { title: "Dokumen", field: "document_count", styles: 'text-center' },
            {
                title: "Bukti Transfer (Maks. 2mb)",
                field: "uploaded_at",
                formatter: function (row) {
                    let str = ``;
                    if (!row.transfer_proof_verified_at) str += `<input name="transfer_proof_${row.id}" type="file" class="form-control form-control-sm" onchange="upload(this, ${row.id})">`;
                    if (row.transfer_proof) str += '<a href="' + _BASE_URL + 'students/confirmation/download/' + row.transfer_proof + '" class="link-primary d-block mt-1 small fw-bold">Lihat Bukti</a>';
                    return str;
                }
            },
            {
                title: "Status Bayar",
                field: "transfer_proof_verified_at",
                formatter: function (row) {
                    return row.transfer_proof_verified_at ? '<span class="badge bg-success">Lunas</span>' : '<span class="badge bg-warning">Menunggu</span>';
                }
            },
        ],
        canAdd: false,
        showToolbox: false
    });

    async function renderRegistrationHistories(id) {
        const res = await fetchData(_BASE_URL + "students/confirmation/details", JSON.stringify({ id }));
        new DataGrid(_grid2, {
            showToolbar: false,
            canSearch: false,
            showToolbox: false,
            columns: [
                { title: "Sekolah", field: "branch" },
                { title: "Tingkat Masuk", field: "entry_grade" },
                { title: "Jenis Kelas", field: "education_track" },
                { title: "Program Pendidikan", field: "education_program" },
                { title: "Jenis Daftar", field: "applicant_type" }
            ],
            canAdd: false,
            canToggleColumn: false,
            records: res.records
        });
    }

    async function renderSubjectGradeRequirements() {
        const res = await fetchData(_BASE_URL + "students/subject-grade-requirements/lists", null);
        new DataGrid(_grid3, {
            showToolbar: false,
            canSearch: false,
            showToolbox: false,
            columns: [
                { title: "Mata Pelajaran", field: "subject_name" },
                { title: "Nilai", field: "score", styles: 'text-center' }
            ],
            canAdd: false,
            canToggleColumn: false,
            records: res.records
        });
    }

    async function renderDocumentRequirements() {
        const res = await fetchData(_BASE_URL + "students/document-requirements/lists", null);
        new DataGrid(_grid4, {
            showToolbar: false,
            canSearch: false,
            showToolbox: false,
            columns: [
                { title: "Nama Dokumen", field: "document_name" },
                {
                    title: "Status",
                    field: "download",
                    formatter: function (row) {
                        if (row.file_name) return '<span class="text-success fw-bold">Terunggah</span>';
                        return '<span class="text-danger fw-bold">Belum</span>';
                    }
                },
            ],
            canAdd: false,
            canToggleColumn: false,
            records: res.records
        });
    }

    async function renderRequirements(id) {
        await renderRegistrationHistories(id);
        await renderSubjectGradeRequirements(id);
        await renderDocumentRequirements(id);
    }

    function setupAndShowModal(title, showConfirmButton, id = 0, onSubmit = null) {
        const el = document.getElementById('MODAL_CONFIRMATION');
        if (el) {
            const modal = new coreui.Modal(el, { backdrop: 'static' });
            const confirmationButton = document.querySelector(".confirmation-button");
            const warningAlert = document.getElementById("alert-confirmation-warning");

            if (showConfirmButton) {
                confirmationButton.classList.remove('d-none');
                if (warningAlert) warningAlert.classList.remove('d-none');
                confirmationButton.onclick = () => {
                    if (onSubmit && typeof onSubmit === "function") onSubmit(id);
                    modal.hide();
                };
            } else {
                confirmationButton.classList.add('d-none');
                if (warningAlert) warningAlert.classList.add('d-none');
            }

            modal.show(null);
        }
    }

    async function showDetails(id) {
        await renderRequirements(id);
        setupAndShowModal("DETAIL PENDAFTARAN", false);
    }

    async function confirmRegistration(id) {
        await renderRequirements(id);
        setupAndShowModal("KONFIRMASI PENDAFTARAN", true, id, submit);
    }

    async function submit(id) {
        const res = await fetchData(_BASE_URL + "students/confirmation/submit", JSON.stringify({ id }));
        if (res.success) {
            window.location.reload();
        } else {
            const elementId = document.querySelector(".alert-response");
            elementId.classList.remove("d-none");
            elementId.innerHTML = res.message;
        }
    }

    function cancelRegistration(id) {
        const modal = new coreui.Modal(document.getElementById("MODAL_CANCEL"));
        modal.show();
        document.getElementById("confirmCancelBtn").onclick = async function () {
            document.getElementById("confirmCancelBtn").disabled = true;
            document.getElementById("confirmCancelBtn").innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Membatalkan...';
            try {
                const res = await fetchData(_BASE_URL + "students/confirmation/delete", JSON.stringify({ id }));
                if (res.success) {
                    window.location.href = _BASE_URL + "students/admissions";
                } else {
                    const alertEl = document.querySelector(".alert-response");
                    alertEl.classList.remove("d-none");
                    alertEl.innerHTML = res.message || "Gagal membatalkan pendaftaran.";
                    modal.hide();
                }
            } catch (err) {
                const alertEl = document.querySelector(".alert-response");
                alertEl.classList.remove("d-none");
                alertEl.innerHTML = "Terjadi kesalahan. Silakan coba lagi.";
                modal.hide();
            } finally {
                document.getElementById("confirmCancelBtn").disabled = false;
                document.getElementById("confirmCancelBtn").innerHTML = '<i class="cil-trash me-1"></i> Ya, Batalkan';
            }
        };
    }

    async function upload(e, id) {
        const fileInput = document.querySelector(`input[name=${e.name}]`);
        const dataset = new FormData();
        dataset.append('id', id);
        dataset.append('file', fileInput.files[0]);
        const res = await fetchData(_BASE_URL + "students/confirmation/upload", dataset);
        if (res.success) window.location.reload();
    }

    async function render() {
        // Handled by DataGrid initialization
    }
</script>
<?= $this->endSection() ?>
