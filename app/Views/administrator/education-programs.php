<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var string $branches */
?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="EDUCATION_PROGRAMS"></div>
</div>
<script>
    DS.Branches = H.StrToObject('<?=$branches?>');
    const _grid = "EDUCATION_PROGRAMS", _form = _grid + "_FORM";

    const _categories = {"MAJOR": "Jurusan", "SPECIALIZATION": "Peminatan", "VOCATIONAL_PROGRAM": "Program Keahlian", "SPECIAL_PROGRAM": "Program Khusus"};

    new DataGrid(_grid, {
        title: "Program Pendidikan",
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
                title: "Cabang Sekolah",
                field: "branch_id",
                formatter: function (row) {
                    return DS.Branches[row.branch_id];
                }
            },
            {title: "Kode", field: "code"},
            {title: "Program", field: "name"},
            {
                title: "Kategori",
                field: "category",
                formatter: function (row) {
                    return _categories[row.category] || row.category;
                }
            },
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Program Pendidikan",
        fields: [
            {label: "Cabang Sekolah", name: "branch_id", type: "select", options: DS.Branches, required: true},
            {label: "Kode", name: "code", type: "text", required: true},
            {label: "Nama Program", name: "name", type: "text", required: true},
            {label: "Kategori", name: "category", type: "select", options: _categories, required: true},
            {label: "Label Tampilan (Opsional)", name: "display_label", type: "text"},
            {label: "Deskripsi", name: "description", type: "textarea"},
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
