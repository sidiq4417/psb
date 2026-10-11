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
        max-width: 500px;
        margin: 2rem auto;
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem;
        background: #fff;
    }

    .settings-section {
        padding: 2.5rem;
    }

    .section-header {
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
    }

    .section-icon {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        color: #475569;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .section-subtitle {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-floating>.form-control {
        border-radius: 0.5rem;
        border: 1.5px solid var(--border-color);
        height: calc(3.5rem + 2px) !important;
    }

    .form-floating>.form-control:focus {
        border-color: var(--input-focus-border);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-floating>label {
        color: var(--text-muted);
        font-weight: 500;
    }

    .pwd-toggle-container {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .btn-toggle-view {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 8px;
        color: var(--text-muted);
        z-index: 5;
    }

    .btn-toggle-view:hover {
        color: var(--input-focus-border);
    }

    .btn-submit-modern {
        background: #1e293b;
        color: #fff;
        border-radius: 0.5rem;
        padding: 0.875rem;
        font-weight: 600;
        width: 100%;
        border: none;
        transition: all 0.2s;
        margin-top: 1rem;
    }

    .btn-submit-modern:hover {
        background: #334155;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        color: #fff;
    }

    .section-divider {
        border: none;
        border-top: 1px solid var(--border-color);
        margin: 1.5rem 0;
    }

    .password-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-settings {
        position: sticky;
        top: 1rem;
        z-index: 1050;
        margin-bottom: 2rem;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container-fluid pb-5 px-3">
    <div id="response-alert" class="alert d-none alert-settings shadow-sm" role="alert"></div>

    <div class="card card-clean border-0 shadow-sm overflow-hidden">
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-user" style="font-size: 1.5rem;"></i></div>
                <h3 class="section-title">Akun Saya</h3>
                <p class="section-subtitle">Kelola informasi profil dan keamanan akun Anda.</p>
            </div>

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="full_name" id="full_name"
                    placeholder="Nama" value="<?= esc($full_name, 'attr') ?>" <?= $is_submitted ? 'disabled' : '' ?>>
                <label for="full_name">Nama Lengkap</label>
                <?php if ($is_submitted): ?>
                    <small class="text-muted mt-1 d-block">Nama tidak dapat diubah karena sudah ada pendaftaran yang dikirim final.</small>
                <?php endif; ?>
            </div>

            <div class="form-floating mb-4">
                <input type="email" class="form-control" name="email" id="email"
                    placeholder="Email" value="<?= esc($email, 'attr') ?>">
                <label for="email">Email</label>
            </div>

            <hr class="section-divider">

            <div class="password-section-title">
                <i class="cil-lock-locked"></i> Ubah Kata Sandi
            </div>

            <div class="form-floating pwd-toggle-container">
                <input type="password" class="form-control" name="current_password" id="current_password"
                    placeholder="Pass">
                <label for="current_password">Kata Sandi Saat Ini</label>
                <button type="button" class="btn-toggle-view" tabindex="-1">
                    <i class="cil-eye"></i>
                </button>
            </div>

            <div class="form-floating pwd-toggle-container">
                <input type="password" class="form-control" name="password" id="password" placeholder="Pass">
                <label for="password">Kata Sandi Baru</label>
                <button type="button" class="btn-toggle-view" tabindex="-1">
                    <i class="cil-eye"></i>
                </button>
            </div>

            <div class="form-floating pwd-toggle-container">
                <input type="password" class="form-control" name="password_confirm" id="password_confirm"
                    placeholder="Pass">
                <label for="password_confirm">Ulangi Kata Sandi Baru</label>
                <button type="button" class="btn-toggle-view" tabindex="-1">
                    <i class="cil-eye"></i>
                </button>
            </div>

            <button type="button" name="submit" class="btn-submit-modern shadow-sm">
                <i class="cil-save me-2"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const submitBtn = document.querySelector('[name="submit"]');
        const alertBox = document.getElementById("response-alert");
        const textInputs = ["full_name", "email"].map(name => document.querySelector(`[name="${name}"]`));
        const pwdInputs = ["current_password", "password", "password_confirm"].map(name => document.querySelector(`[name="${name}"]`));

        document.querySelectorAll(".btn-toggle-view").forEach(btn => {
            btn.addEventListener("click", () => {
                const input = btn.previousElementSibling;
                const icon = btn.querySelector("i");

                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.replace("cil-eye", "cil-eye-slash");
                } else {
                    input.type = "password";
                    icon.classList.replace("cil-eye-slash", "cil-eye");
                }
            });
        });

        const showAlert = (message, type) => {
            alertBox.innerHTML = message;
            alertBox.className = `alert alert-settings alert-dismissible fade show ${type}`;
            alertBox.classList.remove("d-none");

            window.scrollTo({ top: 0, behavior: 'smooth' });

            setTimeout(() => {
                alertBox.classList.add("d-none");
            }, 5000);
        };

        submitBtn.addEventListener("click", async () => {
            if (textInputs.some(input => !input.value.trim())) {
                return showAlert("Harap isi nama lengkap dan email!", "alert-danger");
            }

            submitBtn.disabled = true;
            const originalHTML = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sedang Menyimpan...';

            const payload = {};
            textInputs.forEach(input => payload[input.name] = input.value);
            pwdInputs.forEach(input => payload[input.name] = input.value);

            try {
                const res = await fetch(_BASE_URL + "students/account/save", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload),
                    cache: "no-cache",
                    mode: "cors"
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    showAlert(data.errors ? Object.values(data.errors).map(H.Esc).join("<br/>") : H.Esc(data.message || "Gagal menyimpan perubahan."), "alert-danger");
                } else {
                    pwdInputs.forEach(input => input.value = "");
                    showAlert(H.Esc(data.message || "Perubahan berhasil disimpan."), "alert-success");
                }
            } catch (error) {
                showAlert("Koneksi gagal. Silakan coba lagi nanti.", "alert-danger");
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            }
        });
    });
</script>
<?= $this->endSection() ?>
