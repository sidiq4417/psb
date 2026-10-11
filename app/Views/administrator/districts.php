<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var int $province_id */
?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="DISTRICTS"></div>
</div>
<script>
    const _grid = "DISTRICTS";
    const province_id = '<?= esc((string) $province_id, 'js') ?>';

    new DataGrid(_grid, {
        title: "Kabupaten/Kota",
        url: _BASE_URL + "administrator/districts/lists",
        fetchOptions: {
            method: "POST",
            body: (() => {
                const form = new FormData();
                form.append("province_id", province_id.toString());
                return form;
            })()
        },
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.LinkIcon("search", "Kecamatan", _BASE_URL + "administrator/subdistricts/" + row.district_id, "right");
                }
            },
            {title: "Kode", field: "district_id"},
            {title: "Kabupaten/Kota", field: "district_name"},
        ],
        canAdd: false
    });
</script>
<?= $this->endSection() ?>
