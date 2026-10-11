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

        <!-- SECTION 1: IDENTITAS DASAR -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-user"></i></div>
                <h3 class="section-title">Identitas Dasar</h3>
            </div>

            <?php if ($is_locked): ?>
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="cil-lock-locked me-2"></i> Data profil Anda sudah dikunci dan tidak dapat diubah kembali.
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="full_name" type="text" class="form-control" name="full_name"
                               value="<?= esc($row->full_name ?? '', 'attr') ?>" placeholder="Nama Lengkap"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="full_name">Nama Lengkap (Sesuai Ijazah) <sup class="text-danger">*</sup></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="nik" type="text" class="form-control" name="nik"
                               value="<?= esc($row->nik ?? '', 'attr') ?>" placeholder="NIK"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="nik">NIK / No. Paspor (WNA) <sup class="text-danger">*</sup></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="npwp" type="text" class="form-control" name="npwp"
                               value="<?= esc($row->npwp ?? '', 'attr') ?>" placeholder="NPWP"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="npwp">NPWP</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select id="gender" class="form-select" name="gender" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="">Pilih:</option>
                            <option value="M" <?= isset($row->gender) && $row->gender == 'M' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="F" <?= isset($row->gender) && $row->gender == 'F' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                        <label for="gender">Jenis Kelamin <sup class="text-danger">*</sup></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <select id="citizenship" class="form-select" name="citizenship" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="">Pilih:</option>
                            <?php foreach ($countries as $key => $value) { ?>
                                <option value="<?= esc((string)$key, 'attr') ?>" <?= isset($row->citizenship) && $row->citizenship == $key ? 'selected' : '' ?>><?= esc($value) ?></option>
                            <?php } ?>
                        </select>
                        <label for="citizenship">Kewarganegaraan</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: TEMPAT & TANGGAL LAHIR -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-calendar"></i></div>
                <h3 class="section-title">Tempat & Tanggal Lahir</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="birth_place_country_id" class="form-select" name="birth_place_country_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="99">Lainnya</option>
                            <?php foreach ($countries as $key => $value) { ?>
                                <option value="<?= esc((string)$key, 'attr') ?>" <?= isset($row->birth_place_country_id) && $row->birth_place_country_id == $key ? 'selected' : '' ?>><?= esc($value) ?></option>
                            <?php } ?>
                        </select>
                        <label for="birth_place_country_id">Negara Tempat Lahir</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="birth_date" type="date" class="form-control" name="birth_date"
                               value="<?= esc($row->birth_date ?? '', 'attr') ?>" placeholder="Tanggal Lahir"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="birth_date">Tanggal Lahir</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="birth_place_province_id" class="form-select" name="birth_place_province_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="99">Lainnya</option>
                        </select>
                        <label for="birth_place_province_id">Provinsi Tempat Lahir</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="birth_place_district_id" class="form-select" name="birth_place_district_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="99">Lainnya</option>
                        </select>
                        <label for="birth_place_district_id">Kab/Kota Tempat Lahir</label>
                    </div>
                </div>
                <?php
                // With an Indonesian district chosen, birth_place holds the district name; the
                // free-text field is hidden then and starts empty so it never posts a stale value.
                $birth_place_text = (($row->birth_place_country_id ?? '') === 'ID' && !empty($row->birth_place_district_id)) ? '' : ($row->birth_place ?? '');
                ?>
                <div class="col-md-12" id="birth_place_text_wrapper">
                    <div class="form-floating">
                        <input id="birth_place" type="text" class="form-control" name="birth_place" maxlength="100"
                               value="<?= esc($birth_place_text, 'attr') ?>" placeholder="Kota Tempat Lahir"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="birth_place">Kota Tempat Lahir (isi jika lahir di luar negeri atau tidak ada di daftar)</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: DATA TAMBAHAN -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-list"></i></div>
                <h3 class="section-title">Informasi Tambahan</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="religion_id" class="form-select" name="religion_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="">Pilih:</option>
                            <?php foreach ($religions as $key => $value) { ?>
                                <option value="<?= esc((string)$key, 'attr') ?>" <?= isset($row->religion_id) && $row->religion_id == $key ? 'selected' : '' ?>><?= esc($value) ?></option>
                            <?php } ?>
                        </select>
                        <label for="religion_id">Agama</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="mother_name" type="text" class="form-control" name="mother_name"
                               value="<?= esc($row->mother_name ?? '', 'attr') ?>" placeholder="Nama Ibu Kandung"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="mother_name">Nama Ibu Kandung</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="kps_receiver" class="form-select" name="kps_receiver" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="">Pilih:</option>
                            <option value="1" <?= isset($row->kps_receiver) && $row->kps_receiver == '1' ? 'selected' : '' ?>>Ya</option>
                            <option value="0" <?= isset($row->kps_receiver) && $row->kps_receiver == '0' ? 'selected' : '' ?>>Tidak</option>
                        </select>
                        <label for="kps_receiver">Penerima KPS?</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="kps" type="text" class="form-control" name="kps"
                               value="<?= esc($row->kps ?? '', 'attr') ?>" placeholder="No. KPS"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="kps">No. KPS</label>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$is_locked): ?>
            <div class="card-footer bg-white p-4 border-0 text-end">
                <button type="button" name="submit" class="btn btn-submit-modern shadow-sm">
                    <i class="cil-save me-2"></i> Simpan Profil
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    let data = <?= toJson($row ?? (object) []) ?>;

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

    async function getProvinces() {
        try {
            const res = await H.AjaxRequest(`${_BASE_URL}students/profile/provinces`);
            return res.provinces;
        } catch (error) {
            console.error("Fetch error:", error);
        }
    }

    async function getDistricts(province_id) {
        try {
            const formData = new FormData();
            formData.set("province_id", province_id);
            const res = await H.AjaxRequest(`${_BASE_URL}students/profile/districts`, { body: formData });
            return res.districts;
        } catch (error) {
            console.error("Fetch error:", error);
        }
    }

    async function renderProvinces() {
        const provinceSelect = document.querySelector('[name="birth_place_province_id"]');
        provinceSelect.innerHTML = '<option value="">Memuat...</option>';
        const provinces = await getProvinces();
        provinceSelect.innerHTML = '<option value="99">Lainnya</option>';
        for (const key in provinces) {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = provinces[key];
            if (key == (data.birth_place_province_id || '')) option.selected = true;
            provinceSelect.appendChild(option);
        }
    }

    async function renderDistricts(province_id) {
        const districtSelect = document.querySelector('[name="birth_place_district_id"]');
        districtSelect.innerHTML = '<option value="">Memuat...</option>';
        const districts = await getDistricts(province_id);
        districtSelect.innerHTML = '<option value="99">Lainnya</option>';
        if (districts) {
            for (const key in districts) {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = districts[key];
                if (key == (data.birth_place_district_id || '')) option.selected = true;
                districtSelect.appendChild(option);
            }
        }
    }

    /**
     * Whether the free-text birth place is used: only when the birth country is
     * not Indonesia or no district (kab/kota) is chosen from the list.
     * @returns {boolean}
     */
    function usesBirthPlaceText() {
        const country = document.querySelector('[name="birth_place_country_id"]').value;
        const district = document.querySelector('[name="birth_place_district_id"]').value;
        return !(country === "ID" && district !== "99" && district !== "");
    }

    function toggleBirthPlaceText() {
        const wrapper = document.getElementById("birth_place_text_wrapper");
        if (wrapper) wrapper.classList.toggle("d-none", !usesBirthPlaceText());
    }

    document.addEventListener("DOMContentLoaded", async () => {
        const countrySelect = document.querySelector('[name="birth_place_country_id"]');
        const provinceSelect = document.querySelector('[name="birth_place_province_id"]');
        const districtSelect = document.querySelector('[name="birth_place_district_id"]');
        const submitBtn = document.querySelector('[name="submit"]');

        if (data.birth_place_country_id === "ID") {
            await renderProvinces();
            if (data.birth_place_province_id) await renderDistricts(data.birth_place_province_id);
        }
        toggleBirthPlaceText();

        countrySelect.addEventListener("change", async () => {
            if (countrySelect.value === "ID") {
                await renderProvinces();
            } else {
                provinceSelect.innerHTML = '<option value="99">Lainnya</option>';
                districtSelect.innerHTML = '<option value="99">Lainnya</option>';
            }
            toggleBirthPlaceText();
        });

        provinceSelect.addEventListener("change", async () => {
            await renderDistricts(provinceSelect.value);
            toggleBirthPlaceText();
        });

        districtSelect.addEventListener("change", toggleBirthPlaceText);

        if (submitBtn) {
            const inputNames = [
                "full_name", "nik", "npwp", "gender", "citizenship",
                "birth_place_country_id", "birth_place_province_id", "birth_place_district_id", "birth_place",
                "birth_date", "mother_name", "religion_id", "kps_receiver", "kps"
            ];

            submitBtn.addEventListener("click", async () => {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sedang Menyimpan...';

                try {
                    const formData = new FormData();
                    inputNames.forEach(name => {
                        const input = document.querySelector(`[name="${name}"]`);
                        if (input) formData.set(name, input.value);
                    });
                    // The hidden free-text field is not sent when a district is chosen.
                    if (!usesBirthPlaceText()) formData.set("birth_place", "");

                    const res = await H.AjaxRequest(`${_BASE_URL}students/profile/save`, { body: formData });
                    const messageType = res.success ? "alert-success" : "alert-danger";

                    if (res.success && res.row) {
                        data = res.row;
                        inputNames.forEach(name => {
                            const input = document.querySelector(`[name="${name}"]`);
                            if (input) input.value = data[name] ?? "";
                        });
                        // birth_place holds the derived district name then; keep the free-text field empty.
                        if (data.birth_place_country_id === "ID" && data.birth_place_district_id) {
                            document.querySelector('[name="birth_place"]').value = "";
                        }
                        if (data.birth_place_country_id === "ID") {
                            await renderProvinces();
                            if (data.birth_place_province_id) await renderDistricts(data.birth_place_province_id);
                        } else {
                            countrySelect.value = data.birth_place_country_id || "99";
                            provinceSelect.innerHTML = '<option value="99">Lainnya</option>';
                            districtSelect.innerHTML = '<option value="99">Lainnya</option>';
                        }
                        toggleBirthPlaceText();
                    }

                    showAlert(H.Esc(res.message || "") || Object.values(res.errors || {}).map(H.Esc).join("<br/>"), messageType);
                } catch (error) {
                    console.error("Submission error:", error);
                    showAlert("Terjadi kesalahan teknis saat menyimpan data.", "alert-danger");
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="cil-save me-2"></i> Simpan Profil';
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
