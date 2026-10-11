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
    .file-upload-item {
        background: #f8fafc;
        border: 1px dashed var(--border-color);
        padding: 1.5rem;
        border-radius: 0.75rem;
        transition: all 0.2s;
        height: 100%;
    }
    .file-upload-item:hover {
        border-color: var(--input-focus-border);
        background: #f1f5f9;
    }
    .upload-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .preview-link {
        font-size: 0.75rem;
        text-decoration: none;
        background: #fff;
        padding: 5px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: #2563eb;
        font-weight: 600;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
    }
    .preview-link:hover {
        background: #ebf2ff;
        border-color: #3b82f6;
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

        <!-- SECTION 1: PROFIL PT -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-institution"></i></div>
                <h3 class="section-title">Profil Instansi</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="institution_name" type="text" class="form-control" name="institution_name" value="<?= $row->institution_name ?? '' ?>" placeholder="Nama Instansi">
                        <label for="institution_name">Nama Instansi</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="tagline" type="text" class="form-control" name="tagline" value="<?= $row->tagline ?? '' ?>" placeholder="Portal Admisi Terpadu">
                        <label for="tagline">Tagline / Subtitle</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-floating">
                        <textarea id="address" class="form-control" name="address" style="height: 100px" placeholder="Alamat"><?= $row->address ?? '' ?></textarea>
                        <label for="address">Alamat Lengkap</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: KONTAK -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-phone"></i></div>
                <h3 class="section-title">Informasi Kontak</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="telephone" type="text" class="form-control" name="telephone" value="<?= $row->telephone ?? '' ?>" placeholder="Telepon">
                        <label for="telephone">Telepon Kantor</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="mobile_phone" type="text" class="form-control" name="mobile_phone" value="<?= $row->mobile_phone ?? '' ?>" placeholder="Handphone">
                        <label for="mobile_phone">Handphone / WhatsApp</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="email" type="email" class="form-control" name="email" value="<?= $row->email ?? '' ?>" placeholder="Email">
                        <label for="email">Alamat Email Resmi</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: SISTEM -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-cog"></i></div>
                <h3 class="section-title">Konfigurasi Sistem</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="timezone" class="form-select" name="timezone">
                            <option value="">Pilih Zona Waktu:</option>
                            <?php foreach ($timezones as $timezone): ?>
                                <option value="<?= $timezone ?>" <?= ($row->timezone == $timezone) ? 'selected' : '' ?>><?= $timezone ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="timezone">Zona Waktu Aplikasi</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch" style="padding-top: 1rem;">
                        <input class="form-check-input" type="checkbox" role="switch" id="allow_multiple_registration" name="allow_multiple_registration" value="1" <?= ($row->allow_multiple_registration ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="allow_multiple_registration">Izinkan Pendaftaran Lebih dari Satu Program Studi</label>
                        <div class="form-text text-muted">Jika diaktifkan, siswa dapat mendaftar beberapa program sekaligus (wajib dalam jenjang yang sama, misal semua SD atau semua SMP).</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="max_registrations" type="number" class="form-control" name="max_registrations" value="<?= (int)($row->max_registrations ?? 3) ?>" min="1" max="50" placeholder="Maksimal Pendaftaran">
                        <label for="max_registrations">Maksimal Jumlah Pendaftaran per Siswa</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: MEDIA -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-image"></i></div>
                <h3 class="section-title">Media & Dokumen</h3>
            </div>
            <div class="row g-4">
                <?php
                $uploads = [
                    'logo' => 'Logo Instansi',
                    'favicon' => 'Favicon Browser',
                    'payment_method' => 'Panduan Pembayaran (PDF)',
                    'registration_terms' => 'Syarat & Ketentuan (PDF)'
                ];
                foreach ($uploads as $name => $label): ?>
                    <div class="col-md-6">
                        <div class="file-upload-item">
                            <div class="upload-label">
                                <span><?= $label ?></span>
                                <span id="preview-<?= $name ?>">
                                    <?php if (!empty($row->$name)): ?>
                                        <a href="<?= base_url('administrator/application-settings/download/' . $row->$name) ?>" target="_blank" class="preview-link">
                                            <i class="cil-external-link me-1"></i> Lihat File
                                        </a>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <input type="file" class="form-control form-control-sm" name="<?= $name ?>"
                                   <?= strpos($name, '_terms') !== false || strpos($name, 'method') !== false ? 'accept="application/pdf"' : '' ?>
                                   onchange="upload(this, '<?= $name ?>')">
                            <div class="form-text text-muted smaller mt-1">Ukuran maksimal 2MB</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- SECTION 5: SMTP -->
        <div class="settings-section bg-light-subtle">
            <div class="section-header">
                <div class="section-icon"><i class="cil-envelope-open"></i></div>
                <h3 class="section-title">Pengaturan SMTP Email</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="smtp_host" type="text" class="form-control" name="smtp_host" value="<?= $row->smtp_host ?? '' ?>" placeholder="Host">
                        <label for="smtp_host">SMTP Host</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="smtp_user" type="text" class="form-control" name="smtp_user" value="<?= $row->smtp_user ?? '' ?>" placeholder="User">
                        <label for="smtp_user">SMTP Username</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating">
                        <input id="smtp_pass" type="password" class="form-control" name="smtp_pass" value="<?= $row->smtp_pass ?? '' ?>" placeholder="Pass">
                        <label for="smtp_pass">SMTP Password</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating">
                        <input id="smtp_port" type="text" class="form-control" name="smtp_port" value="<?= $row->smtp_port ?? '' ?>" placeholder="Port">
                        <label for="smtp_port">SMTP Port</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 6: SECURITY (TURNSTILE) -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-shield-alt"></i></div>
                <h3 class="section-title">Keamanan (Cloudflare Turnstile)</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="turnstile_site_key" type="text" class="form-control" name="turnstile_site_key" value="<?= $row->turnstile_site_key ?? '' ?>" placeholder="Site Key">
                        <label for="turnstile_site_key">Site Key</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="turnstile_secret_key" type="password" class="form-control" name="turnstile_secret_key" value="<?= $row->turnstile_secret_key ?? '' ?>" placeholder="Secret Key">
                        <label for="turnstile_secret_key">Secret Key</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white p-4 border-0 text-end">
            <button type="button" name="submit" class="btn btn-submit-modern shadow-sm">
                <i class="cil-save me-2"></i> Simpan Konfigurasi
            </button>
        </div>
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

    async function fetchData(url, params = null) {
        const res = await fetch(url, {
            method: "POST",
            body: params,
            cache: "no-cache",
            mode: "cors"
        });
        if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
        return res.json();
    }

    async function upload(e, document_name) {
        const fileInput = document.querySelector(`input[name=${document_name}]`);
        if (!fileInput.files.length) return;

        const data = new FormData();
        data.append("document_name", document_name);
        data.append("file", fileInput.files[0]);

        try {
            const res = await fetchData(`${_BASE_URL}administrator/application-settings/upload`, data);
            if (res.success) {
                fileInput.value = "";
                const previewContainer = document.getElementById(`preview-${document_name}`);
                if (previewContainer) {
                    previewContainer.innerHTML = `<a href="${_BASE_URL}administrator/application-settings/download/${res.file}" target="_blank" class="preview-link"><i class="cil-external-link me-1"></i> Lihat File</a>`;
                }
                showAlert(res.message, "alert-success");
            } else {
                showAlert(res.errors.file || "Gagal mengunggah file.", "alert-danger");
            }
        } catch (error) {
            showAlert("Terjadi kesalahan teknis saat mengunggah.", "alert-danger");
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const submitBtn = document.querySelector('[name="submit"]');
        const requiredInputs = [
            "institution_name", "address", "telephone", "mobile_phone",
            "email", "timezone"
        ].map(name => document.querySelector(`[name="${name}"]`));

        const optionalInputs = [
            "tagline",
            "smtp_host", "smtp_user", "smtp_pass", "smtp_port",
            "turnstile_site_key", "turnstile_secret_key",
            "allow_multiple_registration", "max_registrations"
        ].map(name => document.querySelector(`[name="${name}"]`));

        const allInputs = [...requiredInputs, ...optionalInputs];

        submitBtn.addEventListener("click", async () => {
            if (requiredInputs.some(input => input && !input.value.trim())) {
                return showAlert("Semua kolom profil wajib diisi!", "alert-danger");
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sedang Menyimpan...';

            try {
                const data = new FormData();
                allInputs.forEach(input => {
                    if (!input) return;
                    if (input.type === 'checkbox') {
                        data.set(input.name, input.checked ? '1' : '0');
                    } else {
                        data.set(input.name, input.value);
                    }
                });

                const res = await H.AjaxRequest(`${_BASE_URL}administrator/application-settings/save`, {body: data});
                const messageType = res.success ? "alert-success" : "alert-danger";
                showAlert(res.message || Object.values(res.errors || {}).join("<br/>"), messageType);
            } catch (error) {
                console.error("Submission error:", error);
                showAlert("Gagal memproses penyimpanan konfigurasi.", "alert-danger");
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="cil-save me-2"></i> Simpan Konfigurasi';
            }
        });
    });
</script>
<?= $this->endSection() ?>
