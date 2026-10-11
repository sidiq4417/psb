<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="ADMISSION_PATHWAYS"></div>
</div>
<script>
    const _grid = "ADMISSION_PATHWAYS", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Jalur Masuk",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            {title: "Jalur Masuk", field: "name"},
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Jalur Masuk",
        fields: [
            {label: "Jalur Masuk", name: "name"},
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
