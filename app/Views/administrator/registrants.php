<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="row row-cards mb-3">
    <div class="col-sm-6 col-md-3">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="admission_year">
                <option value="0">Semua Tahun Penerimaan</option>
                <?php foreach ($admission_years as $year) { ?>
                    <option value="<?= (int) $year ?>" <?= (int) $year === (int) $active_admission_year ? 'selected' : '' ?>><?= (int) $year ?></option>
                <?php } ?>
            </select>
            <label class="form-label fw-bolder text-dark">Tahun Penerimaan</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="selection_status">
                <option value="">Semua Status Seleksi</option>
                <?php foreach (registration_status() as $key => $val) { ?>
                    <option value="<?= esc((string) $key, 'attr') ?>"><?= esc($val) ?></option>
                <?php } ?>
            </select>
            <label class="form-label fw-bolder text-dark">Status Seleksi</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="school_id">
                <option value="0">Semua Sekolah</option>
                <?php
                if (isset($schools)) {
                    $objs = json_decode($schools);
                    foreach ($objs as $id => $name) {
                        echo '<option value="' . esc((string) $id, 'attr') . '">' . esc((string) $name) . '</option>';
                    }
                }
                ?>
            </select>
            <label class="form-label fw-bolder text-dark">Sekolah</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="entry_grade_id">
                <option value="0">Semua Tingkat Masuk</option>
                <?php
                if (isset($entry_grades)) {
                    $objs = json_decode($entry_grades);
                    foreach ($objs as $id => $name) {
                        echo '<option value="' . esc((string) $id, 'attr') . '">' . esc((string) $name) . '</option>';
                    }
                }
                ?>
            </select>
            <label class="form-label fw-bolder text-dark">Tingkat Masuk</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="education_track_id">
                <option value="0">Semua Jenis Kelas</option>
                <?php
                if (isset($education_tracks)) {
                    $objs = json_decode($education_tracks);
                    foreach ($objs as $id => $name) {
                        echo '<option value="' . esc((string) $id, 'attr') . '">' . esc((string) $name) . '</option>';
                    }
                }
                ?>
            </select>
            <label class="form-label fw-bolder text-dark">Jenis Kelas</label>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="education_program_id">
                <option value="0">Semua Program Pendidikan</option>
                <?php
                if (isset($education_programs)) {
                    $objs = json_decode($education_programs);
                    foreach ($objs as $id => $name) {
                        echo '<option value="' . esc((string) $id, 'attr') . '">' . esc((string) $name) . '</option>';
                    }
                }
                ?>
            </select>
            <label class="form-label fw-bolder text-dark">Program Pendidikan</label>
        </div>
    </div>
