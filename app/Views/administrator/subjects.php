<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="SUBJECTS"></div>
</div>
<script>
    const _grid = "SUBJECTS", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Mata Pelajaran",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            {title: "Mata Pelajaran", field: "name"}
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        ajaxUrl: _CURRENT_URL,
        title: "Mata Pelajaran",
        fields: [
            {label: "Mata Pelajaran", name: "name", type: "text", required: true}
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
