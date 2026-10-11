<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var int $academic_year_id */
/** @var string $subjects */
?>
<div class="row">
    <?= view('administrator/academic-year-header') ?>
</div>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="GENERAL_SUBJECT_GRADE_REQUIREMENTS"></div>
</div>
<script>
    const academic_year_id = <?= (int) $academic_year_id ?>;
    DS.Subjects = <?= is_string($subjects) ? $subjects : toJson($subjects) ?>;
    const _grid = "GENERAL_SUBJECT_GRADE_REQUIREMENTS", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Pengaturan Umum Nilai",
        url: _BASE_URL + "administrator/general-subject-grade-requirements/lists",
        fetchOptions: {
            method: "POST",
            body: (() => {
                const form = new FormData();
                form.append("academic_year_id", academic_year_id.toString());
                return form;
            })()
        },
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            {title: "Mata Pelajaran", field: "subject_name"},
            {title: "Nilai Minimum", field: "threshold_score", formatter: "number", styles: "text-end"},
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        ajaxUrl: _BASE_URL + "administrator/general-subject-grade-requirements",
        title: "Pengaturan Umum Nilai",
        fields: [
            {label: "Mata Pelajaran", name: "subject_id", type: "select", options: DS.Subjects, required: true},
            {label: "Nilai Minimum", name: "threshold_score", formatter: 'integer', required: true}
        ],
        beforeSave: (payload) => {
            payload.academic_year_id = academic_year_id;
        },
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
