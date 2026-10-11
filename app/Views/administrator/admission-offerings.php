<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var array $academic_years */
/** @var array $admission_periods */
/** @var array $period_academic_year_map */
/** @var array $schools */
/** @var array $entry_grades */
/** @var array $education_tracks */
/** @var array $education_programs */
/** @var array $applicant_types */
/** @var array $admission_pathways */
/** @var array $branchSchoolTypes */
/** @var array $entryGradeSchoolMap */
?>
<div class="row row-cards mb-3">
    <div class="col-sm-8 col-md-4">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="academic_year_id" required>
                <option value="">-- Pilih Tahun Ajaran --</option>
                <?php foreach ($academic_years as $k => $v) { ?>
                    <option value="<?= esc((string) $k, 'attr') ?>"><?= esc((string) $v) ?></option>
                <?php } ?>
            </select>
            <label class="form-label fw-bolder text-dark">Tahun Ajaran <sup class="text-danger">*</sup></label>
        </div>
    </div>
    <div class="col-sm-8 col-md-4">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="admission_period_id">
                <option value="0">Semua Periode</option>
                <?php foreach ($admission_periods as $k => $v) { ?>
                    <option value="<?= esc((string) $k, 'attr') ?>"><?= esc((string) $v) ?></option>
                <?php } ?>
            </select>
            <label class="form-label fw-bolder text-dark">Periode Pendaftaran</label>
        </div>
    </div>
    <div class="col-sm-8 col-md-4">
        <div class="form-floating border-light-subtle shadow-sm mb-2">
            <select class="form-select form-select-sm" name="branch_id">
                <option value="0">Semua Sekolah</option>
                <?php foreach ($schools as $k => $v) { ?>
                    <option value="<?= esc((string) $k, 'attr') ?>"><?= esc((string) $v) ?></option>
                <?php } ?>
            </select>
            <label class="form-label fw-bolder text-dark">Sekolah</label>
        </div>
    </div>
    <div class="col-12" id="FILTER_WARNING"></div>
