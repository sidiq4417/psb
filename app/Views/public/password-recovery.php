<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $favicon = $favicon ?? 'favicon.ico';
    $favicon_path = ($favicon === 'favicon.ico') ? base_url('favicon.ico') : base_url('upload/' . rawurlencode($favicon));
    ?>
    <meta name="theme-color" content="#2e6b12">
    <link rel="icon" href="<?= esc($favicon_path, 'attr') ?>">
    <link rel="apple-touch-icon" href="<?= esc(base_url('apple-touch-icon.png'), 'attr') ?>">
    <title><?= esc($title) ?> | <?= esc(config('Branding')->productName) ?> | <?= esc($institution_name) ?></title>
    <link href="<?= base_url('css/tailwind.css') ?>" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js" integrity="sha384-9Ax3MmS9AClxJyd5/zafcXXjxmwFhZCdsT6HJoJjarvCaAkJlk5QDzjLJm+Wdx5F" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <?= csrf_meta() ?>
</head>
<body class="antialiased bg-slate-50">

<div class="flex min-h-screen">

    <!-- Left Column: Branding (Same as Registration) -->
    <div class="hidden lg:flex lg:w-7/12 relative p-16 flex-col justify-between overflow-hidden">
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-6">
                <div class="h-20 w-auto flex items-center justify-center overflow-hidden transform hover:scale-105 transition-transform duration-300">
                    <?php
                        $logo_path = ($logo === 'logo.png') ? base_url('logo.png') : base_url('upload/' . rawurlencode($logo));
                    ?>
                    <img src="<?= esc($logo_path, 'attr') ?>" alt="<?= esc('Logo ' . $institution_name, 'attr') ?>" class="h-full w-auto object-contain">
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white leading-tight"><?= esc($institution_name) ?></h1>
                    <p class="text-accent-300 text-xs font-bold uppercase tracking-[0.2em] mt-1.5 border-l-2 border-accent-300 pl-3"><?= esc($tagline) ?></p>
                </div>
            </div>
        </div>

        <div class="relative z-10 max-w-xl">
                <span class="inline-block px-4 py-1.5 rounded-full bg-accent-300/10 text-accent-200 text-xs font-bold uppercase tracking-wider mb-6 border border-accent-300/40">
                    Penerimaan <?= esc($admission_year) ?> Kini Dibuka
                </span>
            <h2 class="text-6xl font-extrabold text-white leading-[1.1] mb-8">
                Pulihkan <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent-200 to-brand-300">Akses Anda.</span>
            </h2>

            <div class="grid grid-cols-2 gap-6 mt-12">
                <div class="glass-effect p-6 rounded-3xl">
                    <p class="text-3xl font-bold text-white">Mudah</p>
                    <p class="text-brand-100 text-sm mt-1">Daftar dan pantau seleksi santri baru dari mana saja</p>
                </div>
                <div class="glass-effect p-6 rounded-3xl">
                    <p class="text-3xl font-bold text-white">Transparan</p>
                    <p class="text-brand-100 text-sm mt-1">Proses seleksi terpusat, hasil terintegrasi</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 flex items-center justify-between text-sm text-brand-200/70">
            <div>
                <p>&copy; <?= date('Y') ?> <?= esc($institution_name) ?></p>
                <p class="mt-1 text-xs opacity-60">Powered by <a href="https://sekolahku.web.id" target="_blank" rel="noopener" class="hover:text-accent-300 font-semibold transition">sekolahku.web.id</a></p>
            </div>
            <?php if (!empty($registration_terms)): ?>
                <div class="flex gap-4">
                    <a href="<?= esc($registration_terms, 'attr') ?>" class="hover:text-accent-300 transition">Download Panduan Lengkap Pendaftaran</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Reset Password Form -->
    <div class="w-full lg:w-5/12 flex items-center justify-center p-8 bg-white overflow-y-auto">
        <div class="w-full max-w-md">

            <div class="lg:hidden flex items-center gap-4 mb-10 pb-6 border-b-2 border-accent-300">
                <img src="<?= esc($logo_path, 'attr') ?>" alt="<?= esc('Logo ' . $institution_name, 'attr') ?>" class="h-16 w-16 object-contain flex-shrink-0">
                <div>
                    <p class="text-lg font-extrabold leading-tight text-brand-900"><?= esc($institution_name) ?></p>
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-700 mt-1"><?= esc($tagline) ?></p>
                </div>
            </div>

            <div class="mb-10 text-center lg:text-left">
                <div class="w-16 h-16 bg-accent-100 rounded-2xl flex items-center justify-center mb-6 mx-auto lg:mx-0">
                    <svg class="w-8 h-8 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Atur Ulang Sandi</h2>
                <p class="text-slate-500 mt-3 font-medium">Silakan masukkan kata sandi baru untuk akun Anda.</p>
                <p class="text-slate-400 mt-2 text-sm">Minimal 8 karakter dan mengandung huruf besar, huruf kecil, angka, serta simbol. Tautan ini berlaku 60 menit.</p>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 ml-1">Kata Sandi Baru</label>
                    <input type="password" id="password" class="w-full mt-2 px-5 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-brand-600 focus:bg-white outline-none transition-all" placeholder="Kata sandi baru">
                    <div id="err-password" class="text-error"></div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 ml-1">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirm" class="w-full mt-2 px-5 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-brand-600 focus:bg-white outline-none transition-all" placeholder="Ulangi kata sandi baru">
                    <div id="err-password-confirm" class="text-error"></div>
                </div>

                <div id="err-general" class="text-error text-center !block"></div>

                <button onclick="handleReset()" id="btnReset" class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-4 rounded-2xl shadow-xl shadow-brand-100 transition-all transform active:scale-95">
                    Perbarui Kata Sandi
                </button>
            </div>

            <div class="mt-10 text-center">
                <a href="<?= esc(base_url(), 'attr') ?>" class="text-sm font-bold text-slate-500 hover:text-brand-700 transition">
                    &larr; Kembali ke Halaman Login
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    const API_URL = "<?= esc(base_url(), 'js') ?>";
    const RESET_TOKEN = "<?= esc($password_reset_token, 'js') ?>";

    async function fetchApi(url, formData) {
        // The token rendered by csrf_meta(); the CSRF cookie itself is HttpOnly and,
        // with token randomization, is not the value the server expects.
        const meta = document.querySelector('meta[name="<?= esc(config('Security')->headerName, 'js') ?>"]');
        const token = meta ? meta.getAttribute('content') : null;
        const headers = {};
        if (token) {
            headers['<?= esc(config('Security')->headerName, 'js') ?>'] = token;
            if (formData instanceof FormData) {
                formData.delete('<?= esc(config('Security')->tokenName, 'js') ?>');
                formData.append('<?= esc(config('Security')->tokenName, 'js') ?>', token);
            }
        }
        return await fetch(url, {
            method: 'POST',
            body: formData,
            headers: headers,
            credentials: 'same-origin'
        });
    }

    function showError(id, msg) {
        const el = document.getElementById(id);
        if (el) { el.innerText = msg; el.style.display = 'block'; }
    }

    function clearErrors() {
        document.querySelectorAll('.text-error').forEach(el => {
            el.innerText = ''; el.style.display = 'none';
        });
    }

    function toggleLoading(button, isLoading) {
        if (isLoading) {
            button.dataset.originalText = button.innerText;
            button.innerText = "Memproses...";
            button.disabled = true;
        } else {
            button.innerText = button.dataset.originalText || "Perbarui Kata Sandi";
            button.disabled = false;
        }
    }

    async function handleReset() {
        clearErrors();
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirm').value;
        const btn = document.getElementById('btnReset');

        let hasError = false;
        if (password.length < 8) { showError('err-password', 'Kata sandi minimal 8 karakter.'); hasError = true; }
        if (password !== confirm) { showError('err-password-confirm', 'Konfirmasi kata sandi tidak cocok.'); hasError = true; }
        if (hasError) return;

        toggleLoading(btn, true);

        const formData = new FormData();
        formData.append('password', password);
        formData.append('password_confirm', confirm);

        try {
            const response = await fetchApi(`${API_URL}reset-password/verify/${encodeURIComponent(RESET_TOKEN)}`, formData);
            const result = await response.json();

            if (response.ok && result.success) {
                alert(result.message || "Kata sandi berhasil diperbarui!");
                window.location.href = API_URL;
            } else {
                if (result.errors) {
                    if (result.errors.password) showError('err-password', result.errors.password);
                    if (result.errors.password_confirm) showError('err-password-confirm', result.errors.password_confirm);
                } else {
                    showError('err-general', result.message || "Gagal memperbarui kata sandi.");
                }
            }
        } catch (error) {
            showError('err-general', "Terjadi kesalahan jaringan.");
        } finally {
            toggleLoading(btn, false);
        }
    }
</script>
</body>
</html>
