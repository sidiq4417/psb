<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Penerimaan Santri Baru - PPTQ Al Fath Solo Baru</title>
    <meta content="Penerimaan Santri Baru Pondok Pesantren Tahfidzul Qur'an Al Fath Solo Baru" name="description">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Poppins"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#013220',
                            primary: '#005E2F',
                            secondary: '#008040',
                            light: '#E6F2ED',
                            pale: '#F4F9F6',
                        },
                        accent: {
                            main: '#FBAF1A',
                            hover: '#E59F15',
                            light: '#FDF1D9'
                        }
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #005E2F; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #013220; }
        
        .hero-pattern {
            background-color: #005E2F;
            background-image: radial-gradient(#008040 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-700 antialiased overflow-x-hidden selection:bg-accent-main selection:text-brand-dark flex flex-col min-h-screen">

    <nav class="fixed w-full z-50 bg-white/95 backdrop-blur-sm shadow-sm border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <div class="bg-brand-primary p-2.5 rounded-xl flex items-center justify-center shadow-inner">
                        <svg class="h-6 w-6 text-accent-main" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-lg md:text-xl leading-tight text-brand-dark tracking-tight">PPTQ Al Fath</span>
                        <span class="text-[11px] md:text-xs text-brand-secondary font-medium tracking-wide uppercase">Solo Baru</span>
                    </div>
                </div>

                <div class="hidden md:block">
                    <div class="ml-10 flex items-center space-x-8">
                        <a href="#informasi" class="text-gray-600 hover:text-brand-primary font-medium text-sm transition-colors">Informasi</a>
                        <a href="#alur" class="text-gray-600 hover:text-brand-primary font-medium text-sm transition-colors">Alur & Kuota</a>
                        <a href="#syarat" class="text-gray-600 hover:text-brand-primary font-medium text-sm transition-colors">Syarat & Biaya</a>
                        <a href="#kontak" class="text-gray-600 hover:text-brand-primary font-medium text-sm transition-colors">Kontak</a>
                        <a href="https://siakad.alfathsolobaru.com/psb" class="border-2 border-brand-primary text-brand-primary hover:bg-brand-pale px-5 py-2 rounded-full text-sm font-semibold transition-all">Masuk</a>
                        <a href="https://siakad.alfathsolobaru.com/psb/register" class="bg-brand-primary hover:bg-brand-dark text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow-md transition-all transform hover:-translate-y-0.5">Daftar Sekarang</a>
                    </div>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-brand-primary focus:outline-none p-2" aria-label="Toggle Menu">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path id="menu-icon-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path id="menu-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu-panel" class="hidden md:hidden bg-white border-t border-gray-100 shadow-lg absolute w-full left-0">
            <div class="px-4 pt-2 pb-6 space-y-2 flex flex-col">
                <a href="#informasi" class="mobile-link text-gray-700 hover:bg-brand-pale hover:text-brand-primary block px-4 py-3 rounded-lg font-medium text-base transition-colors">Informasi Pondok</a>
                <a href="#alur" class="mobile-link text-gray-700 hover:bg-brand-pale hover:text-brand-primary block px-4 py-3 rounded-lg font-medium text-base transition-colors">Alur & Kuota</a>
                <a href="#syarat" class="mobile-link text-gray-700 hover:bg-brand-pale hover:text-brand-primary block px-4 py-3 rounded-lg font-medium text-base transition-colors">Syarat & Ketentuan</a>
                <a href="#biaya" class="mobile-link text-gray-700 hover:bg-brand-pale hover:text-brand-primary block px-4 py-3 rounded-lg font-medium text-base transition-colors">Rincian Biaya</a>
                <a href="#kontak" class="mobile-link text-gray-700 hover:bg-brand-pale hover:text-brand-primary block px-4 py-3 rounded-lg font-medium text-base transition-colors">Hubungi Admin</a>
                <a href="https://siakad.alfathsolobaru.com/psb/register" class="bg-brand-primary text-white block px-4 py-3 rounded-lg font-semibold text-center mt-4 shadow-sm">Daftar Sekarang</a>
                <a href="https://siakad.alfathsolobaru.com/psb" class="border-2 border-brand-primary text-brand-primary block px-4 py-3 rounded-lg font-semibold text-center">Masuk</a>
            </div>
        </div>
    </nav>

    <header class="relative pt-32 pb-16 lg:pt-44 lg:pb-24 px-4 hero-pattern text-white overflow-hidden flex-grow-0">
        <div class="absolute inset-0 bg-gradient-to-b from-brand-dark/40 to-brand-dark opacity-95"></div>
        
        <div class="relative max-w-4xl mx-auto text-center z-10">
            <!-- Banner Pendaftaran Dibuka -->
            <div class="bg-emerald-500/15 backdrop-blur-md border border-emerald-400/40 text-left p-4 sm:p-6 rounded-2xl shadow-2xl mb-10 max-w-3xl mx-auto relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-400"></div>
                <div class="flex items-start gap-4">
                    <div class="bg-emerald-400/20 p-2.5 rounded-full flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-base sm:text-lg text-white tracking-wide mb-1 flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400"></span></span>
                            Pendaftaran Sudah Dibuka!
                        </p>
                        <p class="text-sm sm:text-base text-gray-200 leading-relaxed mb-4">
                            Pendaftaran santri baru Tahun Ajaran 2027-2028 telah resmi dibuka sejak <strong>12 Oktober 2026</strong> dan akan ditutup apabila kuota terpenuhi. Segera daftarkan putra-putri Anda secara online melalui SIAKAD PPTQ Al Fath.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-emerald-400/30">
                            <a href="https://siakad.alfathsolobaru.com/psb/register" class="bg-accent-main hover:bg-accent-hover text-brand-dark font-bold py-2.5 px-6 rounded-full text-sm text-center transition-all">Daftar Online Sekarang</a>
                            <a href="https://siakad.alfathsolobaru.com/psb" class="border border-white/60 text-white hover:bg-white/10 font-semibold py-2.5 px-6 rounded-full text-sm text-center transition-all">Sudah punya akun? Masuk</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="inline-block bg-accent-main text-brand-dark text-xs sm:text-sm font-bold px-5 py-2 rounded-full uppercase tracking-widest mb-6 shadow-sm">Tahun Ajaran 2027-2028</div>
            
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight tracking-tight">
                Penerimaan Santri Baru<br>
                <span class="text-accent-main">PPTQ Al Fath</span>
            </h1>
            
            <p class="text-lg sm:text-xl lg:text-2xl text-gray-100 mb-4 font-medium tracking-wide">Tingkat SMP & SMA (Putra & Putri)</p>
            <p class="text-base sm:text-lg text-accent-light italic mb-10 max-w-2xl mx-auto font-light leading-relaxed">"Sholih, Cerdas, Terampil, Mandiri"</p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 sm:gap-6 px-4">
                <a href="https://siakad.alfathsolobaru.com/psb/register" class="w-full sm:w-auto bg-accent-main hover:bg-accent-hover text-brand-dark font-bold py-3.5 px-8 rounded-full shadow-lg transition-all transform hover:-translate-y-1 text-center flex items-center justify-center gap-2">
                    Daftar Sekarang <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="https://siakad.alfathsolobaru.com/psb" class="w-full sm:w-auto bg-transparent border-2 border-white/70 text-white hover:bg-white/10 hover:border-white font-semibold py-3.5 px-8 rounded-full transition-all text-center">
                    Masuk Akun Pendaftar
                </a>
            </div>
            <p class="mt-6 text-sm text-gray-300">
                Ingin tahu dulu? <a href="#alur" class="text-accent-main font-semibold hover:underline">Pelajari alur & syarat</a> atau <a href="#kontak" class="text-accent-main font-semibold hover:underline">hubungi admin</a>.
            </p>
        </div>
    </header>

    <section id="informasi" class="py-16 md:py-24 px-4 bg-brand-pale relative">
        <div class="max-w-6xl mx-auto">
            <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                <div class="bg-white rounded-3xl p-8 lg:p-12 shadow-card border-t-[6px] border-brand-primary h-full flex flex-col">
                    <div class="flex items-center gap-5 mb-8">
                        <div class="bg-brand-light p-4 rounded-2xl text-brand-primary shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                        <h2 class="text-3xl font-extrabold text-brand-dark tracking-tight">Visi</h2>
                    </div>
                    <div class="relative flex-1">
                        <span class="text-6xl text-brand-light absolute -top-6 -left-4 font-serif leading-none">"</span>
                        <p class="text-lg md:text-xl text-gray-700 italic leading-relaxed relative z-10 pt-2 font-medium">
                            Mencetak Generasi Qur'ani menuju Mardhotillah dengan menjadikan Al-Qur'an sebagai pedoman, bahasa sebagai kunci ilmu dan kemandirian sebagai karakter.
                        </p>
                    </div>
                </div>
                
                <div class="bg-white rounded-3xl p-8 lg:p-12 shadow-card border-t-[6px] border-accent-main h-full">
                    <div class="flex items-center gap-5 mb-8">
                        <div class="bg-accent-light p-4 rounded-2xl text-accent-hover shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </div>
                        <h2 class="text-3xl font-extrabold text-brand-dark tracking-tight">Misi</h2>
                    </div>
                    <ul class="space-y-5 text-gray-700">
                        <li class="flex gap-4 items-start">
                            <div class="text-accent-main mt-1 bg-accent-light p-1 rounded-full"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div> 
                            <span class="leading-relaxed text-base md:text-lg">Menerapkan sistem pendidikan berbasis Al-Qur'an dan Sunnah Nabi.</span>
                        </li>
                        <li class="flex gap-4 items-start">
                            <div class="text-accent-main mt-1 bg-accent-light p-1 rounded-full"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div> 
                            <span class="leading-relaxed text-base md:text-lg">Menumbuhkan semangat mempelajari dan menghafal Al-Qur'an secara intensif kepada seluruh santri.</span>
                        </li>
                        <li class="flex gap-4 items-start">
                            <div class="text-accent-main mt-1 bg-accent-light p-1 rounded-full"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div> 
                            <span class="leading-relaxed text-base md:text-lg">Menguasai bahasa Al-Qur'an (Bhs. Arab) dan bahasa internasional (Bhs. Inggris).</span>
                        </li>
                        <li class="flex gap-4 items-start">
                            <div class="text-accent-main mt-1 bg-accent-light p-1 rounded-full"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div> 
                            <span class="leading-relaxed text-base md:text-lg">Membentuk santri yang memiliki jiwa basthotan fil'ilmi wal jizmi bi idznillah.</span>
                        </li>
                        <li class="flex gap-4 items-start">
                            <div class="text-accent-main mt-1 bg-accent-light p-1 rounded-full"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div> 
                            <span class="leading-relaxed text-base md:text-lg">Menyiapkan lulusan yang mandiri dan penggerak ekonomi umat.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 px-4 bg-white">
        <div class="max-w-4xl mx-auto">
             <div class="bg-brand-primary rounded-3xl p-8 shadow-xl text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-accent-main opacity-20 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
                
                <h3 class="text-2xl md:text-3xl font-extrabold text-white mb-8 relative z-10">Kuota Pendaftaran</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 relative z-10">
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-4 rounded-2xl">
                        <div class="text-brand-pale text-sm uppercase tracking-wider mb-2 font-semibold">SMP Putra</div>
                        <div class="text-4xl font-black text-accent-main">20</div>
                        <div class="text-xs text-white/70 mt-1">Santri</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-4 rounded-2xl">
                        <div class="text-brand-pale text-sm uppercase tracking-wider mb-2 font-semibold">SMA Putra</div>
                        <div class="text-4xl font-black text-accent-main">15</div>
                        <div class="text-xs text-white/70 mt-1">Santri</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-4 rounded-2xl">
                        <div class="text-brand-pale text-sm uppercase tracking-wider mb-2 font-semibold">SMP Putri</div>
                        <div class="text-4xl font-black text-accent-main">20</div>
                        <div class="text-xs text-white/70 mt-1">Santri</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-4 rounded-2xl">
                        <div class="text-brand-pale text-sm uppercase tracking-wider mb-2 font-semibold">SMA Putri</div>
                        <div class="text-4xl font-black text-accent-main">15</div>
                        <div class="text-xs text-white/70 mt-1">Santri</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="alur" class="py-16 md:py-24 px-4 bg-white relative">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16 md:mb-20">
                <h2 class="text-sm md:text-base font-bold text-brand-primary tracking-widest uppercase mb-3">Panduan Langkah</h2>
                <h3 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-brand-dark tracking-tight">Alur Pendaftaran</h3>
                <div class="w-20 h-1.5 bg-accent-main mx-auto mt-6 rounded-full"></div>
            </div>
            
            <div class="relative">
                <div class="hidden sm:block absolute left-8 md:left-10 top-4 bottom-4 w-1 bg-brand-light rounded-full"></div>
                
                <div class="space-y-8 md:space-y-12 relative z-10">
                    <div class="flex flex-col sm:flex-row gap-5 md:gap-8 items-start group">
                        <div class="bg-white border-[4px] border-brand-primary text-brand-primary font-bold w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center text-xl md:text-2xl flex-shrink-0 shadow-sm group-hover:bg-brand-primary group-hover:text-white transition-all duration-300">01</div>
                        <div class="bg-brand-pale p-6 md:p-8 rounded-3xl shadow-sm border border-brand-light flex-1 w-full group-hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute top-0 right-0 bg-brand-primary text-white text-xs font-bold px-4 py-1.5 rounded-bl-xl">Dibuka 12 Okt 2026 - Kuota Terpenuhi</div>
                            <h4 class="font-extrabold text-xl md:text-2xl text-brand-dark mb-3 mt-2">Pendaftaran & Input Data</h4>
                            <p class="text-gray-700 text-base md:text-lg leading-relaxed">Calon siswa melakukan pendaftaran dan input data secara online melalui <a href="https://siakad.alfathsolobaru.com/psb/register" class="font-semibold text-brand-primary underline hover:text-brand-dark">SIAKAD PPTQ Al Fath</a>, atau datang langsung ke PPTQ Al-FATH Solo Baru di jam kerja (Senin - Kamis pukul 09.00-15.00). <br><span class="font-semibold text-red-600 mt-2 block">(Pendaftaran ditutup apabila kuota pendaftaran terpenuhi)</span></p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-5 md:gap-8 items-start group">
                        <div class="bg-white border-[4px] border-accent-main text-accent-hover font-bold w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center text-xl md:text-2xl flex-shrink-0 shadow-sm group-hover:bg-accent-main group-hover:text-white transition-all duration-300">02</div>
                        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-card border border-gray-100 flex-1 w-full group-hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute top-0 right-0 bg-accent-main text-brand-dark text-xs font-bold px-4 py-1.5 rounded-bl-xl">13 Desember 2026</div>
                            <h4 class="font-extrabold text-xl md:text-2xl text-brand-dark mb-3 mt-2">Tes Seleksi</h4>
                            <p class="text-gray-700 text-base md:text-lg leading-relaxed">Tes tertulis dan hafalan serta tes wawancara secara offline di PPTQ AL FATH Solo Baru.</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-5 md:gap-8 items-start group">
                        <div class="bg-white border-[4px] border-brand-primary text-brand-primary font-bold w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center text-xl md:text-2xl flex-shrink-0 shadow-sm group-hover:bg-brand-primary group-hover:text-white transition-all duration-300">03</div>
                        <div class="bg-brand-pale p-6 md:p-8 rounded-3xl shadow-sm border border-brand-light flex-1 w-full group-hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute top-0 right-0 bg-brand-primary text-white text-xs font-bold px-4 py-1.5 rounded-bl-xl">20 Desember 2026</div>
                            <h4 class="font-extrabold text-xl md:text-2xl text-brand-dark mb-3 mt-2">Pengumuman</h4>
                            <p class="text-gray-700 text-base md:text-lg leading-relaxed">Pengumuman hasil tes seleksi (Akan diumumkan melalui pesan pribadi/WA setelah tes seleksi selesai).</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-5 md:gap-8 items-start group">
                        <div class="bg-white border-[4px] border-accent-main text-accent-hover font-bold w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center text-xl md:text-2xl flex-shrink-0 shadow-sm group-hover:bg-accent-main group-hover:text-white transition-all duration-300">04</div>
                        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-card border border-gray-100 flex-1 w-full group-hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute top-0 right-0 bg-accent-main text-brand-dark text-xs font-bold px-4 py-1.5 rounded-bl-xl">21 Des 2026 - 22 Apr 2027</div>
                            <h4 class="font-extrabold text-xl md:text-2xl text-brand-dark mb-3 mt-2">Daftar Ulang</h4>
                            <p class="text-gray-700 text-base md:text-lg leading-relaxed">Daftar ulang (Dilakukan setelah dinyatakan lulus seleksi, bisa melalui online atau datang ke PPTQ AL FATH Solo Baru).</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-5 md:gap-8 items-start group">
                        <div class="bg-white border-[4px] border-brand-primary text-brand-primary font-bold w-16 h-16 md:w-20 md:h-20 rounded-full flex items-center justify-center text-xl md:text-2xl flex-shrink-0 shadow-sm group-hover:bg-brand-primary group-hover:text-white transition-all duration-300">05</div>
                        <div class="bg-brand-pale p-6 md:p-8 rounded-3xl shadow-sm border border-brand-light flex-1 w-full group-hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute top-0 right-0 bg-brand-primary text-white text-xs font-bold px-4 py-1.5 rounded-bl-xl">11 Juli 2027</div>
                            <h4 class="font-extrabold text-xl md:text-2xl text-brand-dark mb-3 mt-2">Pelaksanaan Khutbah Ta'aruf</h4>
                            <p class="text-gray-700 text-base md:text-lg leading-relaxed">Pelaksanaan orientasi khutbah ta'aruf.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="syarat" class="py-16 md:py-24 px-4 bg-brand-light relative">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16 md:mb-20">
                <h2 class="text-sm md:text-base font-bold text-brand-primary tracking-widest uppercase mb-3">Ketentuan Administratif</h2>
                <h3 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-brand-dark tracking-tight">Syarat & Rincian Biaya</h3>
                <div class="w-20 h-1.5 bg-brand-primary mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid lg:grid-cols-2 gap-8 mb-16">
                <!-- Syarat Umum & Berkas -->
                <div class="bg-white rounded-3xl p-8 lg:p-12 shadow-card h-full flex flex-col">
                    <h4 class="text-2xl font-extrabold text-brand-dark mb-6 border-b border-gray-100 pb-4">Syarat Umum</h4>
                    <ul class="space-y-4 mb-8 text-gray-700">
                        <li class="flex items-center gap-3"><span class="text-accent-main font-bold">✓</span> Lancar membaca Al-Qur'an</li>
                        <li class="flex items-center gap-3"><span class="text-accent-main font-bold">✓</span> Siap mentaati peraturan pondok</li>
                        <li class="flex items-center gap-3"><span class="text-accent-main font-bold">✓</span> Berakhlaq karimah</li>
                        <li class="flex items-center gap-3"><span class="text-accent-main font-bold">✓</span> Hafal minimal 1 juz</li>
                    </ul>

                    <h4 class="text-2xl font-extrabold text-brand-dark mb-6 border-b border-gray-100 pb-4">Syarat Berkas</h4>
                    <ul class="space-y-4 text-gray-700 text-sm md:text-base">
                        <li>• Membayar biaya pendaftaran <strong>Rp. 200.000</strong></li>
                        <li>• Menyerahkan fotocopy KK, Akta Kelahiran, Ijazah/SKL (masing-masing 4 lembar)</li>
                        <li>• Menyerahkan pas foto background merah 3x4 (4 lembar)</li>
                        <li>• Fotocopy KIP (bagi yang memiliki, 4 lembar)</li>
                    </ul>
                    <div class="mt-6 p-4 bg-brand-pale rounded-xl text-xs md:text-sm text-brand-dark font-medium">
                        *Stopmap: <strong>Biru</strong> untuk SMP, <strong>Kuning</strong> untuk SMA.
                    </div>
                </div>

                <!-- Rincian Biaya -->
                <div class="bg-brand-primary rounded-3xl p-8 lg:p-12 shadow-xl text-white flex flex-col justify-between">
                    <div>
                        <h4 class="text-2xl font-extrabold mb-6 border-b border-white/20 pb-4">Rincian Biaya Santri Baru</h4>
                        <div class="space-y-6">
                            <div class="bg-white/10 p-4 rounded-2xl">
                                <div class="text-accent-main text-sm font-bold">Biaya Pendaftaran</div>
                                <div class="text-2xl font-black text-white">Rp 200.000</div>
                            </div>
                            <div class="bg-white/10 p-4 rounded-2xl">
                                <div class="text-accent-main text-sm font-bold">Daftar Ulang (SMP & SMA Putra/Putri)</div>
                                <div class="text-3xl font-black text-white">Rp 6.900.000</div>
                                <p class="text-xs text-brand-pale mt-1">Meliputi infaq pengembangan, seragam, kitab, perlengkapan asrama, dll.</p>
                            </div>
                            <div class="bg-white/10 p-4 rounded-2xl">
                                <div class="text-accent-main text-sm font-bold">Syahriah / SPP Bulanan</div>
                                <div class="text-2xl font-black text-white">Rp 650.000 / bulan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="biaya" class="py-16 md:py-24 px-4 bg-white">
        <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-12">
            <div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-brand-dark mb-6">Target Kelulusan</h3>
                <div class="bg-brand-pale rounded-3xl p-8 shadow-sm">
                    <ul class="space-y-4 text-gray-700">
                        <li class="flex items-center gap-3">⭐ Hafal Al-Qur'an 30 Juz</li>
                        <li class="flex items-center gap-3">⭐ Hafal Hadits Arbain An-Nawawi</li>
                        <li class="flex items-center gap-3">⭐ Mampu berbahasa Arab aktif & berdakwah</li>
                        <li class="flex items-center gap-3">⭐ Beradab dan berakhlaq karimah</li>
                    </ul>
                </div>
            </div>
            <div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-brand-dark mb-6">Ekstrakurikuler</h3>
                <div class="bg-brand-pale rounded-3xl p-8 shadow-sm">
                    <p class="font-bold text-brand-primary mb-2">Putra:</p>
                    <p class="text-gray-700 mb-4 text-sm">Sepak Bola, Memanah, Futsal, Tenis Meja, Berenang, Pecinta Alam</p>
                    <p class="font-bold text-brand-primary mb-2">Putri:</p>
                    <p class="text-gray-700 text-sm">Berenang, Memanah, Kesehatan, Kerajinan, Memasak, Komputer</p>
                </div>
            </div>
        </div>
    </section>

    <section id="daftar" class="py-16 md:py-20 px-4 bg-brand-pale">
        <div class="max-w-4xl mx-auto">
            <div class="bg-brand-primary rounded-3xl p-8 md:p-12 shadow-xl text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-accent-main opacity-20 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>

                <h3 class="text-2xl md:text-3xl font-extrabold text-white mb-4 relative z-10">Daftar Online Santri Baru</h3>
                <p class="text-brand-pale text-base md:text-lg mb-8 max-w-2xl mx-auto relative z-10">
                    Buat akun pendaftaran melalui SIAKAD PPTQ Al Fath. Sudah punya akun? Silakan masuk untuk melengkapi data dan memantau status pendaftaran.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 relative z-10">
                    <a href="https://siakad.alfathsolobaru.com/psb/register" class="w-full sm:w-auto bg-accent-main hover:bg-accent-hover text-brand-dark font-bold py-3.5 px-8 rounded-full shadow-lg transition-all transform hover:-translate-y-1 text-center">
                        Daftar Akun Baru
                    </a>
                    <a href="https://siakad.alfathsolobaru.com/psb" class="w-full sm:w-auto bg-transparent border-2 border-white/70 text-white hover:bg-white/10 hover:border-white font-semibold py-3.5 px-8 rounded-full transition-all text-center">
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer id="kontak" class="bg-brand-dark text-white pt-20 pb-10 px-4 border-t-[8px] border-accent-main mt-auto">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-12 mb-12">
            <div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="bg-white p-2.5 rounded-xl">
                        <svg class="h-8 w-8 text-brand-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight">PPTQ Al Fath Solo Baru</span>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed mb-4">
                    <strong>Pondok Putra:</strong> Dukuh Sengon RT/RW 03/11, Desa Langenharjo, Grogol, Sukoharjo, Jateng.
                </p>
                <p class="text-gray-300 text-sm leading-relaxed">
                    <strong>Pondok Putri:</strong> Dukuh Jati RT/RW 03/03, Desa Langenharjo, Grogol, Sukoharjo, Jateng.
                </p>
            </div>

            <div>
                <h3 class="text-xl font-bold mb-6 text-white border-b border-gray-700 pb-3">Kontak Resmi Pendaftaran</h3>
                <div class="space-y-4">
                    <a href="https://wa.me/6285878153759" target="_blank" class="flex items-center gap-4 bg-brand-primary/50 hover:bg-brand-primary p-4 rounded-xl transition-all border border-brand-secondary/30">
                        <div class="bg-green-500 p-2.5 rounded-lg text-white font-bold">WA</div>
                        <div>
                            <div class="text-accent-main text-xs font-bold uppercase">Admin Putra</div>
                            <div class="font-extrabold text-white text-lg">0858-7815-3759 <span class="text-xs text-gray-300">(Ustadz Farhan)</span></div>
                        </div>
                    </a>
                    
                    <a href="https://wa.me/6281233399812" target="_blank" class="flex items-center gap-4 bg-brand-primary/50 hover:bg-brand-primary p-4 rounded-xl transition-all border border-brand-secondary/30">
                        <div class="bg-green-500 p-2.5 rounded-lg text-white font-bold">WA</div>
                        <div>
                            <div class="text-accent-main text-xs font-bold uppercase">Admin Putri</div>
                            <div class="font-extrabold text-white text-lg">0812-3339-9812</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="max-w-6xl mx-auto text-center border-t border-gray-800 pt-6">
            <p class="text-gray-400 text-sm">
                &copy; PPTQ Al Fath Solo Baru. Mendidik Generasi Qur'ani. | Instagram: @pptq_al_fath
            </p>
        </div>
    </footer>

    <script>
        // Toggle Mobile Menu
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu-panel');
        const iconOpen = document.getElementById('menu-icon-open');
        const iconClose = document.getElementById('menu-icon-close');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        function toggleMenu() {
            menu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        }

        btn.addEventListener('click', toggleMenu);
        mobileLinks.forEach(link => link.addEventListener('click', toggleMenu));
    </script>
</body>
</html>