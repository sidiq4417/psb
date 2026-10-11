<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="SCHOOLS"></div>
</div>
<script>
    const _grid = "SCHOOLS", _form = _grid + "_FORM";
    new DataGrid(_grid, {
        title: "Asal Sekolah",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            { title: "Nama", field: "school_name" },
            { title: "Alamat", field: "school_address" }
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        ajaxUrl: _CURRENT_URL,
        title: "Asal Sekolah",
        fields: [
            { label: "Nama", name: "school_name", type: "text", required: true },
            { label: "Alamat", name: "school_address", type: "textarea", required: true }
        ],
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
