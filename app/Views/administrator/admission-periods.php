<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var string $academic_years */
?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="ADMISSION_PERIODS"></div>
</div>
<script>
    DS.AcademicYears = H.StrToObject('<?=$academic_years?>');
    const _grid = "ADMISSION_PERIODS", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Periode Pendaftaran",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            {
                title: "Tahun Ajaran",
                field: "academic_year_id",
                formatter: function (row) {
                    return DS.AcademicYears[row.academic_year_id];
                }
            },
            {title: "Kode", field: "code"},
            {title: "Nama", field: "name"},
            {title: "Mulai", field: "registration_start_at"},
            {title: "Akhir", field: "registration_end_at"},
            {title: "Biaya", field: "registration_fee", formatter: function(row) {
                return row.registration_fee ? 'Rp ' + parseInt(row.registration_fee).toLocaleString('id-ID') : 'Gratis';
            }},
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Periode Pendaftaran",
        fields: [
            {label: "Tahun Ajaran", name: "academic_year_id", type: "select", options: DS.AcademicYears, required: true},
            {label: "Kode", name: "code", type: "text", required: true},
            {label: "Nama Periode", name: "name", type: "text", required: true},
            {label: "Mulai Pendaftaran", name: "registration_start_at", type: "date"},
            {label: "Akhir Pendaftaran", name: "registration_end_at", type: "date"},
            {label: "Biaya Pendaftaran", name: "registration_fee", type: "number"},
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
