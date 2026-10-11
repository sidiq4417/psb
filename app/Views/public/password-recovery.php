<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | <?= $institution_name ?></title>
    <link href="<?= base_url('css/tailwind.css') ?>" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <?= csrf_meta() ?>
</head>
<body class="antialiased bg-slate-50">

<div class="flex min-h-screen">

    <!-- Left Column: Branding (Same as Registration) -->
    <div class="hidden lg:flex lg:w-7/12 relative p-16 flex-col justify-between overflow-hidden">
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-indigo-900 via-blue-900 to-emerald-900"></div>
        <div class="absolute inset-0 z-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-6">
                <div class="h-20 w-auto flex items-center justify-center overflow-hidden transform hover:scale-105 transition-transform duration-300">
                    <?php
                        $logo_path = ($logo === 'logo.png') ? base_url('logo.png') : base_url('upload/' . $logo);
                    ?>
                    <img src="<?= $logo_path ?>" alt="Logo Instansi" class="h-full w-auto object-contain">
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white leading-tight"><?= $institution_name ?></h1>
                    <p class="text-emerald-400 text-xs font-bold uppercase tracking-[0.2em] mt-1.5 border-l-2 border-emerald-500 pl-3"><?= $tagline ?></p>
                </div>
            </div>
        </div>

        <div class="relative z-10 max-w-xl">
                <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-6 border border-emerald-500/30">
                    Penerimaan <?= $admission_year ?> Kini Dibuka
                </span>
            <h2 class="text-6xl font-extrabold text-white leading-[1.1] mb-8">
                Pulihkan <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 to-yellow-500">Akses Anda.</span>
            </h2>

            <div class="grid grid-cols-2 gap-6 mt-12">
                <div class="glass-effect p-6 rounded-3xl">
                    <p class="text-3xl font-bold text-white">Multi-Sekolah</p>
                    <p class="text-indigo-200 text-sm mt-1">Satu yayasan, berbagai pilihan sekolah unggulan</p>
                </div>
                <div class="glass-effect p-6 rounded-3xl">
                    <p class="text-3xl font-bold text-white">Transparan</p>
                    <p class="text-indigo-200 text-sm mt-1">Proses seleksi terpusat, hasil terintegrasi</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 flex items-center justify-between text-sm text-indigo-300/70">
            <div>
                <p>&copy; <?= date('Y') ?> <?= $institution_name ?></p>
                <p class="mt-1 text-xs opacity-60">Powered by <a href="https://sekolahku.web.id" target="_blank" class="hover:text-amber-400 font-semibold transition">sekolahku.web.id</a></p>
            </div>
            <div class="flex gap-4">
                <a href="<?= $registration_terms ?? '#' ?>" class="hover:text-amber-400 transition">Download Panduan Lengkap Pendaftaran</a>
            </div>
        </div>
    </div>

    <!-- Right Column: Reset Password Form -->
    <div class="w-full lg:w-5/12 flex items-center justify-center p-8 bg-white overflow-y-auto">
        <div class="w-full max-w-md">

            <div class="mb-10 text-center lg:text-left">
                <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6 mx-auto lg:mx-0">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Atur Ulang Sandi</h2>
                <p class="text-slate-500 mt-3 font-medium">Silakan masukkan kata sandi baru untuk akun Anda.</p>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 ml-1">Kata Sandi Baru</label>
                    <input type="password" id="password" class="w-full mt-2 px-5 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-indigo-600 focus:bg-white outline-none transition-all" placeholder="Minimal 8 karakter">
                    <div id="err-password" class="text-error"></div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 ml-1">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirm" class="w-full mt-2 px-5 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-indigo-600 focus:bg-white outline-none transition-all" placeholder="Ulangi kata sandi baru">
                    <div id="err-password-confirm" class="text-error"></div>
                </div>

                <div id="err-general" class="text-error text-center !block"></div>

                <button onclick="handleReset()" id="btnReset" class="w-full bg-indigo-700 hover:bg-indigo-800 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-200 transition-all transform active:scale-95">
                    Perbarui Kata Sandi
                </button>
            </div>

            <div class="mt-10 text-center">
                <a href="<?= site_url('login') ?>" class="text-sm font-bold text-slate-400 hover:text-indigo-600 transition">
                    &larr; Kembali ke Halaman Login
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    const API_URL = "<?= base_url() ?>";

    function getCookie(name) {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (match) return match[2];
        return null;
    }

    async function fetchApi(url, formData) {
        let token = getCookie('<?= config('Security')->cookieName ?>');
        if (!token) {
            const meta = document.querySelector('meta[name="<?= config('Security')->headerName ?>"]');
            if (meta) token = meta.getAttribute('content');
        }
        const headers = {};
        if (token) {
            headers['<?= config('Security')->headerName ?>'] = token;
            if (formData instanceof FormData) {
                formData.delete('<?= config('Security')->tokenName ?>');
                formData.append('<?= config('Security')->tokenName ?>', token);
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
            const response = await fetchApi(`${API_URL}reset-password/verify/<?= $password_reset_token ?>`, formData);
            const result = await response.json();

            if (result.success) {
                alert("Kata sandi berhasil diperbarui!");
                window.location.href = "<?= site_url('login') ?>";
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
