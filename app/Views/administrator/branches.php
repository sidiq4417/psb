<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var array $educationLevels */
?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="BRANCHES"></div>
</div>
<script>
    DS.EducationLevels = <?= toJson(array_combine($educationLevels, $educationLevels)) ?>;
    const _grid = "BRANCHES", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Sekolah",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            {title: "Kode", field: "code"},
            {title: "Nama Sekolah", field: "name"},
            {title: "Jenjang", field: "education_level"},
            {title: "Alamat", field: "address"},
            {title: "Telepon", field: "phone"},
            {title: "Email", field: "email"},
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Sekolah",
        fields: [
            {label: "Kode Sekolah", name: "code", type: "text", required: true},
            {label: "Nama Sekolah", name: "name", type: "text", required: true},
            {label: "Jenjang Pendidikan", name: "education_level", type: "select", options: DS.EducationLevels, required: true},
            {label: "Alamat", name: "address", type: "textarea"},
            {label: "Telepon", name: "phone", type: "text"},
            {label: "Email", name: "email", type: "email"},
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
