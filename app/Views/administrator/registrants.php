<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="row row-cards mb-3">
    <div class="col-sm-6 col-md-3">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="admission_year">
                <option value="0">Semua Tahun Penerimaan</option>
                <?php foreach ($admission_years as $year) { ?>
                    <option value="<?= $year ?>" <?= $year == $active_admission_year ? 'selected' : '' ?>><?= $year ?></option>
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
                    <option value="<?= $key ?>"><?= $val ?></option>
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
                        echo '<option value="' . $id . '">' . $name . '</option>';
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
                        echo '<option value="' . $id . '">' . $name . '</option>';
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
                        echo '<option value="' . $id . '">' . $name . '</option>';
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
                        echo '<option value="' . $id . '">' . $name . '</option>';
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
    DS.Gender = H.StrToObject('<?= $gender ?>');
    DS.Countries = H.StrToObject('<?= $countries ?>');
    DS.Religions = H.StrToObject('<?= $religions ?>');
    DS.Residences = H.StrToObject('<?= $residences ?>');
    DS.Transportations = H.StrToObject('<?= $transportations ?>');
    DS.Educations = H.StrToObject('<?= $educations ?>');
    DS.Occupations = H.StrToObject('<?= $occupations ?>');
    DS.Incomes = H.StrToObject('<?= $incomes ?>');
    DS.RegistrationStatuses = H.StrToObject('<?= $registration_statuses ?>');
    const _grid = "REGISTRANTS", _form = _grid + "_FORM", _form2 = _grid + "_FORM2";
    const countryOptions = Object.assign({ 99: "Lainnya" }, DS.Countries);
    const REGION_URL = _BASE_URL + "administrator/registrants";
    const regionTokens = { bp: 0, ad: 0 };
    async function regionGet(url, params) {
        const body = new FormData();
        for (const key in params) body.set(key, params[key]);
        const res = await H.AjaxRequest(url, { body });
        return res || {};
    }
    async function populateRegions(fb, prefix, country, vProv, vDist, vSub) {
        const hasSub = fb.fieldByName[prefix + "subdistrict_id"] != null;
        const other = { 99: "Lainnya" };
        if (country !== "ID") {
            await fb.updateOptions(prefix + "province_id", other);
            await fb.updateOptions(prefix + "district_id", other);
            if (hasSub) await fb.updateOptions(prefix + "subdistrict_id", other);
            return;
        }
        const provinces = await regionGet(REGION_URL + "/provinces", {});
        await fb.updateOptions(prefix + "province_id", Object.assign({}, other, provinces.provinces || {}), vProv || null);
        if (!vProv) return;
        const districts = await regionGet(REGION_URL + "/districts", { province_id: vProv });
        await fb.updateOptions(prefix + "district_id", Object.assign({}, other, districts.districts || {}), vDist || null);
        if (!vDist || !hasSub) return;
        const subdistricts = await regionGet(REGION_URL + "/subdistricts", { district_id: vDist });
        await fb.updateOptions(prefix + "subdistrict_id", Object.assign({}, other, subdistricts.subdistricts || {}), vSub || null);
    }
    async function cascadeStep(fb, field, value) {
        const token = (key) => ++regionTokens[key];
        const stale = (key, tok) => tok !== regionTokens[key];
        const other = { 99: "Lainnya" };
        if (field === "birth_place_country_id") {
            const tok = token("bp");
            await populateRegions(fb, "birth_place_", value);
            if (stale("bp", tok)) return;
        } else if (field === "birth_place_province_id") {
            const tok = token("bp");
            await fb.updateOptions("birth_place_district_id", other);
            if (value && value !== "99") {
                const districts = await regionGet(REGION_URL + "/districts", { province_id: value });
                if (stale("bp", tok)) return;
                await fb.updateOptions("birth_place_district_id", Object.assign({}, other, districts.districts || {}));
            }
        } else if (field === "address_country_id") {
            const tok = token("ad");
            await populateRegions(fb, "address_", value);
            if (stale("ad", tok)) return;
        } else if (field === "address_province_id") {
            const tok = token("ad");
            await fb.updateOptions("address_district_id", other);
            await fb.updateOptions("address_subdistrict_id", other);
            if (value && value !== "99") {
                const districts = await regionGet(REGION_URL + "/districts", { province_id: value });
                if (stale("ad", tok)) return;
                await fb.updateOptions("address_district_id", Object.assign({}, other, districts.districts || {}));
            }
        } else if (field === "address_district_id") {
            const tok = token("ad");
            await fb.updateOptions("address_subdistrict_id", other);
            if (value && value !== "99") {
                const subdistricts = await regionGet(REGION_URL + "/subdistricts", { district_id: value });
                if (stale("ad", tok)) return;
                await fb.updateOptions("address_subdistrict_id", Object.assign({}, other, subdistricts.subdistricts || {}));
            }
        }
    }
    async function fetchData(url, params) {
        const req = new Request(url, {
            method: "POST",
            headers: new Headers({
                "Content-Type": "application/json"
            }),
            body: params,
            cache: "no-cache",
            mode: "cors"
        });

        const res = await fetch(req);
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
                    return `<span class="chip chip-${color} ${active}">${status}</span>`;
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
                    return DS.Religions[row.religion_id] || '';
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
                    return DS.Residences[row.residence_id] || '';
                },
                hidden: true
            },
            {
                title: "Alat Transportasi",
                field: "transportation_id",
                formatter: function (row) {
                    return DS.Transportations[row.transportation_id] || '';
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
                    return DS.Educations[row.father_education_id] || '';
                },
                hidden: true
            },
            {
                title: "Pekerjaan Ayah",
                field: "father_occupation_id",
                formatter: function (row) {
                    return DS.Occupations[row.father_occupation_id] || '';
                },
                hidden: true
            },
            {
                title: "Penghasilan Ayah",
                field: "father_income_id",
                formatter: function (row) {
                    return DS.Incomes[row.father_income_id] || '';
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
                    return DS.Educations[row.mother_education_id] || '';
                },
                hidden: true
            },
            {
                title: "Pekerjaan Ibu",
                field: "mother_occupation_id",
                formatter: function (row) {
                    return DS.Occupations[row.mother_occupation_id] || '';
                },
                hidden: true
            },
            {
                title: "Penghasilan Ibu",
                field: "mother_income_id",
                formatter: function (row) {
                    return DS.Incomes[row.mother_income_id] || '';
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
                    return DS.Educations[row.guardian_education_id] || '';
                },
                hidden: true
            },
            {
                title: "Penghasilan Wali",
                field: "guardian_income_id",
                formatter: function (row) {
                    return DS.Incomes[row.guardian_income_id] || '';
                },
                hidden: true
            },
            {
                title: "Pekerjaan Wali",
                field: "guardian_occupation_id",
                formatter: function (row) {
                    return DS.Occupations[row.guardian_occupation_id] || '';
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
        const grades = H.StrToObject(row.subject_grade_requirements);
        if (!grades || !grades.length) { alert('Belum ada data nilai.'); return; }
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
            const scoreBadge = hasScore ? '<span class="badge bg-' + (meetThreshold ? 'success' : 'danger') + ' bg-opacity-10 text-' + (meetThreshold ? 'success' : 'danger') + ' fs-6 px-3 py-1 rounded-pill fw-bold">' + g.score + '</span>' : '<span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill">-</span>';
            html += '<div class="col-md-6"><div class="d-flex align-items-center gap-2 p-3 border rounded-3 bg-white ' + borderClass + '"><div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width:36px;height:36px;background:#f8fafc"><i class="' + statusIcon + ' fs-5"></i></div><div class="flex-grow-1 min-w-0"><div class="fw-semibold text-dark text-truncate small">' + (g.subject_name || 'Mata Pelajaran #' + g.subject_id) + '</div><div class="text-muted small">Min. ' + (g.threshold_score || 0) + '</div></div>' + scoreBadge + '</div></div>';
        });
        html += '</div>';
        showModal('Nilai Calon Siswa', html);
    }

    function viewStudentDocuments(regId) {
        const row = DS.Registrants[regId];
        if (!row) return;
        const docs = H.StrToObject(row.document_requirements);
        if (!docs || !docs.length) { alert('Belum ada data dokumen.'); return; }
        const total = docs.length;
        const uploaded = docs.filter(function (d) { return d.file_name; }).length;
        let html = '<div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border"><div class="d-flex align-items-center justify-content-center rounded-3" style="width:48px;height:48px;background:#e8f1ff"><i class="cil-file fs-3 text-primary"></i></div><div><div class="fw-bold text-dark" style="font-size:0.9rem">Kelengkapan Dokumen</div><div class="text-muted small">' + uploaded + ' dari ' + total + ' dokumen telah diunggah</div></div><div class="ms-auto"><span class="badge bg-' + (uploaded === total ? 'success' : 'warning') + ' fs-6 px-3 py-2 rounded-pill">' + Math.round(uploaded / total * 100) + '%</span></div></div>';
        html += '<div class="row g-2">';
        docs.forEach(function (d) {
            const hasFile = d.file_name ? true : false;
            const fileLink = hasFile ? '<a href="' + _BASE_URL + 'administrator/registrants/download/' + d.file_name + '" class="btn btn-sm btn-outline-primary rounded-2"><i class="cil-data-transfer-down me-1"></i>Download</a>' : '<span class="text-muted small fst-italic">Belum diunggah</span>';
            const icon = hasFile ? 'cil-check-circle text-success' : 'cil-file text-muted';
            html += '<div class="col-md-6"><div class="d-flex align-items-center gap-2 p-3 border rounded-3 bg-white ' + (hasFile ? 'border-success border-opacity-25' : '') + '"><div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width:36px;height:36px;background:#f8fafc"><i class="' + icon + ' fs-5"></i></div><div class="flex-grow-1 min-w-0"><div class="fw-semibold text-dark text-truncate small">' + (d.document_name || 'Dokumen #' + d.document_id) + '</div><div class="mt-1">' + fileLink + '</div></div></div></div>';
        });
        html += '</div>';
        showModal('Dokumen Calon Siswa', html);
    }

    async function viewEntranceExamScores(regId) {
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
            html += '<div class="col-md-6"><div class="d-flex align-items-center gap-2 p-3 border rounded-3 bg-white ' + borderClass + '"><div class="flex-shrink-0" style="width:36px"><i class="cil-education fs-5 text-secondary"></i></div><div class="flex-grow-1 min-w-0"><div class="fw-semibold text-dark text-truncate small">' + sub.subject + '</div><div class="text-muted small">Min. ' + sub.threshold_score + '</div></div><div style="width:90px"><input type="number" class="form-control form-control-sm text-center entrance-exam-input border-2 ' + (hasScore ? (meetThreshold ? 'border-success' : 'border-danger') : '') + '" data-reg-id="' + regId + '" data-subject-id="' + sub.subject_id + '" value="' + val + '" min="0" max="100" placeholder="0"></div></div></div>';
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
        await Promise.all(promises);

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
        fields: [
            { label: "Konfirmasi Pendaftaran", name: "submitted", type: "checkbox" },
            { label: "Tanggal Konfirmasi Pendaftaran", name: "submitted_at", type: "date" },
            { label: "Verifikasi Biaya Pendaftaran", name: "transfer_proof_verified", type: "checkbox" },
            { label: "Tanggal Verifikasi Biaya Pendaftaran", name: "transfer_proof_verified_at", type: "date" },
            { label: "Status Seleksi", name: "selection_status", type: "select", options: DS.RegistrationStatuses, placeholder: "Pilih status kelulusan...", dependency: { field: "submitted", value: true } },
            { label: "Catatan Seleksi", name: "selection_notes", type: "textarea", placeholder: "Masukan catatan seleksi, alasan diterima/ditolak, dll.", dependency: { field: "submitted", value: true } },
        ],
        beforeSave: (nv) => {
            nv.admission_year = document.querySelector('[name="admission_year"]').value;
        },
        afterSave: async () => {
            const year = document.querySelector('[name="admission_year"]').value;
            await render(year);
        },
        afterDelete: async () => {
            const year = document.querySelector('[name="admission_year"]').value;
            await render(year);
        },
        afterRestore: async () => {
            const year = document.querySelector('[name="admission_year"]').value;
            await render(year);
        }
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
            { label: "Tahun Lulus", name: "school_graduation_year", type: "integer" },

            { type: "header", label: "Kata Sandi" },
            { label: "Kata Sandi Baru", name: "password", type: "text", placeholder: "Kosongkan jika tidak ingin mengubah kata sandi" }
        ],
        afterFetch: (values) => {
            values.password = '';
        },
        onShow: async (values, fb) => {
            await populateRegions(fb, "birth_place_", values.birth_place_country_id, values.birth_place_province_id, values.birth_place_district_id, null);
            await populateRegions(fb, "address_", values.address_country_id, values.address_province_id, values.address_district_id, values.address_subdistrict_id);
        },
        onFieldChange: (field, value, fb) => {
            cascadeStep(fb, field, value);
        },
        afterSave: async () => {
            const year = document.querySelector('[name="admission_year"]').value;
            const selection_status = document.querySelector('[name="selection_status"]').value;
            const school_id = document.querySelector('[name="school_id"]').value;
            const entry_grade_id = document.querySelector('[name="entry_grade_id"]').value;
            const education_track_id = document.querySelector('[name="education_track_id"]').value;
            const education_program_id = document.querySelector('[name="education_program_id"]').value;
            await render(year, selection_status, school_id, entry_grade_id, education_track_id, education_program_id);
        }
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
