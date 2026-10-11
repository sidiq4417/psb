<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="<?= esc($institution_name . ' - ' . ($tagline ?: 'Penerimaan Santri Baru (PSB) Online') . '. Buat akun, lengkapi data, dan pantau proses seleksi secara daring.', 'attr') ?>">
    <meta name="author" content="<?= esc($institution_name, 'attr') ?>">
    <meta name="keywords"
        content="<?= esc('penerimaan santri baru, PSB online, pendaftaran santri baru, pondok pesantren, ' . $institution_name, 'attr') ?>">
    <?php
    $favicon_path = ($favicon === 'favicon.ico') ? base_url('favicon.ico') : base_url('upload/' . rawurlencode($favicon));
    $logo_path = ($logo === 'logo.png') ? base_url('logo.png') : base_url('upload/' . rawurlencode($logo));
    ?>
    <meta name="theme-color" content="#2e6b12">
    <link rel="icon" href="<?= esc($favicon_path, 'attr') ?>">
    <link rel="apple-touch-icon" href="<?= esc(base_url('apple-touch-icon.png'), 'attr') ?>">
    <title><?= esc(config('Branding')->productName) ?> | <?= esc($institution_name) ?></title>
    <link href="<?= base_url('css/tailwind.css') ?>" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js" integrity="sha384-9Ax3MmS9AClxJyd5/zafcXXjxmwFhZCdsT6HJoJjarvCaAkJlk5QDzjLJm+Wdx5F" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <?= csrf_meta() ?>
    <?php if (!empty($turnstile_site_key)): ?>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <?php endif; ?>
</head>

