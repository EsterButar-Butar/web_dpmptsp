@extends('layouts.operator')

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
                    <h1 class="text-2xl font-bold text-slate-800">Analisis Tipologi Sektor</h1>
                    <p class="text-slate-600 mt-1">Masukkan Data Tipologi Sektor</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="button" onclick="document.getElementById('importModal').style.display='flex'" class="flex items-center gap-2 bg-[#145239] hover:bg-[#0F8A5F] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Unggah Excel
                    </button>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ $editItem ? route('operator.tipologi.update', $editItem['id']) : route('operator.tipologi.hitung') }}" method="POST" class="space-y-6" x-data="{ 
                tingkat_wilayah: '{{ old('tingkat_wilayah', $editItem['tingkat_wilayah'] ?? 'Kabupaten/Kota') }}',
                provinsi: '{{ old('provinsi', $editItem['provinsi'] ?? '') }}',
                get listKabupaten() {
                    return window.daftarWilayah[this.provinsi] || [];
                }
            }">
        @csrf
        @if($editItem)
            @method('PUT')
        @endif
        
        <!-- Top Inputs Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
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
            <!-- Tahun Awal -->
            <div class="space-y-2 col-span-1">
                <label class="op-label">Tahun Awal</label>
                <input type="number" name="tahun_awal" value="{{ old('tahun_awal', $editItem['tahun_awal'] ?? '') }}" min="1900" max="2100" class="op-input" placeholder="Pilih Tahun" required>
            </div>
            <!-- Tahun Akhir -->
            <div class="space-y-2 col-span-1">
                <label class="op-label">Tahun Akhir</label>
                <input type="number" name="tahun_akhir" value="{{ old('tahun_akhir', $editItem['tahun_akhir'] ?? '') }}" min="1900" max="2100" class="op-input" placeholder="Pilih Tahun" required>
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
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 mt-4">
            
            <!-- Data Analisis Card -->
            <div class="bg-white rounded-xl border-2 border-sky-400 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-1" x-text="tingkat_wilayah === 'Kabupaten/Kota' ? 'Data PDRB Sektor Kabupaten/Kota' : 'Data PDRB Sektor Provinsi'">
                    Data PDRB Sektor Analisis
                </h3>
                <p class="text-xs text-slate-500 mb-5">Masukkan data PDRB sektor dan total PDRB wilayah analisis</p>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2" x-data="{ val: '{{ old('pdrb_sektor_analisis_awal', $editItem['pdrb_sektor_analisis_awal'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="op-label">PDRB Sektor Awal (Rp)</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="op-input" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="pdrb_sektor_analisis_awal" :value="val.replace(/\./g, '')">
                    </div>
                    <div class="space-y-2" x-data="{ val: '{{ old('pdrb_sektor_analisis_akhir', $editItem['pdrb_sektor_analisis_akhir'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="op-label">PDRB Sektor Akhir (Rp)</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="op-input" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="pdrb_sektor_analisis_akhir" :value="val.replace(/\./g, '')">
                    </div>
                    <div class="space-y-2" x-data="{ val: '{{ old('total_pdrb_analisis_awal', $editItem['total_pdrb_analisis_awal'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="op-label">Total PDRB Awal (Rp)</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="op-input" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="total_pdrb_analisis_awal" :value="val.replace(/\./g, '')">
                    </div>
                    <div class="space-y-2" x-data="{ val: '{{ old('total_pdrb_analisis_akhir', $editItem['total_pdrb_analisis_akhir'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="op-label">Total PDRB Akhir (Rp)</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="op-input" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="total_pdrb_analisis_akhir" :value="val.replace(/\./g, '')">
                    </div>
                </div>
            </div>

            <!-- Data Pembanding Card -->
            <div class="bg-white rounded-xl border-2 border-sky-400 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-800 mb-1" x-text="tingkat_wilayah === 'Kabupaten/Kota' ? 'Data PDRB Sektor Provinsi (Pembanding)' : 'Data PDB Sektor Nasional (Pembanding)'">
                    Data PDRB Sektor Pembanding
                </h3>
                <p class="text-xs text-slate-500 mb-5">Masukkan data PDRB sektor dan total PDRB pembanding</p>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2" x-data="{ val: '{{ old('pdrb_sektor_pembanding_awal', $editItem['pdrb_sektor_pembanding_awal'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="op-label">PDRB Sektor Awal (Rp)</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="op-input" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="pdrb_sektor_pembanding_awal" :value="val.replace(/\./g, '')">
                    </div>
                    <div class="space-y-2" x-data="{ val: '{{ old('pdrb_sektor_pembanding_akhir', $editItem['pdrb_sektor_pembanding_akhir'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="op-label">PDRB Sektor Akhir (Rp)</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="op-input" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="pdrb_sektor_pembanding_akhir" :value="val.replace(/\./g, '')">
                    </div>
                    <div class="space-y-2" x-data="{ val: '{{ old('total_pdrb_pembanding_awal', $editItem['total_pdrb_pembanding_awal'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="op-label">Total PDRB Awal (Rp)</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="op-input" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="total_pdrb_pembanding_awal" :value="val.replace(/\./g, '')">
                    </div>
                    <div class="space-y-2" x-data="{ val: '{{ old('total_pdrb_pembanding_akhir', $editItem['total_pdrb_pembanding_akhir'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="op-label">Total PDRB Akhir (Rp)</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="op-input" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="total_pdrb_pembanding_akhir" :value="val.replace(/\./g, '')">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="flex items-center gap-2 bg-[#145239] hover:bg-[#0F8A5F] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                {{ $editItem ? 'Perbarui Data' : 'Hitung' }}
            </button>
            @if($editItem)
                <a href="{{ route('operator.tipologi.index') }}" class="px-6 py-2.5 rounded-xl font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">Batal</a>
            @endif
        </div>
            </form>
        </div>
    </div>

    <!-- Table Container -->
    <div class="op-card !mb-0">
        <div class="op-card-header">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-800 mb-2">Hasil Analisis Tipologi Sektor</h2>
                <p class="text-slate-600 mb-6 text-sm">Data Analisis Tipologi Sektor Tersimpan</p>
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table id="tipologiTable" class="w-full text-left border-collapse min-w-[1200px]">
                    <thead class="op-table-header">
                        <tr>
                            <th class="px-4 py-4 whitespace-nowrap">No</th>
                            <th class="px-4 py-4 whitespace-nowrap">Kab/Kota</th>
                            <th class="px-4 py-4 whitespace-nowrap">Provinsi</th>
                            <th class="px-4 py-4 min-w-[200px]">Sektor</th>
                            <th class="px-4 py-4 whitespace-nowrap">PDRB Sektor Analisis Awal (Rp)</th>
                            <th class="px-4 py-4 whitespace-nowrap">PDRB Sektor Analisis Akhir (Rp)</th>
                            <th class="px-4 py-4 whitespace-nowrap">Total PDRB Analisis Awal (Rp)</th>
                            <th class="px-4 py-4 whitespace-nowrap">Total PDRB Analisis Akhir (Rp)</th>
                            <th class="px-4 py-4 whitespace-nowrap">Nilai SS (Selisih Pertumbuhan)</th>
                            <th class="px-4 py-4 whitespace-nowrap">Nilai LQ (Rasio Kontribusi)</th>
                            <th class="px-4 py-4 whitespace-nowrap">Tipologi</th>
                            <th class="px-4 py-4 whitespace-nowrap">Riwayat</th>
                            <th class="px-4 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($tipologiData as $index => $data)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-4">{{ ($tipologiData->currentPage() - 1) * $tipologiData->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-4">{{ $data['kabupaten'] ?? $data['daerah_analisis'] ?? '-' }}</td>
                                <td class="px-4 py-4">{{ $data['provinsi'] ?? $data['daerah_pembanding'] ?? '-' }}</td>
                                <td class="px-4 py-4 min-w-[200px]">{{ $data['sektor'] }}</td>
                                <td class="px-4 py-4">{{ number_format($data['pdrb_sektor_analisis_awal'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4">{{ number_format($data['pdrb_sektor_analisis_akhir'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4">{{ number_format($data['total_pdrb_analisis_awal'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4">{{ number_format($data['total_pdrb_analisis_akhir'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4">{{ number_format($data['nilai_ss'] ?? 0, 4, ',', '.') }}</td>
                                <td class="px-4 py-4">{{ number_format($data['nilai_lq'] ?? 0, 4, ',', '.') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($data['tipologi'] === 'Maju dan Tumbuh Cepat')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">
                                            Kuadran I
                                        </span>
                                    @elseif($data['tipologi'] === 'Potensial / Berkembang Cepat')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">
                                            Kuadran II
                                        </span>
                                    @elseif($data['tipologi'] === 'Berkembang / Maju Tapi Tertekan')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 shadow-sm">
                                            Kuadran III
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 shadow-sm">
                                            Kuadran IV
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-500">
                                    {{ $data['riwayat'] ?? '-' }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('operator.tipologi.index', ['edit' => $data['id']]) }}" class="text-slate-400 hover:text-emerald-600 transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('operator.tipologi.destroy', $data['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors" title="Hapus">
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
                                <td colspan="12" class="px-4 py-8 text-center text-slate-500 font-medium">
                                    Belum ada data perhitungan Tipologi Sektor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-slate-500">
                    Menampilkan {{ $tipologiData->firstItem() ?? 0 }}-{{ $tipologiData->lastItem() ?? 0 }} data dari {{ $tipologiData->total() }} data
                </div>
                <div>
                    {{ $tipologiData->links('pagination::tailwind') }}
                </div>
            </div>

            <!-- Legend / Keterangan -->
            <div class="mt-8 border-t border-slate-200 pt-6 text-sm text-slate-800 w-full mt-2">
                <h4 class="font-bold mb-3 text-base">Keterangan :</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-2 font-medium max-w-3xl">
                    <div class="flex"><span class="w-[85px]">Kuadran I</span> <span class="mr-2">:</span> <span>Sektor cepat maju dan cepat tumbuh</span></div>
                    <div class="flex"><span class="w-[85px]">Kuadran III</span> <span class="mr-2">:</span> <span>Sektor maju tapi tertekan</span></div>
                    <div class="flex"><span class="w-[85px]">Kuadran II</span> <span class="mr-2">:</span> <span>Sektor berkembang cepat/potensial</span></div>
                    <div class="flex"><span class="w-[85px]">Kuadran IV</span> <span class="mr-2">:</span> <span>Sektor relatif tertinggal</span></div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 w-full">
                <button type="button" onclick="exportToExcel()" class="flex items-center gap-2 bg-[#145239] hover:bg-[#0F8A5F] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh (Excel)
                </button>

            </div>
        </div>
    </div>

    <x-import-modal action="{{ route('operator.tipologi.import') }}" />

<script>
    function exportToExcel() {
        var table = document.getElementById("tipologiTable");
        var clone = table.cloneNode(true);
        
        var rows = clone.rows;
        for (var i = 0; i < rows.length; i++) {
            if(rows[i].cells.length > 0) {
                rows[i].deleteCell(-1); // Remove Aksi
            }
        }
        
        var wb = XLSX.utils.table_to_book(clone, {sheet: "Tipologi Sektor"});
        XLSX.writeFile(wb, "Hasil_Analisis_Tipologi_Sektor.xlsx");
    }
</script>
@endsection
