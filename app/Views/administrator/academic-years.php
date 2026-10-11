<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php

?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="ACADEMIC_YEARS"></div>
</div>
<script>

    const _grid = "ACADEMIC_YEARS", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Tahun Ajaran",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            {title: "Tahun Ajaran", field: "name"},
            {title: "Mulai", field: "start_date"},
            {title: "Akhir", field: "end_date"},
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Tahun Ajaran",
        fields: [
            {label: "Tahun Ajaran", name: "name", type: "text", required: true},
            {label: "Tanggal Mulai", name: "start_date", type: "date"},
            {label: "Tanggal Akhir", name: "end_date", type: "date"},
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
