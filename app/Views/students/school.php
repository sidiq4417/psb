<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>

<style>
    :root {
        --border-color: #e2e8f0;
        --input-focus-border: #3b82f6;
        --text-muted: #64748b;
        --bg-light: #f8fafc;
    }
    .card-clean {
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-radius: 0.75rem;
        background: #fff;
    }
    .settings-section {
        padding: 2.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    .settings-section:last-child {
        border-bottom: none;
    }
    .section-header {
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
    }
    .section-icon {
        width: 36px;
        height: 36px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        color: #475569;
    }
    .section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .form-floating > .form-control,
    .form-floating > .form-select {
        border-radius: 0.5rem;
        border: 1.5px solid var(--border-color);
        height: calc(3.5rem + 2px);
    }
    .form-floating > .form-control:focus,
    .form-floating > .form-select:focus {
        border-color: var(--input-focus-border);
        box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
    }
    .form-floating > label {
        color: var(--text-muted);
        font-weight: 500;
    }
    .btn-submit-modern {
        background: #1e293b;
        color: #fff;
        border-radius: 0.5rem;
        padding: 0.875rem 2.5rem;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }
    .btn-submit-modern:hover {
        background: #334155;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        color: #fff;
    }
    .btn-submit-modern:active {
        transform: translateY(0);
    }
    .alert-settings {
        position: sticky;
        top: 1rem;
        z-index: 1050;
        margin-bottom: 2rem;
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container-fluid pb-5 px-0">
    <div id="response-alert" class="alert d-none alert-settings shadow-sm" role="alert"></div>

    <div class="card card-clean border-0 shadow-sm overflow-hidden">

        <!-- SECTION 1: IDENTITAS SEKOLAH -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-school"></i></div>
                <h3 class="section-title">Asal Sekolah</h3>
            </div>

            <?php if ($is_locked): ?>
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="cil-lock-locked me-2"></i> Data asal sekolah Anda sudah dikunci dan tidak dapat diubah kembali.
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="nisn" type="text" class="form-control" name="nisn"
                               value="<?= $row->nisn ?? '' ?>" placeholder="NISN"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="nisn">NISN</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="npsn" type="text" class="form-control" name="npsn"
                               value="<?= $row->npsn ?? '' ?>" placeholder="NPSN"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="npsn">NPSN</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="school_name" type="text" class="form-control" name="school_name"
                               value="<?= $row->school_name ?? '' ?>" placeholder="Nama Sekolah"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="school_name">Nama Sekolah</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="school_major" type="text" class="form-control" name="school_major"
                               value="<?= $row->school_major ?? '' ?>" placeholder="Jurusan"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="school_major">Jurusan / Program Studi</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: DETAIL LULUSAN -->
        <div class="settings-section bg-light-subtle">
            <div class="section-header">
                <div class="section-icon"><i class="cil-calendar"></i></div>
                <h3 class="section-title">Informasi Kelulusan</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="form-floating">
                        <input id="school_address" type="text" class="form-control" name="school_address"
                               value="<?= $row->school_address ?? '' ?>" placeholder="Alamat Sekolah"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="school_address">Alamat Lengkap Sekolah</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="school_graduation_year" type="number" class="form-control" name="school_graduation_year"
                               value="<?= $row->school_graduation_year ?? '' ?>" placeholder="Tahun Lulus"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="school_graduation_year">Tahun Lulus</label>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$is_locked): ?>
            <div class="card-footer bg-white p-4 border-0 text-end">
                <button type="button" name="submit" class="btn btn-submit-modern shadow-sm">
                    <i class="cil-save me-2"></i> Simpan Data Sekolah
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    async function showAlert(message, type) {
        const alertBox = document.getElementById("response-alert");
        alertBox.innerHTML = message;
        alertBox.className = `alert alert-settings alert-dismissible fade show ${type}`;
        alertBox.classList.remove("d-none");

        window.scrollTo({ top: 0, behavior: 'smooth' });

        setTimeout(() => {
            alertBox.classList.add("d-none");
        }, 5000);
    }

    document.addEventListener("DOMContentLoaded", () => {
        const submitBtn = document.querySelector('[name="submit"]');
        if (!submitBtn) return;

        const inputNames = ["nisn", "npsn", "school_address", "school_name", "school_major", "school_graduation_year"];
        const inputs = inputNames.map(name => document.querySelector(`[name="${name}"]`));

        submitBtn.addEventListener("click", async () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sedang Menyimpan...';

            try {
                const formData = {};
                inputs.forEach(input => {
                    if (input) formData[input.name] = input.value.trim();
                });

                const res = await fetch(`${_BASE_URL}students/school/save`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(formData),
                    cache: "no-cache",
                    mode: "cors"
                });

                const resData = await res.json();
                const messageType = res.ok && resData.success ? "alert-success" : "alert-danger";

                if (resData.success && resData.row) {
                    inputs.forEach(input => {
                        if (input) input.value = resData.row[input.name] ?? "";
                    });
                }

                showAlert(resData.message || Object.values(resData.errors || {}).join("<br/>"), messageType);
            } catch (error) {
                console.error("Submission error:", error);
                showAlert("Terjadi kesalahan teknis saat menyimpan data.", "alert-danger");
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="cil-save me-2"></i> Simpan Data Sekolah';
            }
        });
    });
</script>
<?= $this->endSection() ?>
