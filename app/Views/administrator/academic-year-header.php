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

<div class="col-12 px-0">
    <div class="card-admission-header">
        <div class="row g-4">
            <div class="col-md-6 border-end-md pe-md-4">
                <div class="info-group-title">Tahun Ajaran</div>
                <div class="data-item">
                    <div class="data-icon"><i class="cil-calendar"></i></div>
                    <div class="data-content">
                        <span class="data-label">Tahun Ajaran</span>
                        <span class="data-value"><?= $academic_year->name ?? '-' ?></span>
                    </div>
                </div>
                <div class="data-item mb-0">
                    <div class="data-icon"><i class="cil-clock"></i></div>
                    <div class="data-content">
                        <span class="data-label">Periode</span>
                        <span class="data-value">
                            <?= !empty($academic_year->start_date) ? date('d F Y', strtotime($academic_year->start_date)) : '-' ?> -
                            <?= !empty($academic_year->end_date) ? date('d F Y', strtotime($academic_year->end_date)) : '-' ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 ps-md-4">
                <div class="info-group-title">Periode Pendaftaran</div>
                <?php if (!empty($admission_periods)): ?>
                    <?php foreach ($admission_periods as $period): ?>
                        <div class="data-item <?= $period === end($admission_periods) ? 'mb-0' : '' ?>">
                            <div class="data-icon"><i class="cil-calendar-check"></i></div>
                            <div class="data-content">
                                <span class="data-label"><?= esc($period->name ?? '-') ?></span>
                                <span class="data-value">
                                    <?= !empty($period->registration_start_at) ? date('d F Y', strtotime($period->registration_start_at)) : '-' ?> -
                                    <?= !empty($period->registration_end_at) ? date('d F Y', strtotime($period->registration_end_at)) : '-' ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="data-item mb-0">
                        <div class="data-icon"><i class="cil-calendar-check"></i></div>
                        <div class="data-content">
                            <span class="data-value">Tidak ada periode pendaftaran</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
