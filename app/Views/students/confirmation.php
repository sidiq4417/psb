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

<?php if (($unsubmitted_count ?? 0) > 0): ?>
    <div class="alert alert-warning border-0 shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" role="alert">
        <div>
            <h6 class="fw-bold mb-1"><i class="cil-lock-unlocked me-2"></i> Pendaftaran belum dikirim final</h6>
            <p class="mb-0 small">
                Lengkapi nilai rapor, dokumen wajib, dan bukti pembayaran, lalu klik tombol <strong>Kirim Final</strong> pada pendaftaran Anda.
                Setelah dikirim final, data pendaftaran akan <strong>dikunci</strong> dan tidak dapat diubah lagi.
            </p>
        </div>
        <?php if (!empty($allow_multiple)): ?>
            <button type="button" class="btn btn-submit-final text-nowrap" id="submitAllBtn" onclick="confirmSubmitAll()">
                <i class="cil-send me-2"></i> Kirim Semua (<?= (int) $unsubmitted_count ?>)
            </button>
        <?php endif; ?>
    </div>
<?php endif; ?>


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

<div class="modal" tabindex="-1" id="MODAL_SUBMIT_ALL">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold"><i class="cil-send text-success me-2"></i> Kirim Final Semua Pendaftaran</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="cil-lock-locked text-warning" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mt-3">Kirim final <?= (int) ($unsubmitted_count ?? 0) ?> pendaftaran sekaligus?</h5>
                <p class="text-muted mb-0">Semua pendaftaran yang belum dikirim akan dikirim final dengan satu nomor registrasi yang sama.</p>
                <p class="text-danger fw-semibold mt-2 mb-0"><i class="cil-warning me-1"></i> Setelah dikirim final, data pendaftaran (biodata, pilihan program, nilai rapor, dan dokumen) akan dikunci dan tidak dapat diubah lagi.</p>
            </div>
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-link text-dark text-decoration-none me-auto" data-coreui-dismiss="modal">Tidak, Periksa Lagi</button>
                <button type="button" class="btn btn-submit-final" id="confirmSubmitAllBtn">
                    Ya, Kirim Semua <i class="cil-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="MODAL_CONFIRMATION">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold" id="MODAL_CONFIRMATION_TITLE">Ringkasan Pendaftaran</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 mb-4" id="alert-confirmation-warning">
                    <i class="cil-lock-locked me-2"></i> Harap periksa kembali semua data sebelum menekan tombol <strong>Kirim Final</strong>.
                    Setelah dikirim final, data pendaftaran (biodata, pilihan program, nilai rapor, dan dokumen) akan
                    <strong>dikunci dan tidak dapat diubah lagi</strong>.
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
                    Saya Yakin & Kirim Final <i class="cil-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    DS.ApplicantTypes = <?= $applicant_types ?>;
    DS.AdmissionPathways = <?= $admission_pathways ?>;
    DS.Educations = <?= $educations ?>;
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

    /**
     * Show a server message above the registration list.
     * @param {string} message - Plain text message.
     * @param {boolean} success - Whether to style it as success.
     * @param {string[]} [details] - Optional list of detail lines.
     */
    function showResponse(message, success, details = []) {
        const el = document.querySelector(".alert-response");
        el.classList.remove("d-none", "alert-success", "alert-danger");
        el.classList.add(success ? "alert-success" : "alert-danger");
        let html = H.Esc(message);
        if (Array.isArray(details) && details.length) {
            html += '<ul class="mb-0 mt-2">' + details.map(d => `<li>${H.Esc(d)}</li>`).join("") + '</ul>';
        }
        el.innerHTML = html;
        el.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    function errorText(errors, fallback) {
        if (!errors || typeof errors !== "object") return fallback;
        const parts = Object.values(errors).filter(v => typeof v === "string" && v !== "");
        return parts.length ? parts.join(" ") : fallback;
    }

    new DataGrid(_grid, {
        title: "Daftar Registrasi",
        url: _BASE_URL + "students/confirmation/lists",
        columns: [
            {
                title: "Aksi",
                field: "A",
                formatter: function (row) {
                    const id = parseInt(row.id) || 0;
                    let btn = `<div class="p-1 hstack gap-1 justify-content-center">`;
                    if (row.submitted_at) {
                        btn += H.BtnIcon("search", "Lihat Detail", "showDetails(" + id + ")");
                        btn += H.LinkIcon("print", "Cetak", _BASE_URL + "students/confirmation/print", "right");
                    } else {
                        if ((parseInt(row.admission_offering_id) || 0) > 0) {
                            btn += `<button type="button" class="btn btn-success btn-sm text-nowrap" onclick="confirmRegistration(${id})" title="Kirim final pendaftaran ini"><i class="cil-send me-1"></i> Kirim Final</button>`;
                        }
                        btn += H.BtnIcon("trash", "Batalkan", "cancelRegistration(" + id + ")");
                    }
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
                    const id = parseInt(row.id) || 0;
                    const verified = parseInt(row.transfer_proof_verified) === 1;
                    let str = ``;
                    if (!verified) str += `<input name="transfer_proof_${id}" type="file" accept=".jpg,.jpeg,.png,.gif,.pdf" class="form-control form-control-sm" onchange="upload(this, ${id})">`;
                    if (row.transfer_proof) str += `<a href="${_BASE_URL}students/confirmation/download/${H.Esc(encodeURIComponent(row.transfer_proof))}" class="link-primary d-block mt-1 small fw-bold">Lihat Bukti</a>`;
                    return str;
                }
            },
            {
                title: "Status Bayar",
                field: "transfer_proof_verified",
                formatter: function (row) {
                    return parseInt(row.transfer_proof_verified) === 1 ? '<span class="badge bg-success">Lunas</span>' : '<span class="badge bg-warning">Menunggu</span>';
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

    async function renderSubjectGradeRequirements(id) {
        const res = await fetchData(_BASE_URL + "students/subject-grade-requirements/lists", JSON.stringify({ registration_id: id }));
        new DataGrid(_grid3, {
            showToolbar: false,
            canSearch: false,
            showToolbox: false,
            columns: [
                { title: "Mata Pelajaran", field: "subject_name" },
                {
                    title: "Nilai",
                    field: "score",
                    styles: 'text-center',
                    formatter: function (row) {
                        const score = parseFloat(row.score) || 0;
                        return score > 0 ? H.Esc(score) : '<span class="text-danger fw-bold">Belum</span>';
                    }
                }
            ],
            canAdd: false,
            canToggleColumn: false,
            records: res.records
        });
    }

    async function renderDocumentRequirements(id) {
        const res = await fetchData(_BASE_URL + "students/document-requirements/lists", JSON.stringify({ registration_id: id }));
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
                        if (row.required === false) return '<span class="text-muted">Opsional</span>';
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
            const modal = coreui.Modal.getOrCreateInstance(el, { backdrop: 'static' });
            const confirmationButton = document.querySelector(".confirmation-button");
            const warningAlert = document.getElementById("alert-confirmation-warning");
            const titleEl = document.getElementById("MODAL_CONFIRMATION_TITLE");
            if (titleEl) titleEl.textContent = title;

            if (showConfirmButton) {
                confirmationButton.classList.remove('d-none');
                confirmationButton.disabled = false;
                if (warningAlert) warningAlert.classList.remove('d-none');
                confirmationButton.onclick = async () => {
                    confirmationButton.disabled = true;
                    try {
                        if (onSubmit && typeof onSubmit === "function") await onSubmit(id);
                    } finally {
                        confirmationButton.disabled = false;
                        modal.hide();
                    }
                };
            } else {
                confirmationButton.classList.add('d-none');
                confirmationButton.onclick = null;
                if (warningAlert) warningAlert.classList.add('d-none');
            }

            modal.show();
        }
    }

    async function showDetails(id) {
        try {
            await renderRequirements(id);
            setupAndShowModal("Detail Pendaftaran", false);
        } catch (err) {
            showResponse("Gagal memuat detail pendaftaran.", false);
        }
    }

    async function confirmRegistration(id) {
        try {
            await renderRequirements(id);
            setupAndShowModal("Konfirmasi Kirim Final", true, id, submit);
        } catch (err) {
            showResponse("Gagal memuat ringkasan pendaftaran.", false);
        }
    }

    async function submit(id) {
        try {
            const res = await fetchData(_BASE_URL + "students/confirmation/submit", JSON.stringify({ id }));
            if (res.success) {
                showResponse(res.message || "Pendaftaran berhasil dikirim final.", true);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showResponse(res.message || "Pendaftaran gagal dikirim final.", false);
            }
        } catch (err) {
            showResponse("Terjadi kesalahan. Silakan coba lagi.", false);
        }
    }

    function confirmSubmitAll() {
        const modal = coreui.Modal.getOrCreateInstance(document.getElementById("MODAL_SUBMIT_ALL"));
        const btn = document.getElementById("confirmSubmitAllBtn");
        modal.show();
        btn.onclick = async function () {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...';
            try {
                const res = await fetchData(_BASE_URL + "students/confirmation/submitAll", JSON.stringify({}));
                if (res.success) {
                    showResponse(res.message || "Semua pendaftaran berhasil dikirim final.", true);
                    setTimeout(() => window.location.reload(), 1500);
                } else if (Array.isArray(res.errors) && res.errors.length) {
                    showResponse("Belum ada pendaftaran yang dikirim final. Lengkapi data berikut terlebih dahulu:", false, res.errors);
                } else {
                    showResponse(res.message || "Pendaftaran gagal dikirim final.", false);
                }
            } catch (err) {
                showResponse("Terjadi kesalahan. Silakan coba lagi.", false);
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Ya, Kirim Semua <i class="cil-arrow-right ms-2"></i>';
                modal.hide();
            }
        };
    }

    function cancelRegistration(id) {
        const modal = coreui.Modal.getOrCreateInstance(document.getElementById("MODAL_CANCEL"));
        modal.show();
        document.getElementById("confirmCancelBtn").onclick = async function () {
            document.getElementById("confirmCancelBtn").disabled = true;
            document.getElementById("confirmCancelBtn").innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Membatalkan...';
            try {
                const res = await fetchData(_BASE_URL + "students/confirmation/delete", JSON.stringify({ id }));
                if (res.success) {
                    window.location.href = _BASE_URL + "students/admissions";
                } else {
                    showResponse(res.message || "Gagal membatalkan pendaftaran.", false);
                    modal.hide();
                }
            } catch (err) {
                showResponse("Terjadi kesalahan. Silakan coba lagi.", false);
                modal.hide();
            } finally {
                document.getElementById("confirmCancelBtn").disabled = false;
                document.getElementById("confirmCancelBtn").innerHTML = '<i class="cil-trash me-1"></i> Ya, Batalkan';
            }
        };
    }

    async function upload(input, id) {
        const file = input.files[0];
        if (!file) return;
        const dataset = new FormData();
        dataset.append('id', id);
        dataset.append('file', file);
        input.disabled = true;
        try {
            const res = await fetchData(_BASE_URL + "students/confirmation/upload", dataset);
            if (res.success) {
                window.location.reload();
                return;
            }
            input.value = "";
            showResponse(errorText(res.errors, res.message || "Gagal mengunggah bukti pembayaran."), false);
        } catch (err) {
            input.value = "";
            showResponse("Terjadi kesalahan saat mengunggah. Silakan coba lagi.", false);
        } finally {
            input.disabled = false;
        }
    }
</script>
<?= $this->endSection() ?>
