<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>

<style>
    :root {
        --border-color: #e2e8f0;
        --input-focus-border: #3b82f6;
        --text-muted: #64748b;
    }

    .grade-header {
        background: linear-gradient(135deg, #1d2671 0%, #3b4299 100%);
        border-radius: 1rem;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
    }

    .grade-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .grade-header p {
        opacity: 0.8;
        font-size: 0.9rem;
    }

    .grade-progress {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 0.5rem;
        padding: 1rem 1.5rem;
        backdrop-filter: blur(4px);
    }

    .grade-progress .progress {
        height: 6px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }

    .grade-progress .progress-bar {
        background: #4ade80;
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    .grade-progress .count-text {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .grade-card {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        transition: all 0.2s;
        padding: 0;
        overflow: hidden;
    }

    .grade-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .grade-card .card-body {
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .grade-card .subject-icon {
        width: 44px;
        height: 44px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        flex-shrink: 0;
    }

    .grade-card .subject-info {
        flex: 1;
        min-width: 0;
    }

    .grade-card .subject-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .grade-card .subject-threshold {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .grade-card .subject-input {
        width: 120px;
        flex-shrink: 0;
    }

    .grade-card .subject-input input {
        border-radius: 0.5rem;
        border: 1.5px solid var(--border-color);
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        text-align: center;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .grade-card .subject-input input:focus {
        border-color: var(--input-focus-border);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .grade-card .subject-input input.is-valid {
        border-color: #22c55e;
        background-color: #f0fdf4;
    }

    .grade-card .subject-input input.saving {
        opacity: 0.6;
        pointer-events: none;
    }

    .grade-card .status-icon {
        width: 28px;
        flex-shrink: 0;
        display: flex;
        justify-content: center;
    }

    .grade-card.saved {
        border-left: 3px solid #22c55e;
    }

    .grade-card.saved .subject-icon {
        background: #f0fdf4;
        color: #22c55e;
    }

    .grade-card.disabled {
        opacity: 0.7;
    }

    .grade-card.disabled .subject-input input {
        background: #f8fafc;
        cursor: not-allowed;
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

    .subject-input .input-group {
        flex-wrap: nowrap;
    }

    .subject-input .input-group-text {
        background: #f8fafc;
        border: 1.5px solid var(--border-color);
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
    }
</style>

<div class="grade-header shadow-lg text-white">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1"><i class="cil-pencil me-2"></i> Input Nilai Rapor</h3>
            <p class="mb-0">Masukkan nilai rapor Anda sesuai dengan mata pelajaran yang dipersyaratkan.</p>
        </div>
        <div class="grade-progress text-white" style="min-width: 200px;">
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

<div id="subjectList" class="row g-3">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted mt-2">Memuat persyaratan nilai...</p>
    </div>
</div>

<div id="toast" class="toast-notification">
    <i class="cil-check-circle"></i>
    <span id="toastMessage">Tersimpan</span>
</div>

<script>
    let subjects = [];
    let isSubmitted = false;

    async function fetchData(url, params = null) {
        const res = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: params,
            cache: "no-cache",
            mode: "cors"
        });
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
        const total = subjects.length;
        const filled = subjects.filter(s => s.score > 0).length;
        const pct = total > 0 ? Math.round((filled / total) * 100) : 0;
        document.getElementById("progressCount").textContent = `${filled}/${total}`;
        document.getElementById("progressBar").style.width = pct + "%";
    }

    async function saveScore(input, subjectId) {
        input.classList.add("saving");
        const score = parseInt(input.value) || 0;

        try {
            const res = await fetchData(_BASE_URL + "students/subject-grade-requirements/save", JSON.stringify({
                subject_id: subjectId,
                score: score
            }));

            if (res.success) {
                const subj = subjects.find(s => s.subject_id === subjectId);
                if (subj) subj.score = score;
                renderCards();
                updateProgress();
                showToast("Nilai tersimpan", "success");
            } else {
                showToast(res.message || "Gagal menyimpan nilai", "error");
            }
        } catch (err) {
            showToast("Koneksi gagal", "error");
        } finally {
            input.classList.remove("saving");
        }
    }

    function renderCards() {
        const container = document.getElementById("subjectList");

        if (!subjects.length) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="empty-state">
                        <i class="cil-book"></i>
                        <h5>Tidak ada persyaratan nilai</h5>
                        <p class="text-muted mb-0">Belum ada mata pelajaran yang dipersyaratkan untuk program yang Anda pilih.</p>
                    </div>
                </div>`;
            return;
        }

        const icons = ["cil-pencil", "cil-calculator", "cil-language", "cil-globe-alt", "cil-flask", "cil-compass", "cil-book", "cil-education", "cil-notes"];
        let html = "";

        subjects.forEach((s, i) => {
            const icon = icons[i % icons.length];
            const hasScore = s.score > 0;
            const disabled = isSubmitted ? "disabled" : "";

            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="grade-card ${hasScore ? 'saved' : ''} ${isSubmitted ? 'disabled' : ''}">
                        <div class="card-body">
                            <div class="subject-icon">
                                <i class="${icon}" style="font-size: 1.25rem;"></i>
                            </div>
                            <div class="subject-info">
                                <div class="subject-name">${s.subject_name}</div>
                                <div class="subject-threshold">Minimal ${s.threshold_score || 0}</div>
                            </div>
                            <div class="subject-input">
                                <div class="input-group">
                                    <input type="number"
                                        value="${s.score || ""}"
                                        ${disabled}
                                        onfocusout="saveScore(this, ${s.subject_id})"
                                        class="form-control ${hasScore ? 'is-valid' : ''}"
                                        placeholder="0"
                                        min="0"
                                        max="100">
                                </div>
                            </div>
                            <div class="status-icon">
                                ${hasScore
                                    ? '<i class="cil-check-circle text-success" style="font-size: 1.25rem;"></i>'
                                    : '<i class="cil-circle text-muted" style="font-size: 1.25rem; opacity: 0.3;"></i>'}
                            </div>
                        </div>
                    </div>
                </div>`;
        });

        container.innerHTML = html;
    }

    async function loadData() {
        try {
            const res = await fetchData(_BASE_URL + "students/subject-grade-requirements/lists");
            subjects = res.records || [];
            isSubmitted = subjects.some(s => s.submitted === true);

            if (isSubmitted) {
                document.querySelector(".grade-header p").textContent = "Pendaftaran telah dikonfirmasi. Nilai tidak dapat diubah lagi.";
            }

            renderCards();
            updateProgress();
        } catch (err) {
            document.getElementById("subjectList").innerHTML = `
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
