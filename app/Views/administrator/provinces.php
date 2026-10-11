<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="PROVINCES"></div>
</div>
<script>
    const _grid = "PROVINCES";
    new DataGrid(_grid, {
        title: "Provinsi",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.LinkIcon("search", "Kabupaten/Kota", _BASE_URL + "administrator/districts/" + row.province_id, "right");
                }
            },
            {title: "Kode", field: "province_id"},
            {title: "Provinsi", field: "province_name"},
        ],
        canAdd: false
    });
</script>
<?= $this->endSection() ?>
