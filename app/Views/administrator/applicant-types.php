<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="APPLICANT_TYPES"></div>
</div>
<script>
    const _grid = "APPLICANT_TYPES", _form = _grid + "_FORM";
    new DataGrid(_grid, {
        title: "Jenis Pendaftaran",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            { title: "Jenis Pendaftaran", field: "name" },
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        ajaxUrl: _CURRENT_URL,
        title: "Jenis Pendaftaran",
        fields: [
            { label: "Jenis Pendaftaran", name: "name" },
        ],
        grids: [_grid]
    });

</script>
<?= $this->endSection() ?>
