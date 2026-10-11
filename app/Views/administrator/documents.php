<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="DOCUMENTS"></div>
</div>
<script>
    const _grid = "DOCUMENTS", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Dokumen Persyaratan",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            {title: "Nama Dokumen", field: "name"},
            {title: "Ukuran Maksimum Dokumen (KB)", field: "max_size", formatter: 'number'}
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Dokumen Persyaratan",
        fields: [
            {label: "Nama Dokumen", name: "name", type: "text", required: true},
            {label: "Ukuran Maksimum Dokumen (KB)", name: "max_size", type: "number", required: true}
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