</div>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="REGISTRANTS"></div>
</div>
<script>

    /**
     * Reload the grid using the filters currently selected above it.
     */
    async function reloadRegistrants() {
        const value = (name, fallback) => {
            const el = document.querySelector(`[name="${name}"]`);
            return el ? el.value : fallback;
        };
        await render(value("admission_year", 0), value("selection_status", ""), value("school_id", 0),
            value("entry_grade_id", 0), value("education_track_id", 0), value("education_program_id", 0));
    }

    document.addEventListener("DOMContentLoaded", async () => {
        const admission_year_select = document.querySelector('[name="admission_year"]');
        const selection_status_select = document.querySelector('[name="selection_status"]');
        const school_id_select = document.querySelector('[name="school_id"]');
        const entry_grade_id_select = document.querySelector('[name="entry_grade_id"]');
        const education_track_id_select = document.querySelector('[name="education_track_id"]');
        const education_program_id_select = document.querySelector('[name="education_program_id"]');

        const reloadData = async () => {
            const admission_year = admission_year_select ? admission_year_select.value : 0;
            const selection_status = selection_status_select ? selection_status_select.value : "";
            const school_id = school_id_select ? school_id_select.value : 0;
            const entry_grade_id = entry_grade_id_select ? entry_grade_id_select.value : 0;
            const education_track_id = education_track_id_select ? education_track_id_select.value : 0;
            const education_program_id = education_program_id_select ? education_program_id_select.value : 0;
            await render(admission_year, selection_status, school_id, entry_grade_id, education_track_id, education_program_id);
        };

        if (admission_year_select) admission_year_select.addEventListener('change', reloadData);
        if (selection_status_select) selection_status_select.addEventListener('change', reloadData);
        if (school_id_select) school_id_select.addEventListener('change', reloadData);
        if (entry_grade_id_select) entry_grade_id_select.addEventListener('change', reloadData);
        if (education_track_id_select) education_track_id_select.addEventListener('change', reloadData);
        if (education_program_id_select) education_program_id_select.addEventListener('change', reloadData);

        await reloadData();
    });
    DS.Gender = <?= $gender ?>;
    DS.Countries = <?= $countries ?>;
    DS.Religions = <?= $religions ?>;
    DS.Residences = <?= $residences ?>;
    DS.Transportations = <?= $transportations ?>;
    DS.Educations = <?= $educations ?>;
    DS.Occupations = <?= $occupations ?>;
    DS.Incomes = <?= $incomes ?>;
    DS.RegistrationStatuses = <?= $registration_statuses ?>;
    const _grid = "REGISTRANTS", _form = _grid + "_FORM", _form2 = _grid + "_FORM2";
    const countryOptions = Object.assign({ 99: "Lainnya" }, DS.Countries);
    const REGION_URL = _BASE_URL + "administrator/registrants";
    const REGION_OTHER = { 99: "Lainnya" };
    // Region selects of the "Perbaikan Data Calon Siswa" form, from the top level down
    // (bp = birth place, ad = address).
    const REGION_CHAINS = {
        bp: ["birth_place_country_id", "birth_place_province_id", "birth_place_district_id"],
        ad: ["address_country_id", "address_province_id", "address_district_id", "address_subdistrict_id"]
    };
    // One counter per chain: a newer load of the same chain makes the older one stale.
    const regionTokens = { bp: 0, ad: 0 };
    // Above zero while the script fills a region select: setting its options or value fires a
    // change event, which must not start the cascade (that would clear the level being loaded).
    let regionsSilent = 0;
    // State of the saved regions of the open form: "loading", "ready" or "failed".
    // Saving is refused until they are loaded, otherwise empty regions would overwrite stored data.
    let regionsState = "ready";
    let regionsGeneration = 0;

    async function regionGet(url, params) {
        const body = new FormData();
        for (const key in params) body.set(key, params[key]);
        const res = await H.AjaxRequest(url, { body });
        return res || {};
    }

    /**
     * Replace the options (and optionally the value) of a region select without starting the cascade.
     * FormBuilder.updateOptions applies them (and fires the change events) before it returns.
     *
     * @param {FormBuilder} fb
     * @param {string} field
     * @param {Object} options
     * @param {?string} value
     * @returns {Promise<void>}
     */
    function setRegionOptions(fb, field, options, value = null) {
        if (!fb.fieldByName[field]) return Promise.resolve();
        regionsSilent++;
        try {
            return fb.updateOptions(field, options, value);
        } finally {
            regionsSilent--;
        }
    }

    /**
     * Fetch the options of one region level for the value chosen one level above.
     *
     * @param {number} level  1 = province, 2 = regency/city, 3 = subdistrict.
     * @param {string} parent Value of the level above (country, province or regency/city).
     * @returns {Promise<Object>} The options, always including "Lainnya".
     */
    async function regionOptions(level, parent) {
        if (level === 1) {
            if (parent !== "ID") return REGION_OTHER;
            const res = await regionGet(REGION_URL + "/provinces", {});
            return Object.assign({}, REGION_OTHER, res.provinces || {});
        }
        if (!parent || parent === "99") return REGION_OTHER;
        if (level === 2) {
            const res = await regionGet(REGION_URL + "/districts", { province_id: parent });
            return Object.assign({}, REGION_OTHER, res.districts || {});
        }
        const res = await regionGet(REGION_URL + "/subdistricts", { district_id: parent });
        return Object.assign({}, REGION_OTHER, res.subdistricts || {});
    }

    /**
     * Refill the levels of a region chain below the level `from`, one level at a time:
     * each level's options are awaited before its saved value is set, and only then is
     * the next level loaded, so a saved regency or subdistrict is not lost while loading.
     *
     * @param {FormBuilder} fb
     * @param {string} key      "bp" or "ad".
     * @param {number} from     Index of the level whose value is known (0 = country).
     * @param {Array} values    Value per level: values[from] is the chosen value, deeper
     *                          entries are saved values to restore (empty to leave unset).
     * @returns {Promise<boolean>} False when a newer load of the same chain took over.
     */
    async function fillRegions(fb, key, from, values) {
        const chain = REGION_CHAINS[key];
        const tok = ++regionTokens[key];
        const text = (v) => (v === undefined || v === null) ? "" : String(v);
        // Reset the deeper levels first, so no choice of the previous parent remains.
        for (let i = from + 1; i < chain.length; i++) {
            await setRegionOptions(fb, chain[i], REGION_OTHER);
        }
        let parent = text(values[from]);
        for (let i = from + 1; i < chain.length; i++) {
            const options = await regionOptions(i, parent);
            if (tok !== regionTokens[key]) return false;
            const saved = text(values[i]);
            const value = saved !== "" && Object.prototype.hasOwnProperty.call(options, saved) ? saved : null;
            await setRegionOptions(fb, chain[i], options, value);
            if (value === null) break;
            parent = value;
        }
        return tok === regionTokens[key];
    }

    /**
     * Load the saved birth place and address regions of the opened applicant.
     *
     * @param {Object} values The applicant record.
     * @param {FormBuilder} fb
     */
    async function loadSavedRegions(values, fb) {
        const generation = ++regionsGeneration;
        regionsState = "loading";
        try {
            await fillRegions(fb, "bp", 0, [values.birth_place_country_id, values.birth_place_province_id, values.birth_place_district_id]);
            if (generation !== regionsGeneration) return;
            await fillRegions(fb, "ad", 0, [values.address_country_id, values.address_province_id, values.address_district_id, values.address_subdistrict_id]);
            if (generation !== regionsGeneration) return;
            regionsState = "ready";
        } catch (error) {
            if (generation !== regionsGeneration) return;
            regionsState = "failed";
            console.error(error);
            Alert.error("Data wilayah gagal dimuat. Tutup formulir lalu buka kembali sebelum menyimpan.");
        }
    }

    /**
     * Follow a change of a region select: refill the levels below it.
     *
     * @param {FormBuilder} fb
     * @param {string} field
     * @param {string} value
     */
    async function cascadeRegions(fb, field, value) {
        for (const key in REGION_CHAINS) {
            const level = REGION_CHAINS[key].indexOf(field);
            if (level < 0 || level === REGION_CHAINS[key].length - 1) continue;
            const values = [];
            values[level] = value;
            try {
                await fillRegions(fb, key, level, values);
            } catch (error) {
                console.error(error);
                Alert.error("Data wilayah gagal dimuat. Silakan pilih ulang.");
            }
        }
    }
    async function fetchData(url, params) {
        const res = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: params,
            cache: "no-cache"
        });
        if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
        return res.json();
    }

    async function render(admission_year, selection_status, school_id, entry_grade_id, education_track_id, education_program_id) {
        const res = await fetchData(_BASE_URL + "administrator/registrants/lists", JSON.stringify({ admission_year, selection_status, school_id, entry_grade_id, education_track_id, education_program_id }));
        const columns = [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    let btn = '';
                    btn += '<div class="p-1 hstack gap-1 justify-content-center">';
                    btn += H.BtnIcon("check", "Verifikasi Berkas & Pembayaran", _form + ".onEdit(" + row.id + ")");
                    btn += H.BtnIcon("user", "Perbaikan Data Calon Siswa", _form2 + ".onEdit(" + row.id + ")");
                    if (row.submitted_at && Number(row.is_deleted) !== 1) btn += H.LinkIcon("print", "Cetak Formulir Pendaftaran", _BASE_URL + "administrator/registrants/print/" + row.id, "right");
                    btn += H.BtnIcon("notes", "Lihat Nilai", "viewStudentGrades(" + row.id + ")");
                    btn += H.BtnIcon("file", "Lihat Dokumen", "viewStudentDocuments(" + row.id + ")");
                    btn += H.BtnIcon("check-circle", "Input Nilai Ujian", "viewEntranceExamScores(" + row.id + ")");
                    btn += '</div>';
                    return btn;
                }
            },
            {
                title: "No. Registrasi",
                field: "registration_number",
                styles: ["text-center"]
            },
            { title: "Nama Lengkap", field: "full_name", styles: "text-nowrap" },
            {
                title: "Status Seleksi",
                field: "selection_status",
                formatter: function (row) {
                    const status = DS.RegistrationStatuses[row.selection_status];
                    if (!status) return '<span class="chip opacity-50">Belum Diproses</span>';
                    let color = "secondary", active = "";
                    switch (row.selection_status) {
                        case "1": color = "info"; break;
                        case "2": color = "secondary"; break;
                        case "3": color = "warning"; break;
                        case "4": color = "success"; active = "chip-clickable"; break;
                        case "5": color = "primary"; break;
                        case "6": color = "danger"; active = "chip-clickable"; break;
                        case "7": color = "success"; active = "chip-clickable"; break;
                    }
                    return `<span class="chip chip-${color} ${active}">${H.Esc(status)}</span>`;
                },
                styles: ["text-nowrap"]
            },
            {
                title: "Tahun",
                field: "admission_year",
                styles: "text-center",
                hidden: true
            },
            { title: "L/P", field: "gender", styles: "text-center", hidden: true },
            { title: "Email", field: "email", hidden: true },
            { title: "Telephone", field: "telephone", styles: "text-nowrap", hidden: true },
            { title: "HP", field: "mobile_phone", styles: "text-nowrap", hidden: true },
            { title: "WhatsApp", field: "whatsapp", styles: "text-nowrap", hidden: true },
            { title: "Asal Sekolah", field: "school_name", styles: "text-nowrap", hidden: true },
            // Program Pilihan
            { title: "Sekolah", field: "branch", styles: "text-nowrap" },
            { title: "Tingkat Masuk", field: "entry_grade", styles: "text-nowrap" },
            { title: "Jenis Kelas", field: "education_track", styles: "text-nowrap" },
            { title: "Periode", field: "admission_period", styles: ["text-nowrap", "text-center"] },
            { title: "Program Pendidikan", field: "education_program", styles: "text-nowrap" },
            // Registrations
            { title: "Konfirmasi Pendaftaran", field: "submitted", formatter: "boolean" },
            { title: "Tanggal Konfirmasi Pendaftaran", field: "submitted_at", styles: "text-nowrap" },
            // Bukti Pembayaran
            { title: "Verifikasi Biaya Pendaftaran", field: "transfer_proof_verified", formatter: "boolean" },
            { title: "Tanggal Verifikasi Biaya Pendaftaran", field: "transfer_proof_verified_at", styles: "text-nowrap" },
            // Profile
            { title: "NIK", field: "nik", hidden: true },
            { title: "NPWP", field: "npwp", hidden: true },
            { title: "Kewarganegaraan", field: "citizenship", hidden: true },
            { title: "Negara Tempat Lahir", field: "birth_country", hidden: true },
            { title: "Tempat Lahir", field: "birth_place", hidden: true },
            { title: "Tanggal Lahir", field: "birth_date", hidden: true },
            {
                title: "Agama",
                field: "religion_id",
                formatter: function (row) {
                    return H.Esc(DS.Religions[row.religion_id] || '');
                },
                hidden: true
            },
            { title: "Penerima KPS?", field: "kps_receiver", hidden: true },
            { title: "Nomor KPS", field: "kps", hidden: true },
            // School
            { title: "NISN", field: "nisn", hidden: true },
            { title: "NPSN", field: "npsn", hidden: true },
            { title: "Alamat Sekolah/Sekolah Asal", field: "school_address", hidden: true },
            { title: "Nama Sekolah/Sekolah Asal", field: "school_name", hidden: true },
            { title: "Jurusan Sekolah/Sekolah Asal", field: "school_major", hidden: true },
            { title: "Tahun Lulus", field: "school_graduation_year", hidden: true },
            // Address
            { title: "Alamat", field: "address", hidden: true },
            { title: "Dusun", field: "village", hidden: true },
            { title: "RT", field: "rt", hidden: true },
            { title: "RW", field: "rw", hidden: true },
            { title: "Kelurahan", field: "urban_village", hidden: true },
            { title: "Kecamatan", field: "district_id", hidden: true },
            {
                title: "Jenis Tinggal",
                field: "residence_id",
                formatter: function (row) {
                    return H.Esc(DS.Residences[row.residence_id] || '');
                },
                hidden: true
            },
            {
                title: "Alat Transportasi",
                field: "transportation_id",
                formatter: function (row) {
                    return H.Esc(DS.Transportations[row.transportation_id] || '');
                },
                hidden: true
            },
            { title: "Kode Pos", field: "post_code", hidden: true },
            // Contact
            { title: "Email", field: "email", hidden: true },
            { title: "Telephone", field: "telephone", hidden: true },
            { title: "HP", field: "mobile_phone", hidden: true },
            { title: "Telegram", field: "telegram", hidden: true },
            { title: "Facebook", field: "facebook", hidden: true },
            { title: "Twitter/X", field: "twitter", hidden: true },
            { title: "Instagram", field: "instagram", hidden: true },
            { title: "TikTok", field: "tiktok", hidden: true },
            // Parent
            { title: "NIK Ayah", field: "father_nik", hidden: true },
            { title: "Nama Ayah", field: "father_name", hidden: true },
            { title: "Tanggal Lahir Ayah", field: "father_birth_date", hidden: true },
            {
                title: "Pendidikan Ayah",
                field: "father_education_id",
                formatter: function (row) {
                    return H.Esc(DS.Educations[row.father_education_id] || '');
                },
                hidden: true
            },
            {
                title: "Pekerjaan Ayah",
                field: "father_occupation_id",
                formatter: function (row) {
                    return H.Esc(DS.Occupations[row.father_occupation_id] || '');
                },
                hidden: true
            },
            {
                title: "Penghasilan Ayah",
                field: "father_income_id",
                formatter: function (row) {
                    return H.Esc(DS.Incomes[row.father_income_id] || '');
                },
                hidden: true
            },
            { title: "NIK Ibu", field: "mother_nik", hidden: true },
            { title: "Nama Ibu", field: "mother_name", hidden: true },
            { title: "Tanggal Lahir Ibu", field: "mother_birth_date", hidden: true },
            {
                title: "Pendidikan Ibu",
                field: "mother_education_id",
                formatter: function (row) {
                    return H.Esc(DS.Educations[row.mother_education_id] || '');
                },
                hidden: true
            },
            {
                title: "Pekerjaan Ibu",
                field: "mother_occupation_id",
                formatter: function (row) {
                    return H.Esc(DS.Occupations[row.mother_occupation_id] || '');
                },
                hidden: true
            },
            {
                title: "Penghasilan Ibu",
                field: "mother_income_id",
                formatter: function (row) {
                    return H.Esc(DS.Incomes[row.mother_income_id] || '');
                },
                hidden: true
            },
            { title: "NIK Wali", field: "guardian_nik", hidden: true },
            { title: "Nama Wali", field: "guardian_name", hidden: true },
            { title: "Tempat Lahir Wali", field: "guardian_birth_date", hidden: true },
            {
                title: "Pendidikan Wali",
                field: "guardian_education_id",
                formatter: function (row) {
                    return H.Esc(DS.Educations[row.guardian_education_id] || '');
                },
                hidden: true
            },
            {
                title: "Penghasilan Wali",
                field: "guardian_income_id",
                formatter: function (row) {
                    return H.Esc(DS.Incomes[row.guardian_income_id] || '');
                },
                hidden: true
            },
            {
                title: "Pekerjaan Wali",
                field: "guardian_occupation_id",
                formatter: function (row) {
                    return H.Esc(DS.Occupations[row.guardian_occupation_id] || '');
                },
                hidden: true
            },
        ];
        // for (const i in subject_grade_requirements) {
        //     const subject = subject_grade_requirements[i];
        //     const column = {
        //         title: `${subject.subject_name}<br><span class="badge text-bg-light">Nilai Min. ${subject.threshold_score}</span>`,
        //         field: `subject_grade_requirement_${subject.subject_id}`,
        //         formatter: function (row) {
        //             const subject_grades = H.StrToObject(row.subject_grades);
        //             let score = 0;
        //             for (const i in subject_grades) {
        //                 const data = subject_grades[i];
        //                 if (subject.subject_id === data.subject_id) {
        //                     score = +data.score || 0;
        //                     break;
        //                 }
        //             }
        //             return `<input value="${score}" onfocusout="updateSubjectScore(this, ${row.id}, ${subject.subject_id})" type="number" class="form-control text-center" placeholder="Masukan nilai ${subject.subject_name}">`
        //         },
        //         sortable: false,
        //         filterable: false,
        //         styles: ["text-center", "bg-body-tertiary"]
        //     }
        //     columns.push(column);
        // }
        // for (const i in document_requirements) {
        //     const document = document_requirements[i];
        //     const column = {
        //         title: `${document.document_name}`,
        //         field: `document_requirement_${document.document_id}`,
        //         formatter: function (row) {
        //             const document_requirements = H.StrToObject(row.document_requirements);
        //             for (const i in document_requirements) {
        //                 const data = document_requirements[i];
        //                 if (document.document_id === data.document_id) {
        //                     return `<a href="${_BASE_URL}administrator/registrants/download/${data.file}" class="btn btn-light text-body btn-sm"><i class="cil-data-transfer-down"></i></a>`;
        //                 }
        //             }
        //         },
        //         sortable: false,
        //         filterable: false,
        //         styles: "text-center"
        //     }
        //     columns.push(column);
        // }
        DS.Registrants = {};
        res.records.forEach(function (r) { DS.Registrants[r.id] = r; });

        new DataGrid(_grid, {
            title: "Data Calon Siswa Baru",
            columns: columns,
            records: res.records,
            showToolbox: false,
            canAdd: false
        });
    }

    function viewStudentGrades(regId) {
        const row = DS.Registrants[regId];
        if (!row) return;
        const grades = (H.StrToObject(row.subject_grade_requirements) || []).map(function (g) {
            return Object.assign({}, g, { score: +g.score || 0, threshold_score: +g.threshold_score || 0 });
        });
        if (!grades.length) { alert('Belum ada data nilai.'); return; }
        const total = grades.length;
        const filled = grades.filter(function (g) { return g.score > 0; }).length;
        const avg = filled > 0 ? Math.round(grades.reduce(function (sum, g) { return sum + (g.score || 0); }, 0) / total) : 0;
        const passed = grades.filter(function (g) { return g.score > 0 && g.score >= g.threshold_score; }).length;
        let html = '<div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border"><div class="d-flex align-items-center justify-content-center rounded-3" style="width:48px;height:48px;background:#fff3e0"><i class="cil-notes fs-3 text-warning"></i></div><div><div class="fw-bold text-dark" style="font-size:0.9rem">Rata-rata Nilai</div><div class="text-muted small">' + filled + ' dari ' + total + ' mata pelajaran telah dinilai</div></div><div class="ms-auto d-flex gap-3"><div class="text-center"><div class="fw-bold fs-5 text-dark">' + avg + '</div><div class="text-muted small" style="font-size:0.6rem">RATA-RATA</div></div><div class="text-center"><div class="fw-bold fs-5 text-' + (passed === filled ? 'success' : 'danger') + '">' + passed + '/' + filled + '</div><div class="text-muted small" style="font-size:0.6rem">LULUS</div></div></div></div>';
        html += '<div class="row g-2">';
        grades.forEach(function (g) {
            const hasScore = g.score > 0;
            const meetThreshold = hasScore && g.score >= g.threshold_score;
            const statusIcon = hasScore ? (meetThreshold ? 'cil-check-circle text-success' : 'cil-x-circle text-danger') : 'cil-minus text-muted';
            const statusLabel = hasScore ? (meetThreshold ? 'Lulus' : 'Tidak') : 'Belum';
            const borderClass = hasScore ? (meetThreshold ? 'border-success border-opacity-25' : 'border-danger border-opacity-10') : '';
            const scoreBadge = hasScore ? '<span class="badge bg-' + (meetThreshold ? 'success' : 'danger') + ' bg-opacity-10 text-' + (meetThreshold ? 'success' : 'danger') + ' fs-6 px-3 py-1 rounded-pill fw-bold">' + H.Esc(g.score) + '</span>' : '<span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill">-</span>';
            html += '<div class="col-md-6"><div class="d-flex align-items-center gap-2 p-3 border rounded-3 bg-white ' + borderClass + '"><div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width:36px;height:36px;background:#f8fafc"><i class="' + statusIcon + ' fs-5"></i></div><div class="flex-grow-1 min-w-0"><div class="fw-semibold text-dark text-truncate small">' + H.Esc(g.subject_name || 'Mata Pelajaran #' + g.subject_id) + '</div><div class="text-muted small">Min. ' + H.Esc(g.threshold_score || 0) + '</div></div>' + scoreBadge + '</div></div>';
        });
        html += '</div>';
        showModal('Nilai Calon Siswa', html);
    }

    function viewStudentDocuments(regId) {
        const row = DS.Registrants[regId];
        if (!row) return;
        const docs = (H.StrToObject(row.document_requirements) || []).slice();
        if (row.transfer_proof) {
            docs.push({ document_id: 0, document_name: 'Bukti Pembayaran', file_name: row.transfer_proof });
        }
        if (!docs.length) { alert('Belum ada data dokumen.'); return; }
        const total = docs.length;
        const uploaded = docs.filter(function (d) { return d.file_name; }).length;
        let html = '<div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border"><div class="d-flex align-items-center justify-content-center rounded-3" style="width:48px;height:48px;background:#e8f1ff"><i class="cil-file fs-3 text-primary"></i></div><div><div class="fw-bold text-dark" style="font-size:0.9rem">Kelengkapan Dokumen</div><div class="text-muted small">' + uploaded + ' dari ' + total + ' dokumen telah diunggah</div></div><div class="ms-auto"><span class="badge bg-' + (uploaded === total ? 'success' : 'warning') + ' fs-6 px-3 py-2 rounded-pill">' + Math.round(uploaded / total * 100) + '%</span></div></div>';
        html += '<div class="row g-2">';
        docs.forEach(function (d) {
            const hasFile = d.file_name ? true : false;
            const fileLink = hasFile ? '<a href="' + H.Esc(_BASE_URL + 'administrator/registrants/download/' + encodeURIComponent(d.file_name)) + '" class="btn btn-sm btn-outline-primary rounded-2"><i class="cil-data-transfer-down me-1"></i>Download</a>' : '<span class="text-muted small fst-italic">Belum diunggah</span>';
            const icon = hasFile ? 'cil-check-circle text-success' : 'cil-file text-muted';
            html += '<div class="col-md-6"><div class="d-flex align-items-center gap-2 p-3 border rounded-3 bg-white ' + (hasFile ? 'border-success border-opacity-25' : '') + '"><div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width:36px;height:36px;background:#f8fafc"><i class="' + icon + ' fs-5"></i></div><div class="flex-grow-1 min-w-0"><div class="fw-semibold text-dark text-truncate small">' + H.Esc(d.document_name || 'Dokumen #' + d.document_id) + '</div><div class="mt-1">' + fileLink + '</div></div></div></div>';
        });
        html += '</div>';
        showModal('Dokumen Calon Siswa', html);
    }

    async function viewEntranceExamScores(regId) {
        regId = parseInt(regId, 10) || 0;
        const res = await fetchData(_BASE_URL + "administrator/registrants/entrance-exam-subjects", JSON.stringify({ reg_id: regId }));
        const subjects = res.subjects || [];
        const existing = res.existing_scores || [];
        if (!subjects.length) { alert('Belum ada pengaturan mata ujian.'); return; }
        const scoreMap = {};
        existing.forEach(function (s) { scoreMap[s.subject_id] = s.score; });
        const total = subjects.length;
        const filled = subjects.filter(function (s) { return scoreMap[s.subject_id] > 0; }).length;
        let html = '<div id="entrance-summary" class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border"><div class="d-flex align-items-center justify-content-center rounded-3" style="width:48px;height:48px;background:#f3e8ff"><i class="cil-check-circle fs-3 text-purple" style="color:#8b5cf6"></i></div><div><div class="fw-bold text-dark" style="font-size:0.9rem">Input Nilai Ujian</div><div class="text-muted small">' + filled + ' dari ' + total + ' mata ujian sudah diisi</div></div><div class="ms-auto"><span class="badge bg-' + (filled === total ? 'success' : 'secondary') + ' fs-6 px-3 py-2 rounded-pill">' + Math.round(filled / total * 100) + '%</span></div></div>';
        html += '<div class="row g-2 mb-3">';
        subjects.forEach(function (sub) {
            const val = scoreMap[sub.subject_id] || 0;
            const hasScore = val > 0;
            const meetThreshold = hasScore && val >= sub.threshold_score;
            const borderClass = hasScore ? (meetThreshold ? 'border-success border-opacity-25' : 'border-danger border-opacity-10') : '';
            html += '<div class="col-md-6"><div class="d-flex align-items-center gap-2 p-3 border rounded-3 bg-white ' + borderClass + '"><div class="flex-shrink-0" style="width:36px"><i class="cil-education fs-5 text-secondary"></i></div><div class="flex-grow-1 min-w-0"><div class="fw-semibold text-dark text-truncate small">' + H.Esc(sub.subject) + '</div><div class="text-muted small">Min. ' + H.Esc(sub.threshold_score) + '</div></div><div style="width:90px"><input type="number" class="form-control form-control-sm text-center entrance-exam-input border-2 ' + (hasScore ? (meetThreshold ? 'border-success' : 'border-danger') : '') + '" data-reg-id="' + regId + '" data-subject-id="' + H.Esc(sub.subject_id) + '" value="' + H.Esc(val) + '" min="0" max="100" step="1" placeholder="0"></div></div></div>';
        });
        html += '</div>';
        html += '<div class="d-flex justify-content-end pt-2 border-top"><button class="btn btn-primary btn-sm rounded-2 px-4 fw-bold" onclick="saveAllEntranceScores(' + regId + ')"><i class="cil-save me-1"></i>Simpan Semua</button></div>';
        showModal('Nilai Ujian Saringan Masuk', html);
    }

    async function saveAllEntranceScores(regId) {
        const inputs = document.querySelectorAll('.entrance-exam-input');
        const promises = [];
        inputs.forEach(function (inp) {
            const subjectId = inp.getAttribute('data-subject-id');
            const score = inp.value;
            promises.push(fetchData(_BASE_URL + "administrator/registrants/update-entrance-exam-score", JSON.stringify({ reg_id: regId, subject_id: subjectId, score: score })));
        });
        const results = await Promise.all(promises);
        const failed = results.find(function (r) { return !r || !r.success; });
        if (failed) {
            Alert.error((failed && failed.errors && failed.errors.score) || 'Sebagian nilai ujian gagal disimpan.');
            return;
        }

        const total = inputs.length;
        const filled = Array.from(inputs).filter(function (inp) { return +inp.value > 0; }).length;
        const pct = Math.round(filled / total * 100);
        const header = document.getElementById('entrance-summary');
        if (header) {
            header.innerHTML = '<div class="d-flex align-items-center justify-content-center rounded-3" style="width:48px;height:48px;background:#f3e8ff"><i class="cil-check-circle fs-3 text-purple" style="color:#8b5cf6"></i></div><div><div class="fw-bold text-dark" style="font-size:0.9rem">Input Nilai Ujian</div><div class="text-muted small">' + filled + ' dari ' + total + ' mata ujian sudah diisi</div></div><div class="ms-auto"><span class="badge bg-' + (filled === total ? 'success' : 'secondary') + ' fs-6 px-3 py-2 rounded-pill">' + pct + '%</span></div>';
        }
        Alert.success('Nilai ujian berhasil disimpan.');
    }

    function showModal(title, bodyHtml) {
        const modalId = 'VIEW_MODAL-' + Date.now();
        const modalEl = document.createElement('div');
        modalEl.className = 'modal fade';
        modalEl.id = modalId;
        modalEl.tabIndex = -1;
        modalEl.setAttribute('role', 'dialog');
        modalEl.setAttribute('aria-modal', 'true');
        modalEl.innerHTML = '<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">' +
            '<div class="modal-content border-0 rounded-0">' +
            '<div class="modal-header"><h5 class="modal-title fw-bold">' + title + '</h5>' +
            '<button type="button" class="btn-close" data-coreui-dismiss="modal"></button></div>' +
            '<div class="modal-body">' + bodyHtml + '</div></div></div>';
        document.body.appendChild(modalEl);
        const modal = new coreui.Modal(modalEl);
        modal.show();
        modalEl.addEventListener('hidden.coreui.modal', function () { modalEl.remove(); });
    }

    new FormBuilder(_form, {
        ajaxUrl: _BASE_URL + "administrator/registrants",
        title: "Data Calon Siswa Baru",
        canDelete: false,
        fields: [
            // The dates are shown only: the server stamps them when a checkbox is switched on and clears them when it is switched off.
            { label: "Konfirmasi Pendaftaran", name: "submitted", type: "checkbox" },
            { label: "Tanggal Konfirmasi Pendaftaran", name: "submitted_at", disabled: true, placeholder: "Terisi otomatis saat dikonfirmasi" },
            { label: "Verifikasi Biaya Pendaftaran", name: "transfer_proof_verified", type: "checkbox" },
            { label: "Tanggal Verifikasi Biaya Pendaftaran", name: "transfer_proof_verified_at", disabled: true, placeholder: "Terisi otomatis saat diverifikasi" },
            { label: "Status Seleksi", name: "selection_status", type: "select", options: DS.RegistrationStatuses, placeholder: "Pilih status kelulusan...", dependency: { field: "submitted", value: true } },
            { label: "Catatan Seleksi", name: "selection_notes", type: "textarea", placeholder: "Masukan catatan seleksi, alasan diterima/ditolak, dll.", dependency: { field: "submitted", value: true } },
        ],
        beforeSave: (nv) => {
            nv.admission_year = document.querySelector('[name="admission_year"]').value;
        },
        afterSave: reloadRegistrants,
        afterDelete: reloadRegistrants,
        afterRestore: reloadRegistrants
    });

    new FormBuilder(_form2, {
        ajaxUrl: _BASE_URL + "administrator/registrants",
        formAction: "form-student",
        saveAction: "save-student",
        title: "Perbaikan Data Calon Siswa",
        canDelete: false,
        fields: [
            { type: "header", label: "Data Pribadi" },
            { label: "Nama Lengkap", name: "full_name", required: true },
            { label: "NIK / No. Paspor (WNA)", name: "nik", required: true },
            { label: "NPWP", name: "npwp" },
            { label: "Jenis Kelamin", name: "gender", type: "select", options: DS.Gender, required: true },
            { label: "Kewarganegaraan", name: "citizenship", type: "select", options: DS.Countries },
            { label: "Agama", name: "religion_id", type: "select", options: DS.Religions },
            { label: "Penerima KPS?", name: "kps_receiver", type: "select", options: { 0: "Tidak", 1: "Ya" } },
            { label: "Nomor KPS", name: "kps" },

            { type: "header", label: "Tempat & Tanggal Lahir" },
            { label: "Negara Tempat Lahir", name: "birth_place_country_id", type: "select", options: countryOptions },
            { label: "Provinsi Tempat Lahir", name: "birth_place_province_id", type: "select", options: { 99: "Lainnya" } },
            { label: "Kab/Kota Tempat Lahir", name: "birth_place_district_id", type: "select", options: { 99: "Lainnya" } },
            { label: "Kota Tempat Lahir (isi jika lahir di luar negeri atau tidak ada di daftar)", name: "birth_place" },
            { label: "Tanggal Lahir", name: "birth_date", type: "date" },

            { type: "header", label: "Alamat" },
            { label: "Negara", name: "address_country_id", type: "select", options: countryOptions },
            { label: "Provinsi", name: "address_province_id", type: "select", options: { 99: "Lainnya" } },
            { label: "Kabupaten/Kota", name: "address_district_id", type: "select", options: { 99: "Lainnya" } },
            { label: "Kecamatan", name: "address_subdistrict_id", type: "select", options: { 99: "Lainnya" } },
            { label: "Jalan", name: "street_address" },
            { label: "Dusun", name: "village" },
            { label: "RT", name: "rt" },
            { label: "RW", name: "rw" },
            { label: "Kelurahan", name: "urban_village" },
            { label: "Kode Pos", name: "post_code" },
            { label: "Jenis Tinggal", name: "residence_id", type: "select", options: DS.Residences },
            { label: "Alat Transportasi", name: "transportation_id", type: "select", options: DS.Transportations },

            { type: "header", label: "Kontak" },
            { label: "Telephone", name: "telephone" },
            { label: "HP", name: "mobile_phone" },
            { label: "WhatsApp", name: "whatsapp" },
            { label: "Telegram", name: "telegram" },
            { label: "Facebook", name: "facebook" },
            { label: "Twitter/X", name: "twitter" },
            { label: "Instagram", name: "instagram" },
            { label: "TikTok", name: "tiktok" },

            { type: "header", label: "Orang Tua" },
            { label: "NIK Ayah", name: "father_nik" },
            { label: "Nama Ayah", name: "father_name" },
            { label: "Tanggal Lahir Ayah", name: "father_birth_date", type: "date" },
            { label: "Pendidikan Ayah", name: "father_education_id", type: "select", options: DS.Educations },
            { label: "Pekerjaan Ayah", name: "father_occupation_id", type: "select", options: DS.Occupations },
            { label: "Penghasilan Ayah", name: "father_income_id", type: "select", options: DS.Incomes },
            { label: "NIK Ibu", name: "mother_nik" },
            { label: "Nama Ibu Kandung", name: "mother_name" },
            { label: "Tanggal Lahir Ibu", name: "mother_birth_date", type: "date" },
            { label: "Pendidikan Ibu", name: "mother_education_id", type: "select", options: DS.Educations },
            { label: "Pekerjaan Ibu", name: "mother_occupation_id", type: "select", options: DS.Occupations },
            { label: "Penghasilan Ibu", name: "mother_income_id", type: "select", options: DS.Incomes },

            { type: "header", label: "Wali" },
            { label: "NIK Wali", name: "guardian_nik" },
            { label: "Nama Wali", name: "guardian_name" },
            { label: "Tanggal Lahir Wali", name: "guardian_birth_date", type: "date" },
            { label: "Pendidikan Wali", name: "guardian_education_id", type: "select", options: DS.Educations },
            { label: "Pekerjaan Wali", name: "guardian_occupation_id", type: "select", options: DS.Occupations },
            { label: "Penghasilan Wali", name: "guardian_income_id", type: "select", options: DS.Incomes },

            { type: "header", label: "Asal Sekolah" },
            { label: "NISN", name: "nisn" },
            { label: "NPSN", name: "npsn" },
            { label: "Nama Asal Sekolah", name: "school_name" },
            { label: "Alamat Asal Sekolah", name: "school_address" },
            { label: "Jurusan", name: "school_major" },
            { label: "Tahun Lulus", name: "school_graduation_year" },

            { type: "header", label: "Kata Sandi" },
            { label: "Kata Sandi Baru", name: "password", type: "text", placeholder: "Kosongkan jika tidak ingin mengubah kata sandi" }
        ],
        afterFetch: (values) => {
            values.password = '';
            regionsState = "loading";
        },
        onShow: loadSavedRegions,
        onFieldChange: (field, value, fb) => {
            if (regionsSilent > 0) return;
            cascadeRegions(fb, field, value);
        },
        onValidate: () => {
            if (regionsState === "loading") {
                Alert.warning("Data wilayah masih dimuat. Tunggu sebentar, lalu simpan kembali.");
                return false;
            }
            if (regionsState === "failed") {
                Alert.error("Data wilayah gagal dimuat. Tutup formulir lalu buka kembali sebelum menyimpan.");
                return false;
            }
            return true;
        },
        afterSave: reloadRegistrants
    });

    async function updateSubjectScore(e, id, subject_id) {
        const res = await fetchData(_BASE_URL + "administrator/registrants/update-subject-score", JSON.stringify({
            score: e.value,
            id: id,
            subject_id: subject_id
        }));
        if (!res.success) console.error(res.errors);
    }

</script>
<?= $this->endSection() ?>
