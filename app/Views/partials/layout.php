<!DOCTYPE html>
<html lang="en" data-coreui-theme="light">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description"
          content="Sistem PMB Online modern dari sekolahku.web.id untuk mengelola penerimaan murid baru secara mudah, cepat, dan terintegrasi. Solusi open source tanpa vendor lock-in untuk digitalisasi sekolah.">
    <meta name="author" content="sekolahku.web.id">
    <meta name="keywords"
          content="PMB Online, Admission System, sistem penerimaan murid baru, aplikasi SPMB sekolah, sistem admisi sekolah, digitalisasi sekolah, SIAKAD, open source sekolah, aplikasi akademik Indonesia, sekolahku.web.id">
    <?php
    $favicon = session()->get('favicon') ?? 'favicon.ico';
    $favicon_path = ($favicon === 'favicon.ico') ? base_url('favicon.ico') : base_url('upload/' . $favicon);
    ?>
    <link rel="icon" type="image/x-icon" href="<?= $favicon_path ?>">
    <?= csrf_meta() ?>
    <title>Admission System | <?= session()->get('institution_name') ?? '' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.4/dist/css/datepicker.min.css" rel="stylesheet">
    <?php echo link_tag('css/simplebar.css'); ?>
    <?php echo link_tag('css/style.css'); ?>
    <?php echo link_tag('coreui-icons/css/free.min.css'); ?>
    <?php echo link_tag('css/customize.css'); ?>
    <script type="text/javascript">
        const _BASE_URL = '<?php echo base_url()?>';
        const _CURRENT_URL = '<?php echo current_url()?>';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.4/dist/js/datepicker-full.min.js"></script>
    <?php echo script_tag('js/coreui.bundle.min.js') ?>
    <?php echo script_tag('js/simplebar.min.js') ?>
    <?php echo script_tag('js/datagrid.min.js') ?>
    <?php echo script_tag('js/formbuilder.min.js') ?>
    <?php echo script_tag('js/Dialog.js') ?>
    <?php echo script_tag('js/Alert.js') ?>
    <?php echo script_tag('js/App.js') ?>
    <?php echo script_tag('js/datasource.js') ?>
</head>
<body class="small">
<?= $this->include('partials/sidebar') ?>
<div class="wrapper d-flex flex-column min-vh-100">
    <header class="header header-sticky p-0 mb-4">
        <div class="container-fluid border-bottom px-4">
            <button class="header-toggler" type="button"
                    onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"
                    style="margin-inline-start: -14px;">
                <svg class="icon icon-lg">
                    <use xlink:href="<?= base_url('coreui-icons/svg/free.svg#cil-menu') ?>"></use>
                </svg>
            </button>
        </div>
        <div class="container-fluid px-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb my-0">
                    <li class="breadcrumb-item active fw-bold text-uppercase"><span><?= ($title ?? '') ?></span></li>
                </ol>
            </nav>
        </div>
    </header>
    <div class="body flex-grow-1 px-4">
        <div class="container-fluid px-0 pb-4">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?= $this->renderSection('content') ?>
        </div>
    </div>
    <footer class="footer px-4 bg-white">
        <div class="fw-lighter">&copy; <?php echo date('Y') . ' ' . session()->get('institution_name') ?>. All rights
            reserved.
        </div>
        <div class="ms-auto fw-lighter">
            SPMB Version <?= APP_VERSION ?> - Powered by <a class="text-decoration-none" target="_blank"
                                                            href="https://sekolahku.web.id">sekolahku.web.id</a>
        </div>
    </footer>
</div>
<script>
    const header = document.querySelector('header.header');
    document.addEventListener('scroll', () => {
        if (header) {
            header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
        }
    });
</script>
</body>
</html>
