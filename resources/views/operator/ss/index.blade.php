{{-- Halaman Indeks Analisis Shift Share (SS) untuk Operator --}}
@extends('partials.layouts.operator')

@section('content')
<div>
    <!-- Alert Messages -->
    @if (session('success'))
        <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Form Container -->
    <div class="op-card">
        <div class="op-card-header">
            <!-- Header & Import Button -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Analisis SS (Shift Share)</h1>
                    <p class="text-slate-600 mt-1">Masukkan Data Analisis SS (Year-on-Year)</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="button" onclick="document.getElementById('syncModal').style.display='flex'" class="flex items-center gap-2 bg-[#D8A62A] hover:bg-[#B88A1A] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Tarik Data Database
                    </button>
                    <button type="button" onclick="document.getElementById('importModal').style.display='flex'" class="flex items-center gap-2 bg-[#145239] hover:bg-[#0F8A5F] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Unggah Excel
                    </button>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ $editItem ? route('operator.ss.update', $editItem['id']) : route('operator.ss.store') }}" method="POST" class="space-y-6" x-data="{ 
                tingkat_wilayah: '{{ old('tingkat_wilayah', $editItem['tingkat_wilayah'] ?? 'Kabupaten/Kota') }}',
                provinsi: '{{ old('provinsi', $editItem['provinsi'] ?? '') }}',
                get listKabupaten() {
                    return window.daftarWilayah[this.provinsi] || [];
                },
                years: @if($editItem)
                [
                    { 
                        tahun: '{{ old('tahun_awal', $editItem['tahun_awal'] ?? '') }}', 
                        pdrb_sektor_analisis: '{{ old('pdrb_sektor_analisis_awal', $editItem['pdrb_sektor_analisis_awal'] ?? '') }}'.split('.')[0], 
                        pdrb_sektor_pembanding: '{{ old('pdrb_sektor_pembanding_awal', $editItem['pdrb_sektor_pembanding_awal'] ?? '') }}'.split('.')[0], 
                        total_pdrb_pembanding: '{{ old('total_pdrb_pembanding_awal', $editItem['total_pdrb_pembanding_awal'] ?? '') }}'.split('.')[0],
                        pdrb_sektor_analisis_fmt: '', pdrb_sektor_pembanding_fmt: '', total_pdrb_pembanding_fmt: ''
                    },
                    { 
                        tahun: '{{ old('tahun_akhir', $editItem['tahun_akhir'] ?? '') }}', 
                        pdrb_sektor_analisis: '{{ old('pdrb_sektor_analisis_akhir', $editItem['pdrb_sektor_analisis_akhir'] ?? '') }}'.split('.')[0], 
                        pdrb_sektor_pembanding: '{{ old('pdrb_sektor_pembanding_akhir', $editItem['pdrb_sektor_pembanding_akhir'] ?? '') }}'.split('.')[0], 
                        total_pdrb_pembanding: '{{ old('total_pdrb_pembanding_akhir', $editItem['total_pdrb_pembanding_akhir'] ?? '') }}'.split('.')[0],
                        pdrb_sektor_analisis_fmt: '', pdrb_sektor_pembanding_fmt: '', total_pdrb_pembanding_fmt: ''
                    }
                ]
                @else
                [
                    { tahun: '2021', pdrb_sektor_analisis: '', pdrb_sektor_pembanding: '', total_pdrb_pembanding: '', pdrb_sektor_analisis_fmt: '', pdrb_sektor_pembanding_fmt: '', total_pdrb_pembanding_fmt: '' },
                    { tahun: '2022', pdrb_sektor_analisis: '', pdrb_sektor_pembanding: '', total_pdrb_pembanding: '', pdrb_sektor_analisis_fmt: '', pdrb_sektor_pembanding_fmt: '', total_pdrb_pembanding_fmt: '' },
                    { tahun: '2023', pdrb_sektor_analisis: '', pdrb_sektor_pembanding: '', total_pdrb_pembanding: '', pdrb_sektor_analisis_fmt: '', pdrb_sektor_pembanding_fmt: '', total_pdrb_pembanding_fmt: '' },
                    { tahun: '2024', pdrb_sektor_analisis: '', pdrb_sektor_pembanding: '', total_pdrb_pembanding: '', pdrb_sektor_analisis_fmt: '', pdrb_sektor_pembanding_fmt: '', total_pdrb_pembanding_fmt: '' },
                    { tahun: '2025', pdrb_sektor_analisis: '', pdrb_sektor_pembanding: '', total_pdrb_pembanding: '', pdrb_sektor_analisis_fmt: '', pdrb_sektor_pembanding_fmt: '', total_pdrb_pembanding_fmt: '' }
                ]
                @endif,
                format(v) { 
                    if (v === undefined || v === null || v === '') return '';
                    let raw = v.toString().replace(/[^0-9]/g, ''); 
                    return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); 
                },
                addYear() {
                    this.years.push({ tahun: '', pdrb_sektor_analisis: '', pdrb_sektor_pembanding: '', total_pdrb_pembanding: '', pdrb_sektor_analisis_fmt: '', pdrb_sektor_pembanding_fmt: '', total_pdrb_pembanding_fmt: '' });
                },
                removeYear(index) {
                    if (this.years.length > 2) {
                        this.years.splice(index, 1);
                    }
                }
            }">
        @csrf
        @if($editItem)
            @method('PUT')
        @endif
        
        <!-- Top Inputs Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 mb-6">
            <div class="space-y-2 col-span-1">
                <label class="op-label">Tingkat Wilayah</label>
                <div class="relative">
                    <select name="tingkat_wilayah" x-model="tingkat_wilayah" class="op-input op-input-icon op-select" required>
                        <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        <option value="Provinsi">Provinsi</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                        <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
                    </div>
                </div>
            </div>

            <!-- Sektor -->
            <div class="space-y-2 col-span-1">
                <label class="op-label">Sektor</label>
                <div class="relative">
                    <input list="sektor-list" name="sektor" value="{{ old('sektor', $editItem['sektor'] ?? '') }}" class="op-input op-input-icon op-datalist" placeholder="Pilih Sektor" required>
                    <datalist id="sektor-list">
                        <option value="PERTANIAN, KEHUTANAN, DAN PERIKANAN">
                        <option value="PERTAMBANGAN DAN PENGGALIAN">
                        <option value="INDUSTRI PENGOLAHAN">
                        <option value="PENGADAAN LISTRIK DAN GAS">
                        <option value="KONSTRUKSI">
                        <option value="PERDAGANGAN BESAR DAN ECERAN">
                        <option value="TRANSPORTASI DAN PERGUDANGAN">
                        <option value="INFORMASI DAN KOMUNIKASI">
                        <option value="JASA KEUANGAN DAN ASURANSI">
                    </datalist>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                        <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Provinsi -->
            <div class="space-y-2 col-span-1">
                <label class="op-label">Provinsi</label>
                <div class="relative">
                    <input list="provinsi-list" name="provinsi" x-model="provinsi" autocomplete="off" class="op-input op-input-icon op-datalist" placeholder="Pilih atau ketik Provinsi" required>
                    <datalist id="provinsi-list">
                        <template x-for="prov in Object.keys(window.daftarWilayah)" :key="prov">
                            <option :value="prov"></option>
                        </template>
                    </datalist>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                        <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Kabupaten / Kota -->
            <div class="space-y-2 col-span-1" x-show="tingkat_wilayah === 'Kabupaten/Kota'">
                <label class="op-label">Kabupaten / Kota</label>
                <div class="relative">
                    <input list="kabupaten-list" name="kabupaten" value="{{ old('kabupaten', $editItem['kabupaten'] ?? '') }}" :required="tingkat_wilayah === 'Kabupaten/Kota'" autocomplete="off" class="op-input op-input-icon op-datalist" placeholder="Pilih atau ketik Kab/Kota">
                    <datalist id="kabupaten-list">
                        <template x-for="kab in listKabupaten" :key="kab">
                            <option :value="kab"></option>
                        </template>
                    </datalist>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                        <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Cards Row -->
        <template x-for="(year, index) in years" :key="index">
            <div class="mb-4 border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
                    <div class="flex items-center gap-3">
                        <span class="bg-[#145239] text-white px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap" x-text="'Data Tahun ' + (index + 1)"></span>
                        <div class="relative">
                            <select name="tahun[]" x-model="year.tahun" class="op-input op-input-icon op-select !w-32 !py-1 !text-sm" required>
                                <option value="" disabled>Pilih Tahun</option>
                                @for($i = 2021; $i <= 2045; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                <svg class="w-3 h-3 text-slate-600 fill-current" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="removeYear(index)" x-show="years.length > 2" class="text-red-500 hover:text-red-700 p-1.5 bg-red-100 rounded-md transition-colors" title="Hapus Tahun">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Data Analisis -->
                    <div class="bg-white rounded-lg border border-sky-200 p-4 shadow-sm">
                        <h4 class="text-sm font-bold text-slate-800 mb-3" x-text="tingkat_wilayah === 'Kabupaten/Kota' ? 'PDRB Analisis (Kab/Kota)' : 'PDRB Analisis (Provinsi)'"></h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600">PDRB Sektor (Rp)</label>
                                <input type="text" x-model="year.pdrb_sektor_analisis_fmt" @input="year.pdrb_sektor_analisis_fmt = format($event.target.value); year.pdrb_sektor_analisis = year.pdrb_sektor_analisis_fmt.replace(/\./g, '')" x-init="year.pdrb_sektor_analisis_fmt = format(year.pdrb_sektor_analisis)" class="op-input !py-1.5 !text-sm" placeholder="1.000.000" required>
                                <input type="hidden" name="pdrb_sektor_analisis[]" :value="year.pdrb_sektor_analisis">
                            </div>
                        </div>
                    </div>
                    <!-- Data Pembanding -->
                    <div class="bg-white rounded-lg border border-sky-200 p-4 shadow-sm">
                        <h4 class="text-sm font-bold text-slate-800 mb-3" x-text="tingkat_wilayah === 'Kabupaten/Kota' ? 'PDRB Pembanding (Provinsi)' : 'PDB Pembanding (Nasional)'"></h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600">PDRB Sektor (Rp)</label>
                                <input type="text" x-model="year.pdrb_sektor_pembanding_fmt" @input="year.pdrb_sektor_pembanding_fmt = format($event.target.value); year.pdrb_sektor_pembanding = year.pdrb_sektor_pembanding_fmt.replace(/\./g, '')" x-init="year.pdrb_sektor_pembanding_fmt = format(year.pdrb_sektor_pembanding)" class="op-input !py-1.5 !text-sm" placeholder="1.000.000" required>
                                <input type="hidden" name="pdrb_sektor_pembanding[]" :value="year.pdrb_sektor_pembanding">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600">Total PDRB/PDB (Rp)</label>
                                <input type="text" x-model="year.total_pdrb_pembanding_fmt" @input="year.total_pdrb_pembanding_fmt = format($event.target.value); year.total_pdrb_pembanding = year.total_pdrb_pembanding_fmt.replace(/\./g, '')" x-init="year.total_pdrb_pembanding_fmt = format(year.total_pdrb_pembanding)" class="op-input !py-1.5 !text-sm" placeholder="1.000.000" required>
                                <input type="hidden" name="total_pdrb_pembanding[]" :value="year.total_pdrb_pembanding">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        
        <div class="flex justify-center mb-6">
            <button type="button" @click="addYear()" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-full text-sm font-bold transition-colors border border-slate-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Tahun
            </button>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="flex items-center gap-2 bg-[#145239] hover:bg-[#0F8A5F] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                {{ $editItem ? 'Perbarui Data' : 'Hitung' }}
            </button>
            @if($editItem)
                <a href="{{ route('operator.ss.index') }}" class="px-6 py-2.5 rounded-xl font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">Batal</a>
            @endif
        </div>
            </form>
        </div>
    </div>

    <!-- Table Container -->
    <div class="op-card !mb-0">
        <div class="op-card-header">
            <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 mb-2">Hasil Analisis SS</h2>
                    <p class="text-slate-600 text-sm">Data Analisis SS (Shift Share) Tersimpan</p>
                </div>
                <form action="{{ route('operator.ss.index') }}" method="GET" class="relative w-full md:w-72">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Daerah atau Sektor..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:border-[#D8A62A] focus:ring-1 focus:ring-[#D8A62A] outline-none transition-all shadow-sm">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table id="ssTable" class="w-full text-left border-collapse min-w-[1200px]">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider w-12 text-center">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer" onclick="toggleSelectAll(this)">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider w-16">No</th>
                            <th class="px-4 py-4 whitespace-nowrap">Daerah Analisis</th>
                            <th class="px-4 py-4 whitespace-nowrap">Daerah Pembanding</th>
                            <th class="px-4 py-4 whitespace-nowrap">Sektor</th>
                            <th class="px-4 py-4 whitespace-nowrap text-center">Tahun</th>
                            <th class="px-4 py-4 whitespace-nowrap">Rij</th>
                            <th class="px-4 py-4 whitespace-nowrap">Rin</th>
                            <th class="px-4 py-4 whitespace-nowrap">Rn</th>
                            <th class="px-4 py-4 whitespace-nowrap">Nij</th>
                            <th class="px-4 py-4 whitespace-nowrap">Mij</th>
                            <th class="px-4 py-4 whitespace-nowrap">Cij</th>
                            <th class="px-4 py-4 whitespace-nowrap">Dij</th>
                            <th class="px-4 py-4 whitespace-nowrap">Status Pertumbuhan</th>
                            <th class="px-4 py-4 whitespace-nowrap">Status Daya Saing</th>
                            <th class="px-4 py-4 whitespace-nowrap">Riwayat</th>
                            <th class="px-4 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($ssData as $index => $data)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <input type="checkbox" class="row-checkbox rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer" value="{{ $data['id'] }}">
                                </td>
                                <td class="px-4 py-4">{{ ($ssData->currentPage() - 1) * $ssData->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-4">{{ $data['kabupaten'] ?? $data['daerah_analisis'] ?? '-' }}</td>
                                <td class="px-4 py-4">{{ $data['provinsi'] ?? $data['daerah_pembanding'] ?? '-' }}</td>
                                <td class="px-4 py-4 min-w-[200px]">{{ $data['sektor'] }}</td>
                                <td class="px-4 py-4 text-center text-slate-500 whitespace-nowrap">{{ $data['tahun_awal'] ?? '' }} - {{ $data['tahun_akhir'] ?? '' }}</td>
                                <td class="px-4 py-4">{{ $data['rij'] }}</td>
                                <td class="px-4 py-4">{{ $data['rin'] }}</td>
                                <td class="px-4 py-4">{{ $data['rn'] }}</td>
                                <td class="px-4 py-4">{{ number_format($data['nij'], 2, ',', '.') }}</td>
                                <td class="px-4 py-4">{{ number_format($data['mij'], 2, ',', '.') }}</td>
                                <td class="px-4 py-4">{{ number_format($data['cij'], 2, ',', '.') }}</td>
                                <td class="px-4 py-4 font-bold">{{ number_format($data['dij'], 2, ',', '.') }}</td>
                                <td class="px-4 py-4">
                                    @if($data['status_pertumbuhan'] === 'Pertumbuhan Cepat')
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm whitespace-nowrap">
                                            Cepat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200 shadow-sm whitespace-nowrap">
                                            Lambat
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if($data['status_daya_saing'] === 'Daya Saing Baik')
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm whitespace-nowrap">
                                            Baik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200 shadow-sm whitespace-nowrap">
                                            Kurang
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs whitespace-nowrap text-slate-500">{{ $data['riwayat'] }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('operator.ss.index', ['edit' => $data['id']]) }}" class="p-1.5 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Edit">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('operator.ss.destroy', $data['id']) }}" method="POST" onsubmit="return confirmDelete(event, this);" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 bg-red-50 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="px-4 py-8 text-center text-slate-500 font-medium">
                                    Belum ada data perhitungan Analisis SS (Shift Share).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-slate-500">
                    Menampilkan {{ $ssData->firstItem() ?? 0 }}-{{ $ssData->lastItem() ?? 0 }} data dari {{ $ssData->total() }} data
                </div>
                <div>
                    {{ $ssData->links('pagination::tailwind') }}
                </div>
            </div>

            <!-- Legend / Keterangan -->
            <div class="mt-8 border-t border-slate-200 pt-6 text-sm text-slate-800 w-full mt-2">
                <h4 class="font-bold mb-3 text-base">Keterangan :</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 font-medium max-w-4xl">
                    <div class="flex"><span class="w-[30px] font-semibold text-slate-800">Rij</span> <span class="mr-2">:</span> <span>Pertumbuhan PDRB sektor i di daerah analisis (kab/kota atau provinsi)</span></div>
                    <div class="flex"><span class="w-[30px] font-semibold text-slate-800">Rin</span> <span class="mr-2">:</span> <span>Pertumbuhan PDRB sektor i di daerah pembanding (provinsi atau nasional)</span></div>
                    <div class="flex"><span class="w-[30px] font-semibold text-slate-800">Rn</span> <span class="mr-2">:</span> <span>Pertumbuhan PDRB total daerah pembanding</span></div>
                    <div class="flex"><span class="w-[30px] font-semibold text-slate-800">Nij</span> <span class="mr-2">:</span> <span>Komponen pertumbuhan nasional</span></div>
                    <div class="flex"><span class="w-[30px] font-semibold text-slate-800">Mij</span> <span class="mr-2">:</span> <span>Komponen pertumbuhan proporsional</span></div>
                    <div class="flex"><span class="w-[30px] font-semibold text-slate-800">Cij</span> <span class="mr-2">:</span> <span>Komponen keunggulan kompetitif</span></div>
                    <div class="flex"><span class="w-[30px] font-semibold text-slate-800">Dij</span> <span class="mr-2">:</span> <span>Perubahan total (Nij + Mij + Cij)</span></div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 w-full">
                <form id="bulkDeleteForm" action="{{ route('operator.ss.bulkDestroy') }}" method="POST" onsubmit="return confirmBulkDelete(event, this);" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="ids" id="selectedIds">
                    <button type="submit" id="bulkDeleteBtn" class="hidden w-full sm:w-auto flex items-center justify-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Terpilih (<span id="bulkDeleteCount">0</span>)
                    </button>
                </form>

                <a href="{{ route('operator.ss.excel', ['search' => request('search')]) }}" class="flex items-center justify-center gap-2 bg-[#145239] hover:bg-[#0F8A5F] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm w-full sm:w-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh (Excel)
                </a>

                <a href="{{ route('operator.ss.pdf', ['search' => request('search')]) }}" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm w-full sm:w-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Unduh PDF
                </a>

                <form action="{{ route('operator.ss.empty') }}" method="POST" onsubmit="return confirmDeleteAll(event, this);" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Semua Data
                    </button>
                </form>
            </div>
        </div>
    </div>

    <x-import-modal action="{{ route('operator.ss.import') }}" type="ss" />

    <!-- Sync Modal -->
    <div id="syncModal" class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 backdrop-blur-sm px-4 py-8" style="display: none;" x-data="{
        sync_tingkat_wilayah: 'Kabupaten/Kota',
        sync_provinsi: '',
        get syncListKabupaten() {
            return window.daftarWilayah[this.sync_provinsi] || [];
        }
    }">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden relative">
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800">Tarik Data dari Database</h3>
                <button type="button" onclick="document.getElementById('syncModal').style.display='none'" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="space-y-2">
                    <label class="op-label">Tingkat Wilayah</label>
                    <div class="relative">
                        <select id="sync_tingkat_wilayah" x-model="sync_tingkat_wilayah" class="op-input op-input-icon op-select">
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                            <option value="Provinsi">Provinsi</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                            <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="op-label">Provinsi</label>
                    <div class="relative">
                        <select id="sync_provinsi" x-model="sync_provinsi" class="op-input op-input-icon op-select" required>
                            <option value="" disabled selected>Pilih Provinsi</option>
                            <template x-for="prov in Object.keys(window.daftarWilayah)" :key="prov">
                                <option :value="prov" x-text="prov"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                            <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2" x-show="sync_tingkat_wilayah === 'Kabupaten/Kota'">
                    <label class="op-label">Kabupaten / Kota</label>
                    <div class="relative">
                        <select id="sync_kabupaten" class="op-input op-input-icon op-select" :required="sync_tingkat_wilayah === 'Kabupaten/Kota'">
                            <option value="" disabled selected>Pilih Kabupaten/Kota</option>
                            <template x-for="kab in syncListKabupaten" :key="kab">
                                <option :value="kab" x-text="kab"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                            <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="op-label">Sektor</label>
                    <div class="relative">
                        <input list="sync-sektor-list" id="sync_sektor" class="op-input op-input-icon op-datalist" placeholder="Pilih Sektor" required>
                        <datalist id="sync-sektor-list">
                            <option value="PERTANIAN, KEHUTANAN, DAN PERIKANAN">
                            <option value="PERTAMBANGAN DAN PENGGALIAN">
                            <option value="INDUSTRI PENGOLAHAN">
                            <option value="PENGADAAN LISTRIK DAN GAS">
                            <option value="KONSTRUKSI">
                            <option value="PERDAGANGAN BESAR DAN ECERAN">
                            <option value="TRANSPORTASI DAN PERGUDANGAN">
                            <option value="INFORMASI DAN KOMUNIKASI">
                            <option value="JASA KEUANGAN DAN ASURANSI">
                        </datalist>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                            <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="op-label">Tahun Awal</label>
                        <div class="relative">
                            <select id="sync_tahun_awal" class="op-input op-input-icon op-select" required>
                                <option value="" disabled selected>Pilih Tahun</option>
                                @for($i = 2021; $i <= 2045; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                                <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="op-label">Tahun Akhir</label>
                        <div class="relative">
                            <select id="sync_tahun_akhir" class="op-input op-input-icon op-select" required>
                                <option value="" disabled selected>Pilih Tahun</option>
                                @for($i = 2021; $i <= 2045; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                                <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="syncStatus" class="text-sm font-medium mt-2 hidden"></div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('syncModal').style.display='none'" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Batal</button>
                <button type="button" onclick="processSync()" id="processSyncBtn" class="flex items-center gap-2 bg-[#D8A62A] hover:bg-[#B88A1A] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">Mulai Tarik Data</button>
            </div>
        </div>
    </div>


<script>
    async function processSync() {
        const tingkat = document.getElementById('sync_tingkat_wilayah').value;
        const provinsi = document.getElementById('sync_provinsi').value;
        const kabupaten = document.getElementById('sync_kabupaten').value;
        const sektor = document.getElementById('sync_sektor').value;
        const tahunAwal = document.getElementById('sync_tahun_awal').value;
        const tahunAkhir = document.getElementById('sync_tahun_akhir').value;
        const statusEl = document.getElementById('syncStatus');
        const processBtn = document.getElementById('processSyncBtn');

        if (!provinsi || !sektor || !tahunAwal || !tahunAkhir || (tingkat === 'Kabupaten/Kota' && !kabupaten)) {
            statusEl.textContent = 'Harap lengkapi semua isian terlebih dahulu!';
            statusEl.className = 'text-sm font-medium mt-2 text-red-600 block';
            return;
        }

        processBtn.disabled = true;
        processBtn.textContent = 'Mencari Data...';
        statusEl.textContent = 'Mencari data PDRB di database...';
        statusEl.className = 'text-sm font-medium mt-2 text-emerald-600 block';

        try {
            const response = await fetch("{{ route('operator.ss.sync') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    tingkat_wilayah: tingkat,
                    provinsi: provinsi,
                    kabupaten: kabupaten,
                    sektor: sektor,
                    tahun_awal: tahunAwal,
                    tahun_akhir: tahunAkhir
                })
            });

            const result = await response.json();
            
            if (result.success) {
                statusEl.textContent = 'Data ditemukan! Mengisi form otomatis...';
                
                // Get the main Alpine data component on the form
                const formEl = document.querySelector('form');
                
                // We dispatch events to update non-alpine selects
                const tingkatSelect = document.querySelector('select[name="tingkat_wilayah"]');
                tingkatSelect.value = tingkat;
                tingkatSelect.dispatchEvent(new Event('change', { bubbles: true }));
                
                setTimeout(() => {
                    const provinsiInput = document.querySelector('input[name="provinsi"]');
                    provinsiInput.value = provinsi;
                    provinsiInput.dispatchEvent(new Event('input', { bubbles: true }));
                    
                    if (tingkat === 'Kabupaten/Kota') {
                        const kabInput = document.querySelector('input[name="kabupaten"]');
                        if (kabInput) kabInput.value = kabupaten;
                    }
                    
                    const sektorInput = document.querySelector('input[name="sektor"]');
                    if (sektorInput) sektorInput.value = sektor;
                    
                    // Access Alpine Component Data safely and set years array
                    if (formEl.__x) {
                        const component = formEl.__x.$data;
                        const newYears = result.data.map(y => ({
                            tahun: y.tahun,
                            pdrb_sektor_analisis: y.pdrb_sektor_analisis.toString().split('.')[0],
                            pdrb_sektor_pembanding: y.pdrb_sektor_pembanding.toString().split('.')[0],
                            total_pdrb_pembanding: y.total_pdrb_pembanding.toString().split('.')[0],
                            pdrb_sektor_analisis_fmt: component.format(y.pdrb_sektor_analisis.toString().split('.')[0]),
                            pdrb_sektor_pembanding_fmt: component.format(y.pdrb_sektor_pembanding.toString().split('.')[0]),
                            total_pdrb_pembanding_fmt: component.format(y.total_pdrb_pembanding.toString().split('.')[0])
                        }));
                        
                        component.years = newYears;
                    }

                    statusEl.textContent = 'Selesai!';
                    setTimeout(() => {
                        document.getElementById('syncModal').style.display='none';
                        statusEl.className = 'text-sm font-medium mt-2 hidden';
                        processBtn.disabled = false;
                        processBtn.textContent = 'Mulai Tarik Data';
                    }, 1000);
                }, 100);
            } else {
                throw new Error(result.message || 'Gagal mencari data.');
            }
        } catch (err) {
            statusEl.textContent = 'Gagal: ' + err.message;
            statusEl.className = 'text-sm font-medium mt-2 text-red-600 block';
            processBtn.disabled = false;
            processBtn.textContent = 'Mulai Tarik Data';
        }
    }
    
    function exportToExcel() {
        var table = document.getElementById("ssTable");
        var clone = table.cloneNode(true);
        
        var rows = clone.rows;
        for (var i = 0; i < rows.length; i++) {
            if(rows[i].cells.length > 0) {
                rows[i].deleteCell(-1); // Delete Aksi column
                rows[i].deleteCell(0);  // Delete Checkbox column
            }
        }
        
        var wb = XLSX.utils.table_to_book(clone, {sheet: "Analisis SS"});
        XLSX.writeFile(wb, "Hasil_Analisis_SS.xlsx");
    }
</script>
@endsection
