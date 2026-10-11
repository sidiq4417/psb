<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var int $district_id */
?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="SUBDISTRICTS"></div>
</div>
<script>
    const _grid = "SUBDISTRICTS";
    const district_id = +'<?=$district_id?>';

    new DataGrid(_grid, {
        title: "Kecamatan",
        url: _BASE_URL + "administrator/subdistricts/lists",
        fetchOptions: {
            method: "POST",
            body: (() => {
                const form = new FormData();
                form.append("district_id", district_id.toString());
                return form;
            })()
        },
        columns: [
            {title: "Kode", field: "subdistrict_id"},
            {title: "Kecamatan", field: "subdistrict_name"},
        ],
        canAdd: false
    });
</script>
<?= $this->endSection() ?>