<body class="antialiased bg-slate-50" x-data="{ page: 'login' }">

    <div class="flex min-h-screen">

        <div class="hidden lg:flex lg:w-7/12 relative p-16 flex-col justify-between overflow-hidden">
            <div class="absolute inset-0 z-0 bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800"></div>
            <div
                class="absolute inset-0 z-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]">
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-6">
                    <div
                        class="h-20 w-auto flex items-center justify-center overflow-hidden transform hover:scale-105 transition-transform duration-300">
                        <img src="<?= esc($logo_path, 'attr') ?>" alt="<?= esc('Logo ' . $institution_name, 'attr') ?>" class="h-full w-auto object-contain">
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-white leading-tight">
                            <?= esc($institution_name) ?>
                        </h1>
                        <p
                            class="text-accent-300 text-xs font-bold uppercase tracking-[0.2em] mt-1.5 border-l-2 border-accent-300 pl-3">
                            <?= esc($tagline) ?></p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 max-w-xl">
                <span
                    class="inline-block px-4 py-1.5 rounded-full bg-accent-300/10 text-accent-200 text-xs font-bold uppercase tracking-wider mb-6 border border-accent-300/40">
                    Penerimaan <?= esc($admission_year) ?> Kini Dibuka
                </span>
                <h2 class="text-6xl font-extrabold text-white leading-[1.1] mb-8">
                    Wujudkan <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-accent-200 to-brand-300">Generasi
                            Qur'ani</span> untuk Masa Depan Putra-Putri Anda.
                </h2>

                <?php if (!empty($branches)): ?>
                <div class="mt-12">
                    <div class="grid grid-cols-2 gap-4">
                        <?php foreach ($branches as $branch): ?>
                        <div class="glass-effect p-5 rounded-2xl">
                            <p class="text-lg font-bold text-white"><?= esc($branch->name) ?></p>
                            <p class="text-brand-100 text-xs mt-1"><?= esc(implode(' - ', array_filter([(string) ($branch->education_level ?? ''), (string) ($branch->address ?? '')], 'strlen'))) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-12 flex flex-wrap gap-6 items-center text-brand-100/80">
                    <?php if ($email): ?>
                        <a href="mailto:<?= esc($email, 'attr') ?>" class="flex items-center gap-2 hover:text-white transition group">
                            <div class="p-2 rounded-xl bg-brand-400/20 group-hover:bg-brand-400/40 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold tracking-wider"><?= esc($email) ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($telephone): ?>
                        <div class="flex items-center gap-2 group">
                            <div class="p-2 rounded-xl bg-brand-400/20 group-hover:bg-brand-400/40 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-wider"><?= esc($telephone) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($mobile_phone): ?>
                        <a href="https://wa.me/<?= esc(preg_replace('/[^0-9]/', '', $mobile_phone), 'attr') ?>" target="_blank" rel="noopener"
                            class="flex items-center gap-2 hover:text-accent-300 transition group">
                            <div class="p-2 rounded-xl bg-brand-400/20 group-hover:bg-brand-400/40 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-wider"><?= esc($mobile_phone) ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="relative z-10 flex items-center justify-between text-sm text-brand-200/70">
                <div>
                    <p>&copy; <?= date('Y') ?> <?= esc($institution_name) ?></p>
                    <p class="mt-1 text-xs opacity-60">Powered by <a href="https://sekolahku.web.id" target="_blank" rel="noopener"
                            class="hover:text-accent-300 font-semibold transition">sekolahku.web.id</a>
                    </p>
                </div>
                <?php if ($registration_terms): ?>
                    <div class="flex gap-4">
                        <a href="<?= esc($registration_terms, 'attr') ?>" class="hover:text-accent-300 transition">Download Panduan
                            Lengkap Pendaftaran</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="w-full lg:w-5/12 flex items-center justify-center p-8 bg-white overflow-y-auto">
            <div class="w-full max-w-md">

                <div class="lg:hidden flex items-center gap-4 mb-10 pb-6 border-b-2 border-accent-300">
                    <img src="<?= esc($logo_path, 'attr') ?>" alt="<?= esc('Logo ' . $institution_name, 'attr') ?>" class="h-16 w-16 object-contain flex-shrink-0">
                    <div>
                        <p class="text-lg font-extrabold leading-tight text-brand-900"><?= esc($institution_name) ?></p>
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-700 mt-1"><?= esc($tagline) ?></p>
                    </div>
                </div>

                <div x-show="page === 'login'" x-transition:enter="transition duration-500"
                    x-transition:enter-start="opacity-0 translate-x-8">
                    <div class="mb-10 text-center lg:text-left">
                        <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Portal Masuk</h2>
                        <p class="text-slate-500 mt-3 font-medium">Selamat datang kembali. Silakan masuk untuk
                            melanjutkan aktivitas Anda.
                        </p>
                    </div>

                    <?php $flash_success = session()->getFlashdata('success'); ?>
                    <?php if (is_string($flash_success) && $flash_success !== ''): ?>
                        <div class="mb-6 px-5 py-4 rounded-2xl bg-brand-50 border border-brand-200 text-brand-800 text-sm font-semibold" role="status">
                            <?= esc($flash_success) ?>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 ml-1">Email</label>
                            <input type="email" id="loginEmail"
                                class="w-full mt-2 px-5 py-4 bg-slate-50 border-2 border-slate-200/60 rounded-2xl focus:border-brand-600 focus:bg-white outline-none transition-all">
                            <div id="err-login-email" class="text-error"></div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center ml-1">
                                <label class="block text-sm font-bold text-slate-700">Kata Sandi</label>
                                <button @click.prevent="page = 'forgot'"
                                    class="text-xs font-bold text-brand-700 hover:text-brand-900 uppercase tracking-wider">
                                    Lupa?
                                </button>
                            </div>
                            <input type="password" id="loginPassword"
                                class="w-full mt-2 px-5 py-4 bg-slate-50 border-2 border-slate-200/60 rounded-2xl focus:border-brand-600 focus:bg-white outline-none transition-all">
                            <div id="err-login-password" class="text-error"></div>
                        </div>

                        <div class="flex items-center gap-3 p-2">
                            <input type="checkbox" id="rememberMe"
                                class="w-5 h-5 accent-brand-600 border-slate-200 rounded-lg">
                            <label for="rememberMe" class="text-xs text-slate-500 font-medium">Ingat Saya di Perangkat
                                Ini</label>
                        </div>

                        <?php if (!empty($turnstile_site_key)): ?>
                            <div class="flex justify-center mt-2 mb-2">
                                <div class="cf-turnstile" data-sitekey="<?= esc($turnstile_site_key, 'attr') ?>" data-theme="light">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div id="err-login-general" class="text-error text-center !block"></div>
                        <button onclick="handleLogin()"
                            class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-4 rounded-2xl shadow-xl shadow-brand-100 transition-all transform active:scale-95">
                            Masuk ke Portal
                        </button>
                    </div>

                    <div class="mt-10 flex flex-col items-center gap-4">
                        <p class="text-slate-500 text-sm font-medium">Belum memiliki akun pendaftaran?</p>
                        <button @click="page = 'register'"
                            class="w-full py-4 border-2 border-slate-100 hover:border-brand-200 hover:bg-brand-50 text-brand-700 font-bold rounded-2xl transition-all">
                            Buat Akun Sekarang
                        </button>
                    </div>
                </div>

                <div x-show="page === 'register'" x-transition:enter="transition duration-500"
                    x-transition:enter-start="opacity-0 translate-x-8" style="display: none;">
                    <div class="mb-8">
                        <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight text-center lg:text-left">
                            Daftar Akun</h2>
                        <p class="text-slate-500 mt-3 font-medium text-center lg:text-left">Mulai langkah pertama Anda
                            menuju kesuksesan.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-bold text-slate-700 ml-1">Nama Lengkap</label>
                            <input type="text" id="regFullName"
                                class="w-full mt-1 px-5 py-4 bg-slate-50 border-2 border-slate-200/60 rounded-2xl focus:border-brand-500 focus:bg-white outline-none transition-all">
                            <div id="err-reg-name" class="text-error"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-bold text-slate-700 ml-1">Email</label>
                                <input type="email" id="regEmail"
                                    class="w-full mt-1 px-5 py-4 bg-slate-50 border-2 border-slate-200/60 rounded-2xl focus:border-brand-500 focus:bg-white outline-none transition-all">
                                <div id="err-reg-email" class="text-error"></div>
                            </div>
                            <div>
                                <label class="text-sm font-bold text-slate-700 ml-1">WhatsApp</label>
                                <input type="tel" id="regWhatsapp"
                                    class="w-full mt-1 px-5 py-4 bg-slate-50 border-2 border-slate-200/60 rounded-2xl focus:border-brand-500 focus:bg-white outline-none transition-all">
                                <div id="err-reg-wa" class="text-error"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-bold text-slate-700 ml-1">Sandi</label>
                                <input type="password" id="regPassword"
                                    class="w-full mt-1 px-5 py-4 bg-slate-50 border-2 border-slate-200/60 rounded-2xl focus:border-brand-500 focus:bg-white outline-none transition-all">
                                <div id="err-reg-pass" class="text-error"></div>
                            </div>
                            <div>
                                <label class="text-sm font-bold text-slate-700 ml-1">Konfirmasi</label>
                                <input type="password" id="regConfirm"
                                    class="w-full mt-1 px-5 py-4 bg-slate-50 border-2 border-slate-200/60 rounded-2xl focus:border-brand-500 focus:bg-white outline-none transition-all">
                                <div id="err-reg-confirm" class="text-error"></div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-2">
                            <input type="checkbox" id="regAgree"
                                class="w-5 h-5 accent-brand-600 border-slate-200 rounded-lg">
                            <label for="regAgree" class="text-xs text-slate-500 font-medium cursor-pointer">Saya setuju
                                dengan kebijakan privasi & syarat pendaftaran.</label>
                        </div>
                        <div id="err-reg-agree" class="text-error"></div>

                        <?php if (!empty($turnstile_site_key)): ?>
                            <div class="flex justify-center mt-2 mb-2">
                                <div class="cf-turnstile" data-sitekey="<?= esc($turnstile_site_key, 'attr') ?>" data-theme="light">
                                </div>
                            </div>
                        <?php endif; ?>

                        <button onclick="handleRegister()"
                            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-brand-100 transition-all active:scale-95">
                            Konfirmasi Pendaftaran
                        </button>
                    </div>

                    <button @click="page = 'login'"
                        class="w-full mt-6 text-center text-sm font-bold text-slate-500 hover:text-brand-700 transition">
                        &larr; Sudah ada akun? Masuk
                    </button>
                </div>

                <div x-show="page === 'forgot'" x-transition:enter="transition duration-500"
                    x-transition:enter-start="opacity-0 translate-x-8" style="display: none;">
                    <div class="mb-10">
                        <div
                            class="w-16 h-16 bg-accent-100 rounded-2xl flex items-center justify-center mb-6 mx-auto lg:mx-0">
                            <svg class="w-8 h-8 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                </path>
                            </svg>
                        </div>
                        <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight text-center lg:text-left">
                            Pulihkan Sandi</h2>
                        <p class="text-slate-500 mt-3 font-medium text-center lg:text-left">Instruksi pemulihan akan
                            dikirim ke email terdaftar.</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="text-sm font-bold text-slate-700 ml-1">Email Terdaftar</label>
                            <input type="email" id="forgotEmail"
                                class="w-full mt-2 px-5 py-4 bg-slate-50 border-2 border-slate-200/60 rounded-2xl focus:border-brand-600 focus:bg-white outline-none transition-all">
                            <div id="err-forgot-email" class="text-error"></div>
                        </div>

                        <?php if (!empty($turnstile_site_key)): ?>
                            <div class="flex justify-center mt-2 mb-2">
                                <div class="cf-turnstile" data-sitekey="<?= esc($turnstile_site_key, 'attr') ?>" data-theme="light">
                                </div>
                            </div>
                        <?php endif; ?>

                        <button onclick="handleForgot()"
                            class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-4 rounded-2xl shadow-xl shadow-brand-100 transition-all">
                            Kirim Instruksi Reset
                        </button>
                    </div>

                    <button @click="page = 'login'"
                        class="w-full mt-10 text-center text-sm font-bold text-slate-500 hover:text-brand-700 transition">
                        Ingat sandi Anda? Kembali ke Login
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        // --- KONFIGURASI ---
        const API_URL = "<?= esc(base_url(), 'js') ?>";

        // --- HELPER UI ---
        async function fetchApi(url, formData) {
            // The token rendered by csrf_meta(); the CSRF cookie itself is HttpOnly and,
            // with token randomization, is not the value the server expects.
            const meta = document.querySelector('meta[name="<?= esc(config('Security')->headerName, 'js') ?>"]');
            const token = meta ? meta.getAttribute('content') : null;
            const headers = {};
            if (token) {
                headers['<?= esc(config('Security')->headerName, 'js') ?>'] = token;
                if (formData instanceof FormData) {
                    // Remove if already exists to prevent duplicate append arrays
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

        function toggleLoading(button, isLoading) {
            if (isLoading) {
                button.dataset.originalText = button.innerText;
                button.innerText = "Memproses...";
                button.disabled = true;
            } else {
                button.innerText = button.dataset.originalText || "Lanjutkan";
                button.disabled = false;
            }
        }

        function showError(id, msg) {
            const el = document.getElementById(id);
            if (el) {
                el.innerText = msg;
                el.style.display = 'block';
            }
        }

        function clearErrors() {
            document.querySelectorAll('.text-error').forEach(el => {
                el.innerText = '';
                el.style.display = 'none';
            });
        }

        function validateEmail(email) {
            return String(email).toLowerCase().match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/);
        }

        // --- AJAX HANDLERS ---

        async function handleLogin() {
            clearErrors();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const rememberMe = document.getElementById('rememberMe').checked;
            const btn = document.querySelector('[x-show="page === \'login\'"] .bg-brand-700');

            let hasError = false;
            if (!validateEmail(email)) {
                showError('err-login-email', 'Format email tidak valid.');
                hasError = true;
            }
            if (!password) {
                showError('err-login-password', 'Kata sandi wajib diisi.');
                hasError = true;
            }
            if (hasError) return;

            toggleLoading(btn, true);

            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            formData.append('remember_me', rememberMe ? '1' : '0');

            const turnstileContainer = document.querySelector('[x-show="page === \'login\'"] .cf-turnstile');
            if (turnstileContainer) {
                const turnstileInput = turnstileContainer.querySelector('[name="cf-turnstile-response"]');
                if (turnstileInput) formData.append('cf-turnstile-response', turnstileInput.value);
            }

            try {
                const response = await fetchApi(`${API_URL}login`, formData);
                const result = await response.json();

                if (response.ok && result.success) {
                    window.location.href = API_URL + 'dashboard';
                } else {
                    showError('err-login-general', result.message || (result.errors ? Object.values(result.errors).join("\n") : "Email atau kata sandi salah."));
                    if (typeof turnstile !== 'undefined') {
                        turnstile.reset();
                    }
                }
            } catch (error) {
                showError('err-login-general', "Tidak dapat terhubung ke server.");
                if (typeof turnstile !== 'undefined') {
                    turnstile.reset();
                }
            } finally {
                toggleLoading(btn, false);
            }
        }

        async function handleRegister() {
            clearErrors();
            const name = document.getElementById('regFullName').value;
            const email = document.getElementById('regEmail').value;
            const wa = document.getElementById('regWhatsapp').value;
            const pass = document.getElementById('regPassword').value;
            const confirm = document.getElementById('regConfirm').value;
            const agree = document.getElementById('regAgree').checked;
            const btn = document.querySelector('[x-show="page === \'register\'"] .bg-brand-600');

            let hasError = false;
            if (!name.trim()) {
                showError('err-reg-name', 'Nama lengkap wajib diisi.');
                hasError = true;
            }
            if (!validateEmail(email)) {
                showError('err-reg-email', 'Format email tidak valid.');
                hasError = true;
            }
            if (wa.length < 10) {
                showError('err-reg-wa', 'Nomor WhatsApp minimal 10 digit.');
                hasError = true;
            }
            if (pass.length < 8) {
                showError('err-reg-pass', 'Minimal 8 karakter.');
                hasError = true;
            }
            if (pass !== confirm) {
                showError('err-reg-confirm', 'Sandi tidak cocok.');
                hasError = true;
            }
            if (!agree) {
                showError('err-reg-agree', 'Setujui syarat & ketentuan.');
                hasError = true;
            }

            if (hasError) return;

            toggleLoading(btn, true);

            const formData = new FormData();
            formData.append('full_name', name);
            formData.append('email', email);
            formData.append('mobile_phone', wa);
            formData.append('password', pass);

            const turnstileContainer = document.querySelector('[x-show="page === \'register\'"] .cf-turnstile');
            if (turnstileContainer) {
                const turnstileInput = turnstileContainer.querySelector('[name="cf-turnstile-response"]');
                if (turnstileInput) formData.append('cf-turnstile-response', turnstileInput.value);
            }

            try {
                const response = await fetchApi(`${API_URL}register`, formData);
                const result = await response.json();

                if (response.ok && result.success) {
                    // result.email_sent === false means the account exists but the verification email failed.
                    alert(result.message || "Pendaftaran berhasil! Silakan cek email Anda.");
                    // Safely transition back to login page
                    window.location.href = API_URL;
                } else {
                    if (result.errors) {
                        const fieldErrors = {
                            full_name: 'err-reg-name',
                            email: 'err-reg-email',
                            mobile_phone: 'err-reg-wa',
                            password: 'err-reg-pass'
                        };
                        Object.entries(result.errors).forEach(([field, message]) => {
                            showError(fieldErrors[field] || 'err-reg-agree', message);
                        });
                    } else {
                        alert(result.message || "Gagal mendaftar.");
                    }
                    if (typeof turnstile !== 'undefined') {
                        turnstile.reset();
                    }
                }
            } catch (error) {
                console.error("Registration Error:", error);
                alert("Tidak dapat memproses pendaftaran. Silakan muat ulang halaman lalu coba masuk dengan email Anda; jika belum terdaftar, ulangi pendaftaran.");
                window.location.reload();
            } finally {
                toggleLoading(btn, false);
            }
        }

        async function handleForgot() {
            clearErrors();
            const email = document.getElementById('forgotEmail').value;
            const btn = document.querySelector('[x-show="page === \'forgot\'"] .bg-brand-700');

            if (!validateEmail(email)) return showError('err-forgot-email', 'Masukkan email valid.');

            toggleLoading(btn, true);
            const formData = new FormData();
            formData.append('email', email);

            const turnstileContainer = document.querySelector('[x-show="page === \'forgot\'"] .cf-turnstile');
            if (turnstileContainer) {
                const turnstileInput = turnstileContainer.querySelector('[name="cf-turnstile-response"]');
                if (turnstileInput) formData.append('cf-turnstile-response', turnstileInput.value);
            }

            try {
                const response = await fetchApi(`${API_URL}forgot-password`, formData);
                const result = await response.json();

                if (response.ok && result.success) {
                    alert(result.message || "Instruksi reset terkirim!");
                    window.location.href = API_URL;
                } else {
                    showError('err-forgot-email', result.message || (result.errors ? Object.values(result.errors).join("\n") : "Permintaan gagal diproses. Silakan coba lagi."));
                    if (typeof turnstile !== 'undefined') {
                        turnstile.reset();
                    }
                }
            } catch (e) {
                alert("Gagal menghubungi server.");
                if (typeof turnstile !== 'undefined') {
                    turnstile.reset();
                }
            } finally {
                toggleLoading(btn, false);
            }
        }
    </script>
</body>

</html>
