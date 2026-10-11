<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="GENERAL_REQUIREMENTS"></div>
</div>
<script>
    const _grid = "GENERAL_REQUIREMENTS";
    new DataGrid(_grid, {
        title: "Persyaratan Umum",
        url: _CURRENT_URL + "/lists",
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    let btn = '<div class="hstack gap-1">';
                    btn += H.LinkIcon("book", "Persyaratan Umum Nilai", _BASE_URL + "administrator/general-subject-grade-requirements/" + row.id, "right");
                    btn += H.LinkIcon("file", "Persyaratan Umum Dokumen", _BASE_URL + "administrator/general-document-requirements/" + row.id, "right");
                    btn += H.LinkIcon("check-circle", "Persyaratan Umum Ujian Saringan Masuk", _BASE_URL + "administrator/general-entrance-exam-scores/" + row.id, "right");
                    btn += '</div>';
                    return btn;
                }
            },
            {title: "Tahun Ajaran", field: "name"},
            {title: "Tanggal Mulai", field: "start_date"},
            {title: "Tanggal Selesai", field: "end_date"},
        ],
        canAdd: false,
        canToggleColumn: false
    });
</script>
<?= $this->endSection() ?>
