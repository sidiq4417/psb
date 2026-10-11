<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var string $branches */
?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="ENTRY_GRADES"></div>
</div>
<script>
    DS.Branches = <?= $branches ?>;
    const _grid = "ENTRY_GRADES", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Tingkat Masuk",
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
                    return H.Esc(DS.Branches[row.branch_id]);
                }
            },
            {title: "Tingkat", field: "name"},
            {title: "No Urut", field: "grade_number"},
            {title: "Urutan", field: "sort_order"},
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Tingkat Masuk",
        fields: [
            {label: "Cabang Sekolah", name: "branch_id", type: "select", options: DS.Branches, required: true},
            {label: "Nama Tingkat", name: "name", type: "text", required: true},
            {label: "Nomor Urut Kelas", name: "grade_number", type: "number"},
            {label: "Urutan Tampilan", name: "sort_order", type: "number"},
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
