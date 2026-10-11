<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var int $offering_id */
/** @var string $subject_grade_requirements */
/** @var string $document_requirements */
/** @var string $gender */
/** @var string $countries */
/** @var string $religions */
/** @var string $residences */
/** @var string $transportations */
/** @var string $educations */
/** @var string $occupations */
/** @var string $incomes */
/** @var string $registration_statuses */
?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="STUDENTS"></div>
</div>
<script>
    const offering_id = <?= (int) $offering_id ?>;
    const subject_grade_requirements = <?= $subject_grade_requirements ?>;
    const document_requirements = <?= $document_requirements ?>;
    DS.Gender = <?= $gender ?>;
    DS.Countries = <?= $countries ?>;
    DS.Religions = <?= $religions ?>;
    DS.Residences = <?= $residences ?>;
    DS.Transportations = <?= $transportations ?>;
    DS.Educations = <?= $educations ?>;
    DS.Occupations = <?= $occupations ?>;
    DS.Incomes = <?= $incomes ?>;
    DS.RegistrationStatuses = <?= $registration_statuses ?>;
    const _grid = "STUDENTS", _form = _grid + "_FORM";

    const columns = [
        {
            title: "A",
            field: "A",
            formatter: function (row) {
                let btn = '';
                btn += '<div class="p-1 hstack gap-1 justify-content-center">';
                if (Number(row.is_deleted) !== 1) {
                    btn += H.BtnIcon("check", "Verifikasi Data", _form + ".onEdit(" + row.record_id + ")");
                    btn += H.LinkIcon("print", "Cetak Formulir Pendaftaran", _BASE_URL + "administrator/students/print/" + row.record_id, "right");
                }
                btn += '</div>';
                return btn;
            }
        },
        {
            title: "No. Registrasi",
            field: "registration_number",
            styles: ["text-nowrap", "text-center"]
        },
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
        { title: "Nama Lengkap", field: "full_name", styles: "text-nowrap" },
        { title: "L/P", field: "gender", styles: "text-center", hidden: true },
        { title: "Email", field: "email", hidden: true },
        { title: "Telephone", field: "telephone", styles: "text-nowrap", hidden: true },
        { title: "HP", field: "mobile_phone", styles: "text-nowrap", hidden: true },
        { title: "WhatsApp", field: "whatsapp", styles: "text-nowrap" },
        { title: "Asal Sekolah", field: "school_name", styles: "text-nowrap", hidden: true },

        // Registrations
        { title: "Konfirmasi Pendaftaran", field: "submitted", formatter: "boolean" },
        { title: "Tanggal Konfirmasi Pendaftaran", field: "submitted_at", styles: "text-nowrap" },
        // Bukti Pembayaran
        { title: "Verifikasi Biaya Pendaftaran", field: "transfer_proof_verified", formatter: "boolean" },
        {
            title: "Tanggal Verifikasi Biaya Pendaftaran",
            field: "transfer_proof_verified_at",
            styles: "text-nowrap",
        },
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
        { title: "Alamat Sekolah Asal", field: "school_address", hidden: true },
        { title: "Nama Sekolah Asal", field: "school_name", hidden: true },
        { title: "Jurusan Sekolah Asal", field: "school_major", hidden: true },
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
    for (const i in subject_grade_requirements) {
        const subject = subject_grade_requirements[i];
        const column = {
            title: `${H.Esc(subject.subject_name)}<br><span class="badge text-bg-light">Nilai Min. ${H.Esc(subject.threshold_score)}</span>`,
            field: `subject_grade_requirement_${subject.subject_id}`,
            formatter: function (row) {
                const subject_grades = H.StrToObject(row.subject_grade_requirements);
                let score = 0;
                for (const i in subject_grades) {
                    const data = subject_grades[i];
                    if (+subject.subject_id === +data.subject_id) {
                        score = +data.score || 0;
                        break;
                    }
                }
                return `<input value="${H.Esc(score)}" onfocusout="updateSubjectScore(this, ${parseInt(row.record_id, 10) || 0}, ${parseInt(subject.subject_id, 10) || 0})" type="number" min="0" max="100" step="0.01" class="form-control text-center" placeholder="Masukan nilai ${H.Esc(subject.subject_name)}">`
            },
            sortable: false,
            filterable: false,
            styles: ["text-center", "bg-body-tertiary"]
        }
        columns.push(column);
    }
    for (const i in document_requirements) {
        const document = document_requirements[i];
        const column = {
            title: H.Esc(document.document_name),
            field: `document_requirement_${document.document_id}`,
            formatter: function (row) {
                const student_documents = H.StrToObject(row.document_requirements);
                for (const i in student_documents) {
                    const data = student_documents[i];
                    if (+document.document_id === +data.document_id && data.file_name) {
                        return `<a href="${H.Esc(_BASE_URL + 'administrator/students/download/' + encodeURIComponent(data.file_name))}" class="btn btn-light text-body btn-sm"><i class="cil-data-transfer-down"></i></a>`;
                    }
                }
                return '-';
            },
            sortable: false,
            filterable: false,
            styles: "text-center"
        }
        columns.push(column);
    }
    new DataGrid(_grid, {
        title: "Data Calon Siswa Baru",
        url: _BASE_URL + "administrator/students/lists",
        fetchOptions: {
            method: "POST",
            body: (() => {
                const form = new FormData();
                form.append("offering_id", offering_id.toString());
                return form;
            })()
        },
        columns: columns,
        showToolbox: false,
        canAdd: false
    });

    new FormBuilder(_form, {
        ajaxUrl: _BASE_URL + "administrator/students",
        title: "Data Calon Siswa Baru",
        fields: [
            { label: "Konfirmasi Pendaftaran", name: "submitted", type: "checkbox" },
            { label: "Tanggal Konfirmasi Pendaftaran", name: "submitted_at", type: "date" },
            { label: "Verifikasi Biaya Pendaftaran", name: "transfer_proof_verified", type: "checkbox" },
            { label: "Tanggal Verifikasi Biaya Pendaftaran", name: "transfer_proof_verified_at", type: "date" },
            { label: "Status Seleksi", name: "selection_status", type: "select", options: DS.RegistrationStatuses, placeholder: "Pilih status kelulusan...", dependency: {field: "submitted", value: true} },
            { label: "Catatan Seleksi", name: "selection_notes", type: "textarea", placeholder: "Masukan catatan seleksi, alasan diterima/ditolak, dll.", dependency: {field: "submitted", value: true} },
        ],
        grids: [_grid]
    });

    async function fetchData(url, params = null) {
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

    async function updateSubjectScore(e, id, subject_id) {
        e.disabled = true;
        e.classList.add("bg-light");
        try {
            const res = await fetchData(_BASE_URL + "administrator/students/update-subject-score", JSON.stringify({
                score: e.value,
                id: id,
                subject_id: subject_id
            }));
            if (!res.success) {
                console.error(res.errors);
                alert(res.message || "Gagal memperbarui nilai!");
            }
        } catch (err) {
            console.error(err);
            alert("Terjadi kesalahan jaringan!");
        } finally {
            e.disabled = false;
            e.classList.remove("bg-light");
        }
    }
</script>
<?= $this->endSection() ?>
