<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var int $academic_year_id */
/** @var string $documents */
?>
<div class="row">
    <?= view('administrator/academic-year-header') ?>
</div>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="GENERAL_DOCUMENT_REQUIREMENTS"></div>
</div>
<script>
    const academic_year_id = +'<?= $academic_year_id ?>';
    DS.Documents = H.StrToObject('<?= is_string($documents) ? $documents : json_encode($documents) ?>');
    const _grid = "GENERAL_DOCUMENT_REQUIREMENTS", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Dokumen Persyaratan",
        url: _BASE_URL + "administrator/general-document-requirements/lists",
        fetchOptions: {
            method: "POST",
            body: (() => {
                const form = new FormData();
                form.append("academic_year_id", academic_year_id.toString());
                return form;
            })()
        },
        columns: [
            { title: "A", field: "A", formatter: function (row) {
                return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
            }},
            { title: "Nama Dokumen", field: "document_name" },
            { title: "Ukuran Maksimum Dokumen (KB)", field: "max_size" },
            { title: "Dokumen Wajib?", field: "required", formatter: "boolean" }
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        ajaxUrl: _BASE_URL + "administrator/general-document-requirements",
        title: "Dokumen Persyaratan",
        fields: [
            { label: "Nama Dokumen", name: "document_id", type: "select", options: DS.Documents, required: true },
            { label: "Dokumen Wajib?", name: "required", type: "checkbox" }
        ],
        beforeSave: (nv) => { nv.academic_year_id = academic_year_id; },
        grids: [_grid]
    });
</script>
<?= $this->endSection() ?>
