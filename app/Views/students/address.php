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

        <!-- SECTION 1: WILAYAH & LOKASI -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-map"></i></div>
                <h3 class="section-title">Wilayah & Lokasi</h3>
            </div>

            <?php if ($is_locked): ?>
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="cil-lock-locked me-2"></i> Data alamat Anda sudah dikunci dan tidak dapat diubah kembali.
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="address_country_id" class="form-select" name="address_country_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="99">Lainnya</option>
                            <?php foreach ($countries as $key => $value) { ?>
                                <option value="<?= $key ?>" <?= isset($row->address_country_id) && $row->address_country_id == $key ? 'selected' : '' ?>><?= $value ?></option>
                            <?php } ?>
                        </select>
                        <label for="address_country_id">Negara <sup class="text-danger">*</sup></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="address_province_id" class="form-select" name="address_province_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="99">Lainnya</option>
                        </select>
                        <label for="address_province_id">Provinsi <sup class="text-danger">*</sup></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="address_district_id" class="form-select" name="address_district_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="99">Lainnya</option>
                        </select>
                        <label for="address_district_id">Kabupaten/Kota <sup class="text-danger">*</sup></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="address_subdistrict_id" class="form-select" name="address_subdistrict_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="99">Lainnya</option>
                        </select>
                        <label for="address_subdistrict_id">Kecamatan <sup class="text-danger">*</sup></label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-floating">
                        <input id="urban_village" type="text" class="form-control" name="urban_village"
                               value="<?= $row->urban_village ?? '' ?>" placeholder="Kelurahan"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="urban_village">Kelurahan / Desa</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: DETAIL ALAMAT -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-location-pin"></i></div>
                <h3 class="section-title">Detail Alamat</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="street_address" type="text" class="form-control" name="street_address"
                               value="<?= $row->street_address ?? '' ?>" placeholder="Jalan"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="street_address">Nama Jalan / Blok / No. Rumah</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input id="village" type="text" class="form-control" name="village"
                               value="<?= $row->village ?? '' ?>" placeholder="Dusun"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="village">Dusun / Lingkungan</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="rt" type="text" class="form-control" name="rt"
                               value="<?= $row->rt ?? '' ?>" placeholder="RT"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="rt">RT</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="rw" type="text" class="form-control" name="rw"
                               value="<?= $row->rw ?? '' ?>" placeholder="RW"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="rw">RW</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input id="post_code" type="text" class="form-control" name="post_code"
                               value="<?= $row->post_code ?? '' ?>" placeholder="Kode Pos"
                               <?= $is_locked ? 'disabled' : '' ?>>
                        <label for="post_code">Kode Pos</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: INFORMASI PENDUKUNG -->
        <div class="settings-section">
            <div class="section-header">
                <div class="section-icon"><i class="cil-info"></i></div>
                <h3 class="section-title">Informasi Pendukung</h3>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="residence_id" class="form-select" name="residence_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="">Pilih:</option>
                            <?php foreach ($residences as $key => $value) { ?>
                                <option value="<?= $key ?>" <?= isset($row->residence_id) && $row->residence_id == $key ? 'selected' : '' ?>><?= $value ?></option>
                            <?php } ?>
                        </select>
                        <label for="residence_id">Tempat Tinggal</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <select id="transportation_id" class="form-select" name="transportation_id" <?= $is_locked ? 'disabled' : '' ?>>
                            <option value="">Pilih:</option>
                            <?php foreach ($transportations as $key => $value) { ?>
                                <option value="<?= $key ?>" <?= isset($row->transportation_id) && $row->transportation_id == $key ? 'selected' : '' ?>><?= $value ?></option>
                            <?php } ?>
                        </select>
                        <label for="transportation_id">Alat Transportasi</label>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$is_locked): ?>
            <div class="card-footer bg-white p-4 border-0 text-end">
                <button type="button" name="submit" class="btn btn-submit-modern shadow-sm">
                    <i class="cil-save me-2"></i> Simpan Alamat
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    let data = H.StrToObject('<?=toJson($row)?>');

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
            const res = await H.AjaxRequest(`${_BASE_URL}students/address/provinces`);
            return res.provinces;
        } catch (error) {
            console.error("Fetch error:", error);
        }
    }

    async function getDistricts(province_id) {
        try {
            const formData = new FormData();
            formData.set("province_id", province_id);
            const res = await H.AjaxRequest(`${_BASE_URL}students/address/districts`, { body: formData });
            return res.districts;
        } catch (error) {
            console.error("Fetch error:", error);
        }
    }

    async function getSubdistricts(district_id) {
        try {
            const formData = new FormData();
            formData.set("district_id", district_id);
            const res = await H.AjaxRequest(`${_BASE_URL}students/address/subdistricts`, { body: formData });
            return res.subdistricts;
        } catch (error) {
            console.error("Fetch error:", error);
        }
    }

    async function renderProvinces() {
        const provinceSelect = document.querySelector('[name="address_province_id"]');
        provinceSelect.innerHTML = '<option value="">Memuat...</option>';
        const provinces = await getProvinces();
        provinceSelect.innerHTML = '<option value="99">Lainnya</option>';
        for (const key in provinces) {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = provinces[key];
            if (key == (data.address_province_id || '')) option.selected = true;
            provinceSelect.appendChild(option);
        }
    }

    async function renderDistricts(province_id) {
        const districtSelect = document.querySelector('[name="address_district_id"]');
        districtSelect.innerHTML = '<option value="">Memuat...</option>';
        const districts = await getDistricts(province_id);
        districtSelect.innerHTML = '<option value="99">Lainnya</option>';
        if (districts) {
            for (const key in districts) {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = districts[key];
                if (key == (data.address_district_id || '')) option.selected = true;
                districtSelect.appendChild(option);
            }
        }
    }

    async function renderSubdistricts(district_id) {
        const subdistrictSelect = document.querySelector('[name="address_subdistrict_id"]');
        subdistrictSelect.innerHTML = '<option value="">Memuat...</option>';
        const subdistricts = await getSubdistricts(district_id);
        subdistrictSelect.innerHTML = '<option value="99">Lainnya</option>';
        if (subdistricts) {
            for (const key in subdistricts) {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = subdistricts[key];
                if (key == (data.address_subdistrict_id || '')) option.selected = true;
                subdistrictSelect.appendChild(option);
            }
        }
    }

    document.addEventListener("DOMContentLoaded", async () => {
        const countrySelect = document.querySelector('[name="address_country_id"]');
        const provinceSelect = document.querySelector('[name="address_province_id"]');
        const districtSelect = document.querySelector('[name="address_district_id"]');
        const submitBtn = document.querySelector('[name="submit"]');

        if (data.address_country_id === "ID") {
            await renderProvinces();
            if (data.address_province_id) await renderDistricts(data.address_province_id);
            if (data.address_district_id) await renderSubdistricts(data.address_district_id);
        }

        countrySelect.addEventListener("change", async () => {
            if (countrySelect.value === "ID") {
                await renderProvinces();
            } else {
                provinceSelect.innerHTML = '<option value="99">Lainnya</option>';
                districtSelect.innerHTML = '<option value="99">Lainnya</option>';
                document.querySelector('[name="address_subdistrict_id"]').innerHTML = '<option value="99">Lainnya</option>';
            }
        });

        provinceSelect.addEventListener("change", async () => {
            await renderDistricts(provinceSelect.value);
        });

        districtSelect.addEventListener("change", async () => {
            await renderSubdistricts(districtSelect.value);
        });

        if (submitBtn) {
            const inputNames = [
                "address_country_id", "address_province_id", "address_district_id", "address_subdistrict_id",
                "urban_village", "village", "street_address", "rt", "rw",
                "residence_id", "transportation_id", "post_code"
            ];

            submitBtn.addEventListener("click", async () => {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sedang Menyimpan...';

                try {
                    const formData = {};
                    inputNames.forEach(name => {
                        const input = document.querySelector(`[name="${name}"]`);
                        if (input) formData[name] = input.value.trim();
                    });

                    const res = await fetch(`${_BASE_URL}students/address/save`, {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify(formData),
                        cache: "no-cache",
                        mode: "cors"
                    });

                    const resData = await res.json();
                    const messageType = res.ok && resData.success ? "alert-success" : "alert-danger";

                    if (resData.success && resData.row) {
                        data = resData.row;
                        inputNames.forEach(name => {
                            const input = document.querySelector(`[name="${name}"]`);
                            if (input) input.value = data[name] ?? "";
                        });
                        if (data.address_country_id === "ID") {
                            await renderProvinces();
                            if (data.address_province_id) await renderDistricts(data.address_province_id);
                            if (data.address_district_id) await renderSubdistricts(data.address_district_id);
                        } else {
                            countrySelect.value = "99";
                            provinceSelect.innerHTML = '<option value="99">Lainnya</option>';
                            districtSelect.innerHTML = '<option value="99">Lainnya</option>';
                            document.querySelector('[name="address_subdistrict_id"]').innerHTML = '<option value="99">Lainnya</option>';
                        }
                    }

                    showAlert(resData.message || Object.values(resData.errors || {}).join("<br/>"), messageType);
                } catch (error) {
                    console.error("Submission error:", error);
                    showAlert("Terjadi kesalahan teknis saat menyimpan data.", "alert-danger");
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="cil-save me-2"></i> Simpan Alamat';
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
