<?= $this->extend('partials/layout') ?>
<?= $this->section('content') ?>
<?php
/** @var int $offering_id */
/** @var array $subjects */
/** @var object|null $offering */
?>
<style>
    .card-admission-header {
        border: 1px solid #e2e8f0;
        background: #fff;
        padding: 1.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        margin-bottom: 1.5rem;
    }

    .info-group-title {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.08em;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
    }

    .info-group-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f1f5f9;
        margin-left: 1rem;
    }

    .data-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .data-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        color: #475569;
        flex-shrink: 0;
        border: 1px solid #f1f5f9;
        transition: all 0.2s;
    }

    .data-item:hover .data-icon {
        background: #f1f5f9;
        color: #3b82f6;
    }

    .data-content {
        display: flex;
        flex-direction: column;
    }

    .data-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        line-height: normal;
        margin-bottom: 2px;
    }

    .data-value {
        font-size: 0.875rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    .badge-fee {
        background: #f0fdf4;
        color: #166534;
        padding: 2px 8px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 0.8rem;
    }

    @media (min-width: 768px) {
        .border-end-md {
            border-right: 1px solid #f1f5f9;
        }
    }
</style>

<?php if ($offering) { ?>
    <div class="col-12 px-0">
        <div class="card-admission-header">
            <div class="row g-4">
                <div class="col-md-6 border-end-md pe-md-4">
                    <div class="info-group-title">Pembukaan Pendaftaran</div>
                    <div class="data-item">
                        <div class="data-icon"><i class="cil-building"></i></div>
                        <div class="data-content">
                            <span class="data-label">Sekolah</span>
                            <span class="data-value"><?= $offering->school_name ?></span>
                        </div>
                    </div>
                    <div class="data-item">
                        <div class="data-icon"><i class="cil-education"></i></div>
                        <div class="data-content">
                            <span class="data-label">Tingkat</span>
                            <span class="data-value"><?= $offering->grade_name ?></span>
                        </div>
                    </div>
                    <div class="data-item">
                        <div class="data-icon"><i class="cil-chevron-right"></i></div>
                        <div class="data-content">
                            <span class="data-label">Jalur</span>
                            <span class="data-value"><?= $offering->track_name ?></span>
                        </div>
                    </div>
                    <div class="data-item mb-0">
                        <div class="data-icon"><i class="cil-list"></i></div>
                        <div class="data-content">
                            <span class="data-label">Program</span>
                            <span class="data-value"><?= $offering->program_name ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 ps-md-4">
                    <div class="info-group-title">Informasi</div>
                    <div class="data-item">
                        <div class="data-icon"><i class="cil-calendar"></i></div>
                        <div class="data-content">
                            <span class="data-label">Tahun Ajaran</span>
                            <span class="data-value"><?= $offering->academic_year_name ?? '-' ?></span>
                        </div>
                    </div>
                    <div class="data-item">
                        <div class="data-icon"><i class="cil-clock"></i></div>
                        <div class="data-content">
                            <span class="data-label">Periode</span>
                            <span class="data-value"><?= $offering->period_name ?></span>
                        </div>
                    </div>
                    <div class="data-item">
                        <div class="data-icon"><i class="cil-people"></i></div>
                        <div class="data-content">
                            <span class="data-label">Kuota</span>
                            <span class="data-value"><?= $offering->quota ?></span>
                        </div>
                    </div>
                    <div class="data-item mb-0">
                        <div class="data-icon"><i class="cil-dollar"></i></div>
                        <div class="data-content">
                            <span class="data-label">Biaya Pendaftaran</span>
                            <span class="data-value"><span class="badge-fee"><?= 'Rp. ' . number_format((float)($offering->registration_fee ?? 0), 0, ',', '.') ?></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div class="card border-light-subtle rounded-0 shadow-sm">
    <div class="card-body" id="SUBJECT_GRADE_SPECIFIC_REQUIREMENTS"></div>
</div>
<script>
    const offering_id = +'<?=$offering_id?>';
    DS.Subjects = H.StrToObject('<?=$subjects?>');
    const _grid = "SUBJECT_GRADE_SPECIFIC_REQUIREMENTS", _form = _grid + "_FORM";

    new DataGrid(_grid, {
        title: "Nilai Mata Pelajaran",
        url: _BASE_URL + "administrator/specific-subject-grade-requirements/lists",
        fetchOptions: {
            method: "POST",
            body: (() => {
                const form = new FormData();
                form.append("offering_id", offering_id.toString());
                return form;
            })()
        },
        columns: [
            {
                title: "A",
                field: "A",
                formatter: function (row) {
                    return H.BtnIcon("pencil", "Edit", _form + ".onEdit(" + row.id + ")")
                }
            },
            {title: "Mata Pelajaran", field: "subject"},
            {title: "Nilai Minimum", field: "threshold_score", formatter: "float", styles: "text-end"},
        ],
        addAction: () => window[_form].onEdit(0)
    });

    new FormBuilder(_form, {
        ajaxUrl: _BASE_URL + "administrator/specific-subject-grade-requirements",
        title: "Nilai Mata Pelajaran",
        fields: [
            {label: "Mata Pelajaran", name: "subject_id", type: "select", options: DS.Subjects, required: true},
            {label: "Nilai Minimum", name: "threshold_score", type: 'float', required: true}
        ],
        beforeSave: (payload) => {
            payload.admission_offering_id = offering_id;
        },
        grids: [_grid],
    });
</script>
<?= $this->endSection() ?>
