<!DOCTYPE html>
<html lang="id" data-coreui-theme="light">
<head>
    <base href="./">
    <?php
    $institution_name = trim((string) (session()->get('institution_name') ?? ''));
    $branding = config('Branding');
    $short_name = $branding->shortName !== '' ? $branding->shortName : $institution_name;
    $page_title = trim((string) ($title ?? ''));
    $favicon = basename((string) (session()->get('favicon') ?? 'favicon.ico'));
    $favicon_path = ($favicon === '' || $favicon === 'favicon.ico') ? base_url('favicon.ico') : base_url('upload/' . rawurlencode($favicon));
    ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description"
          content="<?= esc('Sistem Penerimaan Santri Baru (PSB) online' . ($institution_name !== '' ? ' ' . $institution_name : '') . '.', 'attr') ?>">
    <meta name="author" content="<?= esc($institution_name, 'attr') ?>">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#2e6b12">
    <link rel="icon" href="<?= esc($favicon_path, 'attr') ?>">
    <link rel="apple-touch-icon" href="<?= esc(base_url('apple-touch-icon.png'), 'attr') ?>">
    <?= csrf_meta() ?>
    <title><?= esc(($page_title !== '' ? $page_title . ' | ' : '') . $branding->productName . ($short_name !== '' ? ' ' . $short_name : '')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet" integrity="sha384-oNaL7h8UJDHZ8k9XxysQRnqKxysB2lmlq7uU5Ic9CsOy89U4US8mT9pGYFf0ygyM" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.4/dist/css/datepicker.min.css" rel="stylesheet" integrity="sha384-gTirI3T7NWCAZfQn09CDB5g1sn63KQN+uRpnxZ7NA9AK7WDDR/o5b6xep6IWUIad" crossorigin="anonymous">
    <?php echo link_tag('css/simplebar.css'); ?>
    <?php echo link_tag('css/style.css'); ?>
    <?php echo link_tag('coreui-icons/css/free.min.css'); ?>
    <?php echo link_tag('css/customize.css'); ?>
    <script type="text/javascript">
        const _BASE_URL = '<?= esc(base_url(), 'js') ?>';
        const _CURRENT_URL = '<?= esc(current_url(), 'js') ?>';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js" integrity="sha384-DZEVpSJ4pM9MDuXkgydQD2RzczjPKUOeRgRpYNZ5fyMlf14Pn/3dZbgIpdtH2YRb" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.4/dist/js/datepicker-full.min.js" integrity="sha384-ITSSOnrAObX/N+U1xc39woJMMEogg2xffq4U9Ti7GQ6QREaQmf+qB9H+KJluM2JI" crossorigin="anonymous"></script>
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
                    <li class="breadcrumb-item active fw-bold text-uppercase"><span><?= esc($page_title) ?></span></li>
                </ol>
            </nav>
        </div>
    </header>
    <div class="body flex-grow-1 px-4">
        <div class="container-fluid px-0 pb-4">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= esc((string) session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc((string) session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?= $this->renderSection('content') ?>
        </div>
    </div>
    <footer class="footer px-4 bg-white">
        <div class="fw-lighter">&copy; <?= date('Y') ?> <?= esc($institution_name) ?>. Hak cipta dilindungi.
        </div>
        <div class="ms-auto fw-lighter small text-body-secondary">
            <?= esc($branding->productName) ?> &middot; SPMB <?= esc(APP_VERSION) ?> - Powered by <a class="text-decoration-none" target="_blank" rel="noopener"
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
