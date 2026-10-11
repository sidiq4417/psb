<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="OPERATOR"></div>
</div>
<script>
    const _grid = "OPERATOR", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Operator",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            {title: "Nama Lengkap", field: "full_name"},
            {title: "Email", field: "email"},
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        title: "Operator",
        fields: [
            {label: "Nama Lengkap", name: "full_name", type: "text", required: true},
            {label: "Email", name: "email", type: "text", required: true},
            {label: "Kata Sandi", name: "password", type: "password", placeholder: "Minimal 8 karakter. Kosongkan jika tidak ingin mengubah kata sandi"},
        ],
        onShow: function (nv, instance) {
            const password = instance.getElement("password");
            password.value = "";
        },
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
