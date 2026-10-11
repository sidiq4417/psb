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

        <!-- SECTION 1: TELEPON & MESSAGING -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-phone"></i></div>
                <h3 class="section-title">Telepon & Pesan Instan</h3>
            </div>

            <?php if ($is_locked): ?>
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="cil-lock-locked me-2"></i> Data kontak Anda sudah dikunci dan tidak dapat diubah kembali.
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="telephone" type="text" class="form-control" name="telephone"
                               value="<?= esc($row->telephone ?? '', 'attr') ?>" placeholder="Telepon Rumah"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="telephone">Telepon Rumah</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="mobile_phone" type="text" class="form-control" name="mobile_phone"
                               value="<?= esc($row->mobile_phone ?? '', 'attr') ?>" placeholder="Handphone"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="mobile_phone">Handphone / Mobile</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="whatsapp" type="text" class="form-control" name="whatsapp"
                               value="<?= esc($row->whatsapp ?? '', 'attr') ?>" placeholder="WhatsApp"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="whatsapp">No. WhatsApp <sup class="text-danger">*</sup></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="telegram" type="text" class="form-control" name="telegram"
                               value="<?= esc($row->telegram ?? '', 'attr') ?>" placeholder="Telegram"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="telegram">Username / No. Telegram</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: MEDIA SOSIAL -->
        <div class="settings-section bg-light-subtle">
            <div class="section-header">
                <div class="section-icon"><i class="cil-share-all"></i></div>
                <h3 class="section-title">Media Sosial</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="facebook" type="text" class="form-control" name="facebook"
                               value="<?= esc($row->facebook ?? '', 'attr') ?>" placeholder="Facebook"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="facebook">Facebook URL / Username</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="twitter" type="text" class="form-control" name="twitter"
                               value="<?= esc($row->twitter ?? '', 'attr') ?>" placeholder="Twitter"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="twitter">Twitter / X Username</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="instagram" type="text" class="form-control" name="instagram"
                               value="<?= esc($row->instagram ?? '', 'attr') ?>" placeholder="Instagram"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="instagram">Instagram Username</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="tiktok" type="text" class="form-control" name="tiktok"
                               value="<?= esc($row->tiktok ?? '', 'attr') ?>" placeholder="TikTok"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="tiktok">TikTok Username</label>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$is_locked): ?>
            <div class="card-footer bg-white p-4 border-0 text-end">
                <button type="button" name="submit" class="btn btn-submit-modern shadow-sm">
                    <i class="cil-save me-2"></i> Simpan Kontak
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

        const inputNames = ["telephone", "mobile_phone", "whatsapp", "telegram", "facebook", "twitter", "instagram", "tiktok"];
        const inputs = inputNames.map(name => document.querySelector(`[name="${name}"]`));

        submitBtn.addEventListener("click", async () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sedang Menyimpan...';

            try {
                const formData = {};
                inputs.forEach(input => {
                    if (input) formData[input.name] = input.value.trim();
                });

                const res = await fetch(`${_BASE_URL}students/contact/save`, {
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

                showAlert(H.Esc(resData.message || "") || Object.values(resData.errors || {}).map(H.Esc).join("<br/>"), messageType);
            } catch (error) {
                console.error("Submission error:", error);
                showAlert("Terjadi kesalahan teknis saat menyimpan data.", "alert-danger");
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="cil-save me-2"></i> Simpan Kontak';
            }
        });
    });
</script>
<?= $this->endSection() ?>