</div>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="ADMISSION_OFFERINGS"></div>
</div>
<script>
    DS.AcademicYears = <?= toJson($academic_years) ?>;
    DS.AdmissionPeriods = <?= toJson($admission_periods) ?>;
    DS.PeriodAcademicYearMap = <?= toJson($period_academic_year_map) ?>;
    DS.Branches = <?= toJson($schools) ?>;
    DS.EntryGrades = <?= toJson($entry_grades) ?>;
    DS.EducationTracks = <?= toJson($education_tracks) ?>;
    DS.EducationPrograms = <?= toJson($education_programs) ?>;
    DS.ApplicantTypes = <?= toJson($applicant_types) ?>;
    DS.AdmissionPathways = <?= toJson($admission_pathways) ?>;
    DS.BranchSchoolTypes = <?= toJson($branchSchoolTypes) ?>;
    DS.EntryGradeSchoolMap = <?= toJson($entryGradeSchoolMap) ?>;
    const _grid = "ADMISSION_OFFERINGS", _form = _grid + "_FORM";

    function getSchoolType(schoolId) {
        return DS.BranchSchoolTypes[schoolId] || '';
    }

    const hideTypes = ['SD', 'MI', 'SMP', 'MTs'];

    function getSchoolIdFromEntryGrade(entryGradeId) {
        return DS.EntryGradeSchoolMap[entryGradeId] || 0;
    }

    function updateProgramField(schoolId) {
        const progField = document.querySelector('[name="education_program_id"]');
        if (!progField) return;
        const formGroup = progField.closest('.mb-3');
        if (!formGroup) return;
        const hidden = hideTypes.includes(getSchoolType(schoolId));
        formGroup.style.display = hidden ? 'none' : '';
        // A hidden field must not still send a program chosen earlier.
        if (hidden) {
            const ts = window[_form]?.fieldByName?.education_program_id?.ts;
            if (ts) ts.setValue('0'); else progField.value = '0';
        }
    }

    const datatable = new DataGrid(_grid, {
        title: "Pembukaan Pendaftaran",
        url: _CURRENT_URL + "/lists",
        fetchOptions: { method: "POST" },
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return `
                        <div class="d-flex flex-nowrap gap-1">
                            ${H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")}
                            ${H.BtnIcon("notes", "Nilai", "window.open('" + _BASE_URL + "administrator/specific-subject-grade-requirements/" + row.id + "', '_self')")}
                            ${H.BtnIcon("file", "Dokumen", "window.open('" + _BASE_URL + "administrator/specific-document-requirements/" + row.id + "', '_self')")}
                            ${H.BtnIcon("check-circle", "Ujian", "window.open('" + _BASE_URL + "administrator/specific-entrance-exam-scores/" + row.id + "', '_self')")}
                        </div>
                    `;
                }
            },
            {
                title: "Periode",
                field: "period_name",
                sortable: false,
                filterable: false
            },
            {
                title: "Sekolah",
                field: "school",
                formatter: function (row) {
                    return H.Esc(DS.Branches[row.branch_id] || row.school_name);
                },
                sortable: false,
                filterable: false
            },
            {
                title: "Tingkat",
                field: "grade",
                formatter: function (row) {
                    return H.Esc(row.grade_name);
                },
                sortable: false,
                filterable: false
            },
            {
                title: "Jenis Kelas",
                field: "track",
                formatter: function (row) {
                    return H.Esc(DS.EducationTracks[row.education_track_id] || row.track_name);
                },
                sortable: false,
                filterable: false
            },
            {
                title: "Program Pendidikan",
                field: "program",
                formatter: function (row) {
                    return Number(row.education_program_id) > 0 ? H.Esc(DS.EducationPrograms[row.education_program_id] || row.program_name) : '-';
                },
                sortable: false,
                filterable: false
            },
            {
                title: "Jenis Pendaftaran",
                field: "applicant_type",
                formatter: function (row) {
                    return H.Esc(DS.ApplicantTypes[row.applicant_type_id] || row.applicant_type_name || '-');
                },
                sortable: false,
                filterable: false
            },
            {
                title: "Jalur Masuk",
                field: "pathway",
                formatter: function (row) {
                    return H.Esc(DS.AdmissionPathways[row.admission_pathway_id] || row.pathway_name || '-');
                },
                sortable: false,
                filterable: false
            },
            {
                title: "Informasi Lainnya",
                field: "info",
                formatter: function (row) {
                    return `
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center text-nowrap text-small p-1">Kuota<span class="text-nowrap fw-bold">${H.Esc(row.quota)}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-nowrap text-small p-1">Pendaftar<span class="text-nowrap fw-bold">${H.Esc(row.total_registrant || 0)}</span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-nowrap text-small p-1">Submit<span class="text-nowrap fw-bold">${H.Esc(row.total_submitted || 0)}</span></li>
                        </ul>
                    `;
                },
                sortable: false,
                filterable: false
            }
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Pembukaan Pendaftaran",
        fields: [
            {label: "Periode Pendaftaran", name: "admission_period_id", type: "select", options: DS.AdmissionPeriods, required: true},
            {label: "Tingkat Masuk", name: "entry_grade_id", type: "select", options: DS.EntryGrades, required: true},
            {label: "Jenis Kelas", name: "education_track_id", type: "select", options: DS.EducationTracks, required: true},
            // "0" (no program) comes first, so a new offering has no program unless one is chosen.
            {label: "Program Pendidikan", name: "education_program_id", type: "select", options: Object.assign({0: "-- Kosongkan --"}, DS.EducationPrograms), placeholder: "-- Kosongkan --"},
            {label: "Jenis Pendaftaran", name: "applicant_type_id", type: "select", options: DS.ApplicantTypes, required: true},
            {label: "Jalur Masuk", name: "admission_pathway_id", type: "select", options: DS.AdmissionPathways, required: true},
            {label: "Kuota", name: "quota", type: "number", required: true},
            {label: "Urutan Tampilan", name: "sort_order", type: "number"},
        ],
        grids: [_grid]
    });

    function populatePeriodOptions(academicYearId) {
        const periodSelect = document.querySelector('select[name="admission_period_id"]');
        if (!periodSelect) return;
        const currentVal = periodSelect.value;
        periodSelect.innerHTML = '<option value="0">Semua Periode</option>';
        Object.keys(DS.AdmissionPeriods).forEach(id => {
            if (!academicYearId || DS.PeriodAcademicYearMap[id] == academicYearId) {
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = DS.AdmissionPeriods[id];
                periodSelect.appendChild(opt);
            }
        });
        if (periodSelect.querySelector(`option[value="${currentVal}"]`)) {
            periodSelect.value = currentVal;
        } else {
            periodSelect.value = "0";
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const filterFields = ['academic_year_id', 'admission_period_id', 'branch_id'];
        const warningEl = document.getElementById('FILTER_WARNING');

        function doFetch() {
            const ayEl = document.querySelector('select[name="academic_year_id"]');
            const ayVal = ayEl?.value || '';
            if (!ayVal) {
                warningEl.innerHTML = '<div class="alert alert-warning py-2 mb-0 small">Pilih Tahun Ajaran terlebih dahulu untuk menampilkan data.</div>';
                datatable.options.data = [];
                datatable.refresh();
                return;
            }
            warningEl.innerHTML = '';
            const newForm = new FormData();
            filterFields.forEach(f => {
                const e = document.querySelector(`select[name="${f}"]`);
                if (e) newForm.append(f, e.value);
            });
            datatable.options.fetchOptions.body = newForm;
            datatable.refresh();
        }

        filterFields.forEach(v => {
            const el = document.querySelector(`select[name="${v}"]`);
            if (!el) return;
            el.addEventListener("change", doFetch);
        });

        const ayFilter = document.querySelector('select[name="academic_year_id"]');
        if (ayFilter) {
            ayFilter.addEventListener("change", function () {
                populatePeriodOptions(this.value);
            });
        }

        document.addEventListener("change", function (e) {
            if (e.target && e.target.matches('[name="entry_grade_id"]')) {
                const schoolId = getSchoolIdFromEntryGrade(e.target.value);
                updateProgramField(schoolId);
            }
        });

        const origOnEdit = window[_form].onEdit.bind(window[_form]);
        window[_form].onEdit = function (id) {
            origOnEdit(id);
            setTimeout(() => {
                const egField = document.querySelector('[name="entry_grade_id"]');
                const schoolId = getSchoolIdFromEntryGrade(egField?.value || 0);
                updateProgramField(schoolId);
            }, 150);
        };
    });
</script>
<?= $this->endSection() ?>
