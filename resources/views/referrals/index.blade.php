<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> Rujukan - CekGO</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS CDNs -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/primevue@3.38.1/resources/themes/lara-light-teal/theme.css">
    <link rel="stylesheet" href="https://unpkg.com/primevue@3.38.1/resources/primevue.min.css">
    <link rel="stylesheet" href="https://unpkg.com/primeicons@6.0.1/primeicons.css">
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0fbf9',
                            100: '#dcf6f1',
                            200: '#b9edd4',
                            500: '#14b8a6', // Teal
                            600: '#0d9488',
                            700: '#0f766e',
                        },
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background-color: #f8fafc;
        }
        .p-component {
            font-family: 'Outfit', sans-serif !important;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
    </style>
</head>
<body class="antialiased text-slate-800">
    <div id="app" class="min-h-screen flex flex-col">
        
        <!-- Premium Navbar Header -->
        <header class="glass-panel sticky top-0 z-50 px-6 py-4 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-teal-600 flex items-center justify-center text-white shadow-md shadow-teal-600/20">
                    <i class="pi pi-directions-alt text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-teal-950">CekGO</h1>
                    <p class="text-xs text-teal-600/80 font-medium">Kelola Rujukan Kesehatan Siswa</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- User Profile Badge -->
                <div class="flex items-center gap-3 bg-teal-50 border border-teal-100 rounded-full py-1.5 pl-3 pr-4">
                    <div class="h-7 w-7 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-xs">
                        @{{ userInitial }}
                    </div>
                    <div class="text-left leading-none">
                        <p class="text-xs font-semibold text-teal-950">@{{ user.name }}</p>
                        <span class="text-[10px] text-teal-600 font-medium capitalize">@{{ user.role }}</span>
                    </div>
                </div>
                
                <a href="/admin" class="p-button p-component p-button-secondary p-button-outlined p-button-sm rounded-lg flex items-center gap-2">
                    <i class="pi pi-arrow-left"></i>
                    <span>Kembali ke Panel</span>
                </a>
            </div>
        </header>

        <div class="flex-1 max-w-7xl w-full mx-auto p-6 md:p-8 flex flex-col gap-6">
            
            <!-- Dynamic Navigation Tabs -->
            <div class="flex border-b border-slate-200 gap-1 bg-white p-1.5 rounded-xl shadow-sm border border-slate-100">
                <button @click="setActiveTab('dashboard')" :class="activeTab === 'dashboard' ? 'bg-teal-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50'" class="flex-1 md:flex-initial flex items-center justify-center gap-2 py-2.5 px-6 rounded-lg text-sm transition-all duration-200">
                    <i class="pi pi-chart-bar"></i>
                    <span>Dashboard Rujukan</span>
                </button>
                <button @click="setActiveTab('list')" :class="activeTab === 'list' ? 'bg-teal-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50'" class="flex-1 md:flex-initial flex items-center justify-center gap-2 py-2.5 px-6 rounded-lg text-sm transition-all duration-200">
                    <i class="pi pi-list"></i>
                    <span>Daftar Rujukan</span>
                </button>
                <button @click="setActiveTab('school-recap')" :class="activeTab === 'school-recap' ? 'bg-teal-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50'" class="flex-1 md:flex-initial flex items-center justify-center gap-2 py-2.5 px-6 rounded-lg text-sm transition-all duration-200">
                    <i class="pi pi-building"></i>
                    <span>Rekap Sekolah</span>
                </button>
                <button @click="setActiveTab('class-recap')" :class="activeTab === 'class-recap' ? 'bg-teal-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50'" class="flex-1 md:flex-initial flex items-center justify-center gap-2 py-2.5 px-6 rounded-lg text-sm transition-all duration-200">
                    <i class="pi pi-table"></i>
                    <span>Rekap Kelas</span>
                </button>
                <button @click="setActiveTab('export')" :class="activeTab === 'export' ? 'bg-teal-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50'" class="flex-1 md:flex-initial flex items-center justify-center gap-2 py-2.5 px-6 rounded-lg text-sm transition-all duration-200">
                    <i class="pi pi-download"></i>
                    <span>Ekspor Data</span>
                </button>
            </div>

            <!-- TAB 1: DASHBOARD ANALYTICS -->
            <div v-if="activeTab === 'dashboard'" class="flex flex-col gap-6">
                <!-- Dashboard Filters -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Sekolah</label>
                        <select v-model="dashboardFilters.school_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-teal-500/20 focus:outline-none">
                            <option value="">Semua Sekolah</option>
                            <option v-for="school in options.schools" :key="school.id" :value="school.id">@{{ school.nama_sekolah }}</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Kecamatan</label>
                        <select v-model="dashboardFilters.kecamatan_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-teal-500/20 focus:outline-none">
                            <option value="">Semua Kecamatan</option>
                            <option v-for="kec in options.kecamatans" :key="kec.id" :value="kec.id">@{{ kec.nama_kecamatan }}</option>
                        </select>
                    </div>
                    <div class="w-full md:w-auto flex gap-2">
                        <button @click="fetchDashboardData" class="bg-teal-600 hover:bg-teal-700 text-white font-medium text-sm py-2.5 px-6 rounded-lg flex items-center gap-2 transition-all">
                            <i class="pi pi-filter"></i>
                            <span>Terapkan</span>
                        </button>
                        <button @click="resetDashboardFilters" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm py-2.5 px-4 rounded-lg transition-all">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Stats Counters -->
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl"><i class="pi pi-users"></i></div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Total Rujukan</p>
                            <h3 class="text-2xl font-bold text-slate-800">@{{ stats.status_counts.total }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl"><i class="pi pi-exclamation-circle"></i></div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Belum Dirujuk</p>
                            <h3 class="text-2xl font-bold text-slate-800">@{{ stats.status_counts.belum_dirujuk }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl"><i class="pi pi-send"></i></div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Sudah Dirujuk</p>
                            <h3 class="text-2xl font-bold text-slate-800">@{{ stats.status_counts.sudah_dirujuk }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl"><i class="pi pi-sync"></i></div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Dalam Proses</p>
                            <h3 class="text-2xl font-bold text-slate-800">@{{ stats.status_counts.dalam_tindak_lanjut }}</h3>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 col-span-2 lg:col-span-1">
                        <div class="h-12 w-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl"><i class="pi pi-check-circle"></i></div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase">Selesai</p>
                            <h3 class="text-2xl font-bold text-slate-800">@{{ stats.status_counts.selesai }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Charts Display Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center min-h-[350px]">
                        <h4 class="text-sm font-semibold text-slate-600 mb-4 self-start">Proporsi Status Rujukan</h4>
                        <div class="relative w-full max-w-[230px] flex-1 flex items-center justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-center min-h-[350px] lg:col-span-2">
                        <h4 class="text-sm font-semibold text-slate-600 mb-4">Rujukan Berdasarkan Jenis Pemeriksaan</h4>
                        <div class="w-full flex-1 flex items-center justify-center">
                            <canvas id="typeChart" style="max-height: 250px;"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-center min-h-[350px] lg:col-span-3">
                        <h4 class="text-sm font-semibold text-slate-600 mb-4">Tren Perkembangan Rujukan Bulanan</h4>
                        <div class="w-full flex-1 flex items-center justify-center">
                            <canvas id="trendChart" style="max-height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: DAFTAR RUJUKAN TABLE -->
            <div v-if="activeTab === 'list'" class="flex flex-col gap-6">
                <!-- Advanced Filter Box -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Sekolah</label>
                            <select v-model="filters.school_id" @change="onSchoolChange" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-teal-500/20 focus:outline-none">
                                <option value="">Semua Sekolah</option>
                                <option v-for="school in options.schools" :key="school.id" :value="school.id">@{{ school.nama_sekolah }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Kelas</label>
                            <select v-model="filters.school_class_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-teal-500/20 focus:outline-none" :disabled="!filters.school_id">
                                <option value="">Semua Kelas</option>
                                <option v-for="cls in filteredClasses" :key="cls.id" :value="cls.id">@{{ cls.nama_kelas }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Jenis Pemeriksaan</label>
                            <select v-model="filters.jenis_pemeriksaan" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-teal-500/20 focus:outline-none">
                                <option value="">Semua Jenis</option>
                                <option v-for="type in options.jenis_options" :key="type" :value="type">@{{ type }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Status Rujukan</label>
                            <select v-model="filters.status_rujukan" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-teal-500/20 focus:outline-none">
                                <option value="">Semua Status</option>
                                <option v-for="stat in options.status_options" :key="stat" :value="stat">@{{ stat }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Tanggal Mulai</label>
                            <input type="date" v-model="filters.tanggal_mulai" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/20">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Tanggal Selesai</label>
                            <input type="date" v-model="filters.tanggal_selesai" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/20">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Cari Siswa (Nama/NIK/NISN)</label>
                            <input type="text" v-model="filters.search" placeholder="Masukkan nama, NIK, atau NISN siswa..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/20">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-2">
                        <button @click="applyFilters" class="bg-teal-600 hover:bg-teal-700 text-white font-medium text-sm py-2.5 px-6 rounded-lg flex items-center gap-2 transition-all">
                            <i class="pi pi-search"></i>
                            <span>Terapkan Filter</span>
                        </button>
                        <button @click="resetFilters" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm py-2.5 px-4 rounded-lg transition-all">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Referrals Data Table -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-4 bg-slate-50/60">
                        <h4 class="text-sm font-bold text-slate-800">Daftar Rujukan</h4>
                        <div class="flex gap-2">
                            <button @click="exportReferrals('xlsx')" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-medium text-xs py-2 px-4 rounded-lg flex items-center gap-2 transition-all">
                                <i class="pi pi-file-excel text-emerald-600 font-bold"></i>
                                <span>Unduh Excel</span>
                            </button>
                            <button @click="exportReferrals('pdf')" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-medium text-xs py-2 px-4 rounded-lg flex items-center gap-2 transition-all">
                                <i class="pi pi-file-pdf text-rose-600 font-bold"></i>
                                <span>Unduh PDF</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-400 font-bold text-[10px] uppercase border-b border-slate-100">
                                    <th class="py-4 px-6 text-center w-12">No</th>
                                    <th class="py-4 px-6">Siswa</th>
                                    <th class="py-4 px-6">Identitas</th>
                                    <th class="py-4 px-6">Sekolah & Kelas</th>
                                    <th class="py-4 px-6">Jenis Pemeriksaan</th>
                                    <th class="py-4 px-6">Alasan</th>
                                    <th class="py-4 px-6">Status</th>
                                    <th class="py-4 px-6">Tgl Pemeriksaan</th>
                                    <th class="py-4 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <tr v-for="(ref, index) in referrals" :key="ref.id" class="hover:bg-slate-50/60 transition-all">
                                    <td class="py-4 px-6 text-center font-medium text-slate-400">@{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-800">@{{ ref.student?.nama_lengkap }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium">@{{ ref.student?.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div><span class="text-slate-400 font-medium">NIK:</span> @{{ ref.student?.nik }}</div>
                                        <div><span class="text-slate-400 font-medium">NISN:</span> @{{ ref.student?.nisn }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-semibold text-slate-700">@{{ ref.school?.nama_sekolah }}</div>
                                        <div class="text-xs text-slate-400">Kelas @{{ ref.class?.nama_kelas }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full font-bold text-[10px]" :class="getJenisBadgeClass(ref.jenis_pemeriksaan)">
                                            @{{ ref.jenis_pemeriksaan }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-medium text-slate-600 max-w-[200px] truncate" :title="ref.alasan_rujukan">
                                        @{{ ref.alasan_rujukan }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full font-bold text-[10px]" :class="getStatusBadgeClass(ref.status_rujukan)">
                                            @{{ ref.status_rujukan }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-medium text-slate-500">
                                        @{{ formatDate(ref.tanggal_pemeriksaan) }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <button @click="openDetail(ref.id)" class="text-teal-600 hover:text-teal-700 bg-teal-50 hover:bg-teal-100 h-8 w-8 rounded-lg flex items-center justify-center transition-all inline-block">
                                            <i class="pi pi-pencil text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="referrals.length === 0">
                                    <td colspan="9" class="py-8 text-center text-slate-400">
                                        <i class="pi pi-folder-open text-2xl mb-2 block"></i>
                                        <span>Data rujukan tidak ditemukan.</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between gap-4 bg-slate-50/40">
                        <span class="text-xs text-slate-500">
                            Menampilkan @{{ pagination.from ?? 0 }} sampai @{{ pagination.to ?? 0 }} dari @{{ pagination.total }} data
                        </span>
                        
                        <div class="flex items-center gap-1.5">
                            <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="h-8 w-8 rounded-lg border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:bg-slate-50 disabled:opacity-50 transition-all">
                                <i class="pi pi-angle-left"></i>
                            </button>
                            
                            <button v-for="page in pages" :key="page" @click="changePage(page)" :class="pagination.current_page === page ? 'bg-teal-600 text-white font-medium border-teal-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'" class="h-8 min-w-[32px] px-2 rounded-lg border flex items-center justify-center text-xs transition-all">
                                @{{ page }}
                            </button>
                            
                            <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="h-8 w-8 rounded-lg border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:bg-slate-50 disabled:opacity-50 transition-all">
                                <i class="pi pi-angle-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: REKAP SEKOLAH -->
            <div v-if="activeTab === 'school-recap'" class="flex flex-col gap-6">
                <!-- Filter bar -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Kecamatan</label>
                        <select v-model="recapFilters.kecamatan_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-teal-500/20 focus:outline-none">
                            <option value="">Semua Kecamatan</option>
                            <option v-for="kec in options.kecamatans" :key="kec.id" :value="kec.id">@{{ kec.nama_kecamatan }}</option>
                        </select>
                    </div>
                    <div class="w-full md:w-auto flex gap-2">
                        <button @click="fetchSchoolRecaps" class="bg-teal-600 hover:bg-teal-700 text-white font-medium text-sm py-2.5 px-6 rounded-lg flex items-center gap-2 transition-all">
                            <i class="pi pi-search"></i>
                            <span>Terapkan</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-400 font-bold text-[10px] uppercase border-b border-slate-100">
                                    <th class="py-4 px-6 text-center w-12">No</th>
                                    <th class="py-4 px-6">Nama Sekolah</th>
                                    <th class="py-4 px-6 text-center">Belum Dirujuk</th>
                                    <th class="py-4 px-6 text-center">Sudah Dirujuk</th>
                                    <th class="py-4 px-6 text-center">Dalam Tindak Lanjut</th>
                                    <th class="py-4 px-6 text-center">Selesai</th>
                                    <th class="py-4 px-6 text-center font-bold">Total Rujukan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <tr v-for="(item, index) in schoolRecaps" :key="item.school_id" class="hover:bg-slate-50/60 transition-all">
                                    <td class="py-4 px-6 text-center font-medium text-slate-400">@{{ index + 1 }}</td>
                                    <td class="py-4 px-6 font-bold text-slate-700">@{{ item.nama_sekolah }}</td>
                                    <td class="py-4 px-6 text-center font-semibold text-red-600 bg-red-50/20">@{{ item.belum_dirujuk }}</td>
                                    <td class="py-4 px-6 text-center font-semibold text-amber-600 bg-amber-50/20">@{{ item.sudah_dirujuk }}</td>
                                    <td class="py-4 px-6 text-center font-semibold text-blue-600 bg-blue-50/20">@{{ item.dalam_tindak_lanjut }}</td>
                                    <td class="py-4 px-6 text-center font-semibold text-teal-600 bg-teal-50/20">@{{ item.selesai }}</td>
                                    <td class="py-4 px-6 text-center font-bold text-slate-800 bg-slate-50">@{{ item.total_rujukan }}</td>
                                </tr>
                                <tr v-if="schoolRecaps.length === 0">
                                    <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada rekap sekolah.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 4: REKAP KELAS -->
            <div v-if="activeTab === 'class-recap'" class="flex flex-col gap-6">
                <!-- Filter bar -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Sekolah</label>
                        <select v-model="recapFilters.school_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-teal-500/20 focus:outline-none">
                            <option value="">Semua Sekolah</option>
                            <option v-for="school in options.schools" :key="school.id" :value="school.id">@{{ school.nama_sekolah }}</option>
                        </select>
                    </div>
                    <div class="w-full md:w-auto flex gap-2">
                        <button @click="fetchClassRecaps" class="bg-teal-600 hover:bg-teal-700 text-white font-medium text-sm py-2.5 px-6 rounded-lg flex items-center gap-2 transition-all">
                            <i class="pi pi-search"></i>
                            <span>Terapkan</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-400 font-bold text-[10px] uppercase border-b border-slate-100">
                                    <th class="py-4 px-6 text-center w-12">No</th>
                                    <th class="py-4 px-6">Sekolah</th>
                                    <th class="py-4 px-6">Kelas</th>
                                    <th class="py-4 px-6 text-center">Belum Dirujuk</th>
                                    <th class="py-4 px-6 text-center">Sudah Dirujuk</th>
                                    <th class="py-4 px-6 text-center">Dalam Tindak Lanjut</th>
                                    <th class="py-4 px-6 text-center">Selesai</th>
                                    <th class="py-4 px-6 text-center font-bold">Total Rujukan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <tr v-for="(item, index) in classRecaps" :key="item.school_class_id" class="hover:bg-slate-50/60 transition-all">
                                    <td class="py-4 px-6 text-center font-medium text-slate-400">@{{ index + 1 }}</td>
                                    <td class="py-4 px-6 font-semibold text-slate-700">@{{ item.nama_sekolah }}</td>
                                    <td class="py-4 px-6 font-semibold text-slate-600">Kelas @{{ item.nama_kelas }}</td>
                                    <td class="py-4 px-6 text-center font-semibold text-red-600">@{{ item.belum_dirujuk }}</td>
                                    <td class="py-4 px-6 text-center font-semibold text-amber-600">@{{ item.sudah_dirujuk }}</td>
                                    <td class="py-4 px-6 text-center font-semibold text-blue-600">@{{ item.dalam_tindak_lanjut }}</td>
                                    <td class="py-4 px-6 text-center font-semibold text-teal-600">@{{ item.selesai }}</td>
                                    <td class="py-4 px-6 text-center font-bold text-slate-800 bg-slate-50">@{{ item.total_rujukan }}</td>
                                </tr>
                                <tr v-if="classRecaps.length === 0">
                                    <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada rekap kelas.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 5: EXPORT FORM -->
            <div v-if="activeTab === 'export'" class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-6 max-w-2xl mx-auto">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-lg font-bold text-slate-800">Ekspor Laporan Rujukan</h3>
                    <p class="text-sm text-slate-400">Ekspor seluruh data rujukan berdasarkan filter spesifik ke format Excel (XLSX) atau PDF.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Sekolah</label>
                        <select v-model="exportFilters.school_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none">
                            <option value="">Semua Sekolah</option>
                            <option v-for="school in options.schools" :key="school.id" :value="school.id">@{{ school.nama_sekolah }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Jenjang</label>
                        <select v-model="exportFilters.jenjang" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none">
                            <option value="">Semua Jenjang</option>
                            <option value="SD">SD (Sekolah Dasar)</option>
                            <option value="SMP">SMP (Sekolah Menengah Pertama)</option>
                            <option value="SMA">SMA (Sekolah Menengah Atas)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Jenis Pemeriksaan</label>
                        <select v-model="exportFilters.jenis_pemeriksaan" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none">
                            <option value="">Semua Jenis</option>
                            <option v-for="type in options.jenis_options" :key="type" :value="type">@{{ type }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Status Rujukan</label>
                        <select v-model="exportFilters.status_rujukan" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none">
                            <option value="">Semua Status</option>
                            <option v-for="stat in options.status_options" :key="stat" :value="stat">@{{ stat }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Tanggal Mulai</label>
                        <input type="date" v-model="exportFilters.tanggal_mulai" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Tanggal Selesai</label>
                        <input type="date" v-model="exportFilters.tanggal_selesai" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none">
                    </div>
                </div>

                <div class="flex gap-3 justify-end mt-4 pt-4 border-t border-slate-100">
                    <button @click="doExport('xlsx')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm py-2.5 px-6 rounded-lg flex items-center gap-2 transition-all">
                        <i class="pi pi-file-excel"></i>
                        <span>Ekspor Excel (.xlsx)</span>
                    </button>
                    <button @click="doExport('pdf')" class="bg-rose-600 hover:bg-rose-700 text-white font-medium text-sm py-2.5 px-6 rounded-lg flex items-center gap-2 transition-all">
                        <i class="pi pi-file-pdf"></i>
                        <span>Ekspor PDF (.pdf)</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- DETAIL & STATUS UPDATE MODAL (DIALOUGE) -->
        <div v-if="detailDialog" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[85vh] overflow-y-auto flex flex-col shadow-2xl animate-[fadeIn_0.2s_ease-out]">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Detail Rujukan Kesehatan</h3>
                        <span class="text-xs text-slate-400">ID Rujukan: #@{{ selectedReferral.id }}</span>
                    </div>
                    <button @click="detailDialog = false" class="text-slate-400 hover:text-slate-600 h-8 w-8 rounded-lg flex items-center justify-center hover:bg-slate-100">
                        <i class="pi pi-times"></i>
                    </button>
                </div>
                
                <!-- Body -->
                <div class="p-6 flex flex-col gap-6 flex-1">
                    <!-- Student details card -->
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 uppercase font-semibold mb-1 block">Nama Lengkap</span>
                            <span class="text-slate-800 font-bold text-sm block">@{{ selectedReferral.student?.nama_lengkap }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase font-semibold mb-1 block">NIK / NISN</span>
                            <span class="text-slate-800 font-medium block">@{{ selectedReferral.student?.nik }} / @{{ selectedReferral.student?.nisn }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase font-semibold mb-1 block">Sekolah</span>
                            <span class="text-slate-800 font-semibold block">@{{ selectedReferral.school?.nama_sekolah }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase font-semibold mb-1 block">Kelas</span>
                            <span class="text-slate-800 font-semibold block">Kelas @{{ selectedReferral.class?.nama_kelas }}</span>
                        </div>
                    </div>

                    <!-- Examination details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 mb-3 border-b border-slate-100 pb-2">Informasi Pemeriksaan</h4>
                            <div class="flex flex-col gap-2.5 text-xs text-slate-600">
                                <div class="flex justify-between"><span class="text-slate-400">Jenis Pemeriksaan:</span><strong class="text-slate-800 font-semibold">@{{ selectedReferral.jenis_pemeriksaan }}</strong></div>
                                <div class="flex justify-between"><span class="text-slate-400">Tanggal Pemeriksaan:</span><strong class="text-slate-800">@{{ formatDate(selectedReferral.tanggal_pemeriksaan) }}</strong></div>
                                <div class="flex justify-between"><span class="text-slate-400">Petugas Pemeriksa:</span><strong class="text-slate-800">@{{ selectedReferral.petugas_pemeriksa }}</strong></div>
                                <div class="flex flex-col gap-1 border-t border-dashed border-slate-100 pt-2">
                                    <span class="text-slate-400">Alasan Rujukan:</span>
                                    <p class="text-slate-800 font-medium p-2.5 bg-red-50 text-red-700 border border-red-100 rounded-lg">@{{ selectedReferral.alasan_rujukan }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Update status form -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 mb-3 border-b border-slate-100 pb-2">Tindak Lanjut & Update Status</h4>
                            <div class="flex flex-col gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Status Rujukan</label>
                                    <select v-model="statusForm.status_rujukan" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/20">
                                        <option v-for="stat in options.status_options" :key="stat" :value="stat">@{{ stat }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase">Catatan / Tindak Lanjut</label>
                                    <textarea v-model="statusForm.catatan" rows="3" placeholder="Masukkan catatan penanganan atau hasil rujukan..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/20"></textarea>
                                </div>
                                <button @click="submitStatusUpdate" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium text-xs py-2.5 rounded-lg flex justify-center items-center gap-2 transition-all">
                                    <i class="pi pi-check"></i>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Logs History -->
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-3 border-b border-slate-100 pb-2">Riwayat Tindak Lanjut</h4>
                        <div class="flex flex-col gap-3 max-h-[150px] overflow-y-auto pr-2">
                            <div v-for="log in selectedReferral.status_histories" :key="log.id" class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs flex justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-full font-bold text-[8px] bg-slate-200 text-slate-600">@{{ log.status_lama || 'Baru' }}</span>
                                        <i class="pi pi-arrow-right text-[8px] text-slate-400"></i>
                                        <span class="px-2 py-0.5 rounded-full font-bold text-[8px]" :class="getStatusBadgeClass(log.status_baru)">@{{ log.status_baru }}</span>
                                    </div>
                                    <p class="mt-2 text-slate-700 font-medium">@{{ log.catatan || 'Tidak ada catatan' }}</p>
                                </div>
                                <div class="text-right text-[10px] text-slate-400 leading-tight">
                                    <p class="font-bold text-slate-500">@{{ log.user?.name }}</p>
                                    <span>@{{ formatDateTime(log.created_at) }}</span>
                                </div>
                            </div>
                            <div v-if="!selectedReferral.status_histories || selectedReferral.status_histories.length === 0" class="text-slate-400 text-xs py-2 text-center">
                                Belum ada riwayat tindak lanjut.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS CDNs -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    activeTab: 'dashboard',
                    user: {
                        name: 'Guest User',
                        role: 'Guest',
                        school_id: null,
                    },
                    options: {
                        schools: [],
                        classes: [],
                        kecamatans: [],
                        kelurahans: [],
                        status_options: [],
                        jenis_options: [],
                    },
                    filters: {
                        school_id: '',
                        school_class_id: '',
                        jenis_pemeriksaan: '',
                        status_rujukan: '',
                        tanggal_mulai: '',
                        tanggal_selesai: '',
                        search: '',
                    },
                    dashboardFilters: {
                        school_id: '',
                        kecamatan_id: '',
                    },
                    recapFilters: {
                        school_id: '',
                        kecamatan_id: '',
                    },
                    exportFilters: {
                        school_id: '',
                        jenjang: '',
                        jenis_pemeriksaan: '',
                        status_rujukan: '',
                        tanggal_mulai: '',
                        tanggal_selesai: '',
                    },
                    referrals: [],
                    schoolRecaps: [],
                    classRecaps: [],
                    stats: {
                        status_counts: { total: 0, belum_dirujuk: 0, sudah_dirujuk: 0, dalam_tindak_lanjut: 0, selesai: 0 },
                        type_counts: { gizi: 0, gigi: 0, mata: 0, telinga: 0, umum: 0 },
                        trend: [],
                    },
                    pagination: {
                        current_page: 1,
                        per_page: 15,
                        total: 0,
                        last_page: 1,
                        from: 0,
                        to: 0,
                    },
                    detailDialog: false,
                    selectedReferral: {},
                    statusForm: {
                        status_rujukan: '',
                        catatan: '',
                    },
                    charts: {
                        status: null,
                        type: null,
                        trend: null,
                    }
                }
            },
            computed: {
                userInitial() {
                    return this.user.name ? this.user.name.charAt(0).toUpperCase() : 'G';
                },
                filteredClasses() {
                    if (!this.filters.school_id) return [];
                    return this.options.classes.filter(c => c.school_id === parseInt(this.filters.school_id));
                },
                pages() {
                    let pagesArray = [];
                    let start = Math.max(1, this.pagination.current_page - 2);
                    let end = Math.min(this.pagination.last_page, this.pagination.current_page + 2);
                    for (let i = start; i <= end; i++) {
                        pagesArray.push(i);
                    }
                    return pagesArray;
                }
            },
            mounted() {
                // Fetch Dropdowns & Current User
                this.fetchOptions().then(() => {
                    // Set default scopes based on role
                    if (this.user.school_id) {
                        this.filters.school_id = this.user.school_id;
                        this.dashboardFilters.school_id = this.user.school_id;
                        this.recapFilters.school_id = this.user.school_id;
                        this.exportFilters.school_id = this.user.school_id;
                    }
                    if (this.user.kecamatan_id) {
                        this.dashboardFilters.kecamatan_id = this.user.kecamatan_id;
                        this.recapFilters.kecamatan_id = this.user.kecamatan_id;
                    }
                    
                    this.fetchDashboardData();
                    this.fetchData();
                    this.fetchSchoolRecaps();
                    this.fetchClassRecaps();
                });
            },
            methods: {
                setActiveTab(tab) {
                    this.activeTab = tab;
                    if (tab === 'dashboard') {
                        this.$nextTick(() => {
                            this.fetchDashboardData();
                        });
                    }
                },
                fetchOptions() {
                    return axios.get('/api/referrals/options')
                        .then(res => {
                            this.options = res.data;
                            this.user = res.data.user;
                        })
                        .catch(err => {
                            console.error('Gagal mengambil options', err);
                        });
                },
                fetchData(page = 1) {
                    let params = { ...this.filters, page: page };
                    axios.get('/api/referrals', { params: params })
                        .then(res => {
                            this.referrals = res.data.data;
                            this.pagination = res.data.meta;
                        })
                        .catch(err => {
                            console.error('Gagal memuat daftar rujukan', err);
                        });
                },
                changePage(page) {
                    if (page >= 1 && page <= this.pagination.last_page) {
                        this.fetchData(page);
                    }
                },
                onSchoolChange() {
                    this.filters.school_class_id = '';
                },
                applyFilters() {
                    this.fetchData(1);
                },
                resetFilters() {
                    this.filters = {
                        school_id: this.user.school_id || '',
                        school_class_id: '',
                        jenis_pemeriksaan: '',
                        status_rujukan: '',
                        tanggal_mulai: '',
                        tanggal_selesai: '',
                        search: '',
                    };
                    this.fetchData(1);
                },
                resetDashboardFilters() {
                    this.dashboardFilters = {
                        school_id: this.user.school_id || '',
                        kecamatan_id: this.user.kecamatan_id || '',
                    };
                    this.fetchDashboardData();
                },
                fetchSchoolRecaps() {
                    let params = {};
                    if (this.recapFilters.kecamatan_id) params.kecamatan_id = this.recapFilters.kecamatan_id;
                    if (this.recapFilters.school_id) params.school_id = this.recapFilters.school_id;
                    
                    axios.get('/api/referrals/recap-school', { params: params })
                        .then(res => {
                            this.schoolRecaps = res.data;
                        });
                },
                fetchClassRecaps() {
                    let params = {};
                    if (this.recapFilters.school_id) params.school_id = this.recapFilters.school_id;

                    axios.get('/api/referrals/recap-class', { params: params })
                        .then(res => {
                            this.classRecaps = res.data;
                        });
                },
                fetchDashboardData() {
                    axios.get('/api/referrals/dashboard', { params: this.dashboardFilters })
                        .then(res => {
                            this.stats = res.data;
                            this.$nextTick(() => {
                                this.renderCharts();
                            });
                        });
                },
                renderCharts() {
                    // Status Doughnut Chart
                    const ctxStatus = document.getElementById('statusChart');
                    if (ctxStatus) {
                        if (this.charts.status) this.charts.status.destroy();
                        this.charts.status = new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: ['Belum', 'Sudah', 'Tindak Lanjut', 'Selesai'],
                                datasets: [{
                                    data: [
                                        this.stats.status_counts.belum_dirujuk,
                                        this.stats.status_counts.sudah_dirujuk,
                                        this.stats.status_counts.dalam_tindak_lanjut,
                                        this.stats.status_counts.selesai
                                    ],
                                    backgroundColor: ['#e63946', '#f4a261', '#457b9d', '#2a9d8f'],
                                    borderWidth: 2,
                                    borderColor: '#ffffff',
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { position: 'bottom', labels: { font: { family: 'Outfit' } } }
                                },
                                cutout: '65%'
                            }
                        });
                    }

                    // Exam Types Bar Chart
                    const ctxType = document.getElementById('typeChart');
                    if (ctxType) {
                        if (this.charts.type) this.charts.type.destroy();
                        this.charts.type = new Chart(ctxType, {
                            type: 'bar',
                            data: {
                                labels: ['Gizi', 'Gigi', 'Mata', 'Telinga', 'Umum'],
                                datasets: [{
                                    label: 'Jumlah Rujukan',
                                    data: [
                                        this.stats.type_counts.gizi,
                                        this.stats.type_counts.gigi,
                                        this.stats.type_counts.mata,
                                        this.stats.type_counts.telinga,
                                        this.stats.type_counts.umum
                                    ],
                                    backgroundColor: '#0d9488',
                                    borderRadius: 8,
                                    barThickness: 32,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { borderDash: [4, 4] }, ticks: { font: { family: 'Outfit' } } },
                                    x: { ticks: { font: { family: 'Outfit' } } }
                                }
                            }
                        });
                    }

                    // Monthly Trend Line Chart
                    const ctxTrend = document.getElementById('trendChart');
                    if (ctxTrend) {
                        if (this.charts.trend) this.charts.trend.destroy();
                        
                        const labels = this.stats.trend.map(t => t.month);
                        const data = this.stats.trend.map(t => t.total);

                        this.charts.trend = new Chart(ctxTrend, {
                            type: 'line',
                            data: {
                                labels: labels.length ? labels : ['KOSONG'],
                                datasets: [{
                                    label: 'Tren Rujukan',
                                    data: data.length ? data : [0],
                                    borderColor: '#14b8a6',
                                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#14b8a6',
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, ticks: { font: { family: 'Outfit' } } },
                                    x: { ticks: { font: { family: 'Outfit' } } }
                                }
                            }
                        });
                    }
                },
                openDetail(id) {
                    axios.get(`/api/referrals/${id}`)
                        .then(res => {
                            this.selectedReferral = res.data.data;
                            this.statusForm.status_rujukan = this.selectedReferral.status_rujukan;
                            this.statusForm.catatan = '';
                            this.detailDialog = true;
                        });
                },
                submitStatusUpdate() {
                    axios.put(`/api/referrals/${this.selectedReferral.id}/status`, this.statusForm)
                        .then(res => {
                            // Update list and close dialog
                            this.fetchData(this.pagination.current_page);
                            this.detailDialog = false;
                            
                            // Visual Alert
                            alert('Status rujukan berhasil diperbarui!');
                        })
                        .catch(err => {
                            alert('Gagal memperbarui status rujukan.');
                        });
                },
                exportReferrals(format) {
                    let queryParams = new URLSearchParams({ ...this.filters, format: format }).toString();
                    window.location.href = `/api/referrals/export?` + queryParams;
                },
                doExport(format) {
                    let queryParams = new URLSearchParams({ ...this.exportFilters, format: format }).toString();
                    window.location.href = `/api/referrals/export?` + queryParams;
                },
                // Formatting utils
                formatDate(dateStr) {
                    if (!dateStr) return '-';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                },
                formatDateTime(dateTimeStr) {
                    if (!dateTimeStr) return '-';
                    const date = new Date(dateTimeStr);
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                },
                getJenisBadgeClass(type) {
                    switch (type) {
                        case 'Gizi': return 'bg-teal-50 text-teal-700 border border-teal-100';
                        case 'Gigi': return 'bg-indigo-50 text-indigo-700 border border-indigo-100';
                        case 'Mata': return 'bg-amber-50 text-amber-700 border border-amber-100';
                        case 'Telinga': return 'bg-purple-50 text-purple-700 border border-purple-100';
                        case 'Umum': return 'bg-slate-50 text-slate-700 border border-slate-100';
                        default: return 'bg-slate-50 text-slate-700';
                    }
                },
                getStatusBadgeClass(status) {
                    switch (status) {
                        case 'Belum Dirujuk': return 'bg-rose-50 text-rose-700 border border-rose-100';
                        case 'Sudah Dirujuk': return 'bg-amber-50 text-amber-700 border border-amber-100';
                        case 'Dalam Tindak Lanjut': return 'bg-blue-50 text-blue-700 border border-blue-100';
                        case 'Selesai': return 'bg-teal-50 text-teal-700 border border-teal-100';
                        default: return 'bg-slate-50 text-slate-700';
                    }
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
