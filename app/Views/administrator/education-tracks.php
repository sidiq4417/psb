<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="EDUCATION_TRACKS"></div>
</div>
<script>
    const _grid = "EDUCATION_TRACKS", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Jenis Kelas",
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
            {title: "Jenis Kelas", field: "name"},
            {title: "Default", field: "is_default"},
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Jenis Kelas",
        fields: [
            {label: "Kode", name: "code", type: "text", required: true},
            {label: "Jenis Kelas", name: "name", type: "text", required: true},
            {label: "Deskripsi", name: "description", type: "textarea"},
            {label: "Default?", name: "is_default", type: "select", options: {"true": "Ya", "false": "Tidak"}},
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
