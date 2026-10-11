<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>

<style>
    :root {
        --border-color: #e2e8f0;
        --input-focus-border: #3b82f6;
        --text-muted: #64748b;
    }

    .doc-header {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
        border-radius: 1rem;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
    }

    .doc-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .doc-header p {
        opacity: 0.8;
        font-size: 0.9rem;
    }

    .doc-progress {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 0.5rem;
        padding: 1rem 1.5rem;
        backdrop-filter: blur(4px);
    }

    .doc-progress .progress {
        height: 6px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .doc-progress .progress-bar {
        background: #fbbf24;
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    .doc-progress .count-text {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .doc-card {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        transition: all 0.2s;
        overflow: hidden;
    }

    .doc-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .doc-card .card-body {
        padding: 1.25rem 1.5rem;
    }

    .doc-card .doc-icon-wrap {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        flex-shrink: 0;
    }

    .doc-card .doc-icon-wrap.uploaded {
        background: #f0fdf4;
        color: #16a34a;
    }

    .doc-card .doc-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .doc-card .doc-meta {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .doc-card .doc-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .doc-card .doc-actions .btn {
        border-radius: 0.5rem;
        font-size: 0.85rem;
        padding: 0.375rem 0.85rem;
        font-weight: 600;
    }

    .upload-area {
        position: relative;
        border: 2px dashed var(--border-color);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        background: #f8fafc;
    }

    .upload-area:hover {
        border-color: var(--input-focus-border);
        background: #f0f5ff;
    }

    .upload-area input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .upload-area .upload-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .upload-area .upload-label i {
        font-size: 1.1rem;
    }

    .upload-area.uploaded {
        border-color: #22c55e;
        background: #f0fdf4;
    }

    .upload-area.uploaded .upload-label {
        color: #16a34a;
    }

    .upload-area.saving {
        opacity: 0.6;
        pointer-events: none;
    }

    .doc-card.disabled {
        opacity: 0.7;
    }

    .doc-card.disabled .upload-area {
        cursor: not-allowed;
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.65rem;
        border-radius: 50rem;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #fff;
        border-radius: 1rem;
        border: 1px solid var(--border-color);
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-state h5 {
        color: #64748b;
        font-weight: 600;
    }

    .toast-notification {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 9999;
        background: #1e293b;
        color: #fff;
        padding: 0.75rem 1.25rem;
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        transform: translateY(120%);
        transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .toast-notification.show {
        transform: translateY(0);
    }

    .toast-notification.success {
        background: #166534;
    }

    .toast-notification.error {
        background: #991b1b;
    }
</style>

<div class="doc-header shadow-lg text-white">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1"><i class="cil-cloud-upload me-2"></i> Unggah Dokumen</h3>
            <p class="mb-0">Unggah dokumen persyaratan pendaftaran sesuai dengan ketentuan yang berlaku.</p>
        </div>
        <div class="doc-progress text-white" style="min-width: 200px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold small">Kelengkapan</span>
                <span class="count-text fw-bold" id="progressCount">0/0</span>
            </div>
            <div class="progress">
                <div class="progress-bar" id="progressBar" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>

<div id="documentList" class="row g-3">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted mt-2">Memuat persyaratan dokumen...</p>
    </div>
</div>

<div id="toast" class="toast-notification">
    <i class="cil-check-circle"></i>
    <span id="toastMessage">Tersimpan</span>
</div>

<script>
    let docs = [];
    let isSubmitted = false;

    async function fetchData(url, params = null) {
        const isFormData = params instanceof FormData;
        const options = { method: "POST", body: params, cache: "no-cache", mode: "cors" };
        if (!isFormData) options.headers = { "Content-Type": "application/json" };
        const res = await fetch(url, options);
        if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
        return res.json();
    }

    function showToast(message, type = "success") {
        const toast = document.getElementById("toast");
        const icon = toast.querySelector("i");
        toast.className = `toast-notification ${type}`;
        document.getElementById("toastMessage").textContent = message;
        icon.className = type === "success" ? "cil-check-circle" : "cil-warning";
        toast.classList.add("show");
        clearTimeout(toast._hideTimer);
        toast._hideTimer = setTimeout(() => toast.classList.remove("show"), 2500);
    }

    function updateProgress() {
        const total = docs.length;
        const uploaded = docs.filter(d => d.file_name).length;
        const pct = total > 0 ? Math.round((uploaded / total) * 100) : 0;
        document.getElementById("progressCount").textContent = `${uploaded}/${total}`;
        document.getElementById("progressBar").style.width = pct + "%";
    }

    async function uploadFile(input, documentId) {
        const file = input.files[0];
        if (!file) return;

        const area = input.closest(".upload-area");
        area.classList.add("saving");

        const fd = new FormData();
        fd.append("document_id", documentId);
        fd.append("file", file);

        try {
            const res = await fetchData(_BASE_URL + "students/document-requirements/upload", fd);
            if (res.success) {
                const doc = docs.find(d => d.document_id === documentId);
                if (doc) doc.file_name = file.name;
                renderCards();
                updateProgress();
                showToast("Dokumen berhasil diunggah", "success");
            } else {
                input.value = "";
                const msg = res.errors ? Object.values(res.errors).join(", ") : "Gagal mengunggah";
                showToast(msg, "error");
            }
        } catch (err) {
            input.value = "";
            showToast("Koneksi gagal", "error");
        } finally {
            area.classList.remove("saving");
        }
    }

    function formatSize(kb) {
        if (!kb) return "-";
        return parseInt(kb) >= 1024 ? (kb / 1024).toFixed(1) + " MB" : kb + " KB";
    }

    const docIcons = ["cil-file", "cil-image", "cil-file", "cil-cloud-upload", "cil-library", "cil-notes", "cil-book", "cil-description"];

    function renderCards() {
        const container = document.getElementById("documentList");

        if (!docs.length) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="empty-state">
                        <i class="cil-document"></i>
                        <h5>Tidak ada persyaratan dokumen</h5>
                        <p class="text-muted mb-0">Belum ada dokumen yang dipersyaratkan untuk program yang Anda pilih.</p>
                    </div>
                </div>`;
            return;
        }

        const acceptTypes = ".jpg,.jpeg,.png,.pdf";
        let html = "";

        docs.forEach((d, i) => {
            const icon = docIcons[i % docIcons.length];
            const hasFile = !!d.file_name;
            const disabled = isSubmitted ? "disabled" : "";

            html += `
                <div class="col-md-6">
                    <div class="doc-card ${isSubmitted ? 'disabled' : ''}">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="doc-icon-wrap ${hasFile ? 'uploaded' : ''}">
                                    <i class="${icon}" style="font-size: 1.3rem;"></i>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="doc-name">${d.document_name}</div>
                                    <div class="doc-meta">Maks. ${formatSize(d.max_size)} &middot; ${acceptTypes}</div>
                                </div>
                                <div>
                                    ${hasFile
                                        ? '<span class="status-badge bg-success text-white"><i class="cil-check"></i> Terunggah</span>'
                                        : '<span class="status-badge bg-secondary text-white">Belum</span>'}
                                </div>
                            </div>

                            <div class="doc-actions">
                                ${hasFile ? `
                                    <a href="${_BASE_URL}students/document-requirements/download/${d.file_name}"
                                        class="btn btn-outline-success d-inline-flex align-items-center justify-content-center">
                                        <i class="cil-cloud-download me-1"></i> Unduh
                                    </a>
                                ` : ''}
                                <div class="upload-area flex-grow-1 ${hasFile ? 'uploaded' : ''}">
                                    <input type="file" accept="${acceptTypes}" ${disabled}
                                        onchange="uploadFile(this, ${d.document_id})">
                                    <div class="upload-label">
                                        ${hasFile
                                            ? '<i class="cil-check-circle"></i> Ketuk untuk ganti file'
                                            : '<i class="cil-cloud-upload"></i> Pilih file untuk diunggah'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
        });

        container.innerHTML = html;
    }

    async function loadData() {
        try {
            const res = await fetchData(_BASE_URL + "students/document-requirements/lists");
            docs = res.records || [];
            isSubmitted = docs.some(d => d.submitted === true);

            if (isSubmitted) {
                document.querySelector(".doc-header p").textContent = "Pendaftaran telah dikonfirmasi. Dokumen tidak dapat diubah lagi.";
            }

            renderCards();
            updateProgress();
        } catch (err) {
            document.getElementById("documentList").innerHTML = `
                <div class="col-12">
                    <div class="empty-state">
                        <i class="cil-ban"></i>
                        <h5>Gagal memuat data</h5>
                        <p class="text-muted mb-0">Terjadi kesalahan. Silakan muat ulang halaman.</p>
                    </div>
                </div>`;
        }
    }

    document.addEventListener("DOMContentLoaded", loadData);
</script>

<?= $this->endSection() ?>
