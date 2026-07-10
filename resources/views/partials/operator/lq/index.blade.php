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
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="p-6 md:p-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Analisis LQ</h1>
                    <p class="text-slate-600 mt-1">Masukkan Data LQ per Kabupaten/Kota</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="button" onclick="document.getElementById('importModal').style.display='flex'" class="flex items-center gap-2 bg-[#0056b3] hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Unggah Excel
                    </button>
                </div>
            </div>

            <form action="{{ $editItem ? route('operator.lq.update', $editItem['id']) : route('operator.lq.hitung') }}" method="POST" x-data="{ 
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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6 mb-6">
                    <!-- Tingkat Wilayah -->
                    <div class="space-y-2 md:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700">Tingkat Wilayah</label>
                        <div class="relative">
                            <select name="tingkat_wilayah" x-model="tingkat_wilayah" class="w-full px-4 py-2.5 pr-10 rounded-lg border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-slate-700 text-sm transition-colors cursor-pointer appearance-none" required>
                                <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                                <option value="Provinsi">Provinsi</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                                <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
                            </div>
                        </div>
                    </div>
                    <!-- Sektor -->
                    <div class="space-y-2 md:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700">Sektor</label>
                        <div class="relative">
                            <input list="sektor-list" name="sektor" value="{{ old('sektor', $editItem['sektor'] ?? '') }}" class="w-full px-4 py-2.5 pr-10 rounded-lg border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-slate-700 text-sm transition-colors [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:cursor-pointer" placeholder="Pilih atau ketik Sektor" required>
                            <datalist id="sektor-list">
                                <option value="PERTANIAN, KEHUTANAN, DAN PERIKANAN">
                                <option value="PERTAMBANGAN DAN PENGGALIAN">
                                <option value="INDUSTRI PENGOLAHAN">
                                <option value="PENGADAAN LISTRIK DAN GAS">
                                <option value="PENGADAAN AIR, PENGELOLAAN SAMPAH, LIMBAH DAN DAUR ULANG">
                                <option value="KONSTRUKSI">
                                <option value="PERDAGANGAN BESAR DAN ECERAN">
                                <option value="TRANSPORTASI DAN PERGUDANGAN">
                                <option value="PENYEDIAAN AKOMODASI DAN MAKAN MINUM">
                                <option value="INFORMASI DAN KOMUNIKASI">
                                <option value="JASA KEUANGAN DAN ASURANSI">
                                <option value="REAL ESTAT">
                                <option value="JASA PERUSAHAAN">
                                <option value="ADMINISTRASI PEMERINTAHAN, PERTAHANAN DAN JAMINAN SOSIAL WAJIB">
                                <option value="JASA PENDIDIKAN">
                                <option value="JASA KESEHATAN DAN KEGIATAN SOSIAL">
                                <option value="JASA LAINNYA">
                            </datalist>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4">
                                <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 24 24">
                                    <path d="M7 10l5 5 5-5z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <!-- Tahun -->
                    <div class="space-y-2 col-span-1">
                        <label class="block text-sm font-semibold text-slate-700">Tahun</label>
                        <input type="number" name="tahun" value="{{ old('tahun', $editItem['tahun'] ?? '') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-slate-700 text-sm transition-colors" placeholder="Contoh: 2024" required>
                    </div>
                    <!-- Provinsi -->
                    <div class="space-y-2 col-span-1">
                        <label class="block text-sm font-semibold text-slate-700">Provinsi</label>
                        <div class="relative">
                            <input list="provinsi-list" name="provinsi" x-model="provinsi" autocomplete="off" class="w-full px-4 py-2.5 pr-10 rounded-lg border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-slate-700 text-sm transition-colors [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:cursor-pointer" placeholder="Pilih atau ketik Provinsi" required>
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
                        <label class="block text-sm font-semibold text-slate-700">Kabupaten / Kota</label>
                        <div class="relative">
                            <input list="kabupaten-list" name="kabupaten" value="{{ old('kabupaten', $editItem['kabupaten'] ?? '') }}" :required="tingkat_wilayah === 'Kabupaten/Kota'" autocomplete="off" class="w-full px-4 py-2.5 pr-10 rounded-lg border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-slate-700 text-sm transition-colors [&::-webkit-calendar-picker-indicator]:opacity-0 [&::-webkit-calendar-picker-indicator]:cursor-pointer" placeholder="Pilih atau ketik Kab/Kota">
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

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                    <div class="space-y-2 col-span-1" x-data="{ val: '{{ old('pdrb_sektor_analisis', $editItem['pdrb_sektor_analisis'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="block text-sm font-semibold text-slate-700" x-text="tingkat_wilayah === 'Kabupaten/Kota' ? 'PDRB Sektor Kabupaten' : 'PDRB Sektor Provinsi'">PDRB Sektor Analisis</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-slate-700 text-sm transition-colors" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="pdrb_sektor_analisis" :value="val.replace(/\./g, '')">
                    </div>
                    <div class="space-y-2 col-span-1" x-data="{ val: '{{ old('total_pdrb_analisis', $editItem['total_pdrb_analisis'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="block text-sm font-semibold text-slate-700" x-text="tingkat_wilayah === 'Kabupaten/Kota' ? 'Total PDRB Kabupaten' : 'Total PDRB Provinsi'">Total PDRB Analisis</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-slate-700 text-sm transition-colors" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="total_pdrb_analisis" :value="val.replace(/\./g, '')">
                    </div>
                    <div class="space-y-2 col-span-1" x-data="{ val: '{{ old('pdrb_sektor_pembanding', $editItem['pdrb_sektor_pembanding'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="block text-sm font-semibold text-slate-700" x-text="tingkat_wilayah === 'Kabupaten/Kota' ? 'PDRB Sektor Provinsi' : 'PDB Sektor Nasional'">PDRB Sektor Pembanding</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-slate-700 text-sm transition-colors" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="pdrb_sektor_pembanding" :value="val.replace(/\./g, '')">
                    </div>
                    <div class="space-y-2 col-span-1" x-data="{ val: '{{ old('total_pdrb_pembanding', $editItem['total_pdrb_pembanding'] ?? '') }}', format(v) { let raw = v.toString().replace(/[^0-9]/g, ''); return raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); } }" x-init="val = format(val)">
                        <label class="block text-sm font-semibold text-slate-700" x-text="tingkat_wilayah === 'Kabupaten/Kota' ? 'Total PDRB Provinsi' : 'Total PDB Nasional'">Total PDRB Pembanding</label>
                        <input type="text" x-model="val" @input="val = format($event.target.value)" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-slate-700 text-sm transition-colors" placeholder="Contoh: 50.000" required>
                        <input type="hidden" name="total_pdrb_pembanding" :value="val.replace(/\./g, '')">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="flex items-center gap-2 bg-[#0056b3] hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        {{ $editItem ? 'Perbarui LQ' : 'Hitung LQ' }}
                    </button>
                    @if($editItem)
                        <a href="{{ route('operator.lq.index') }}" class="px-5 py-2.5 rounded-lg font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">Batal</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="p-6 md:p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Hasil Analisis LQ</h2>
                <p class="text-slate-600 mt-1">Data Analisis LQ Tersimpan</p>
            </div>

            <div class="overflow-x-auto border-t border-slate-200">
                <table id="lqTable" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider bg-slate-50/50">
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">NO</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">KAB/KOTA</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">PROVINSI</th>
                            <th class="px-4 py-4 font-semibold min-w-[200px]">SEKTOR</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">TAHUN</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">NILAI LQ</th>
                            <th class="px-4 py-4 font-semibold min-w-[250px]">KETERANGAN</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">KATEGORI</th>
                            <th class="px-4 py-4 font-semibold whitespace-nowrap">RIWAYAT</th>
                            <th class="px-4 py-4 font-semibold text-center whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($lqData as $index => $data)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-4">{{ ($lqData->currentPage() - 1) * $lqData->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-4">{{ $data['kabupaten'] ?? $data['daerah_analisis'] ?? '-' }}</td>
                                <td class="px-4 py-4">{{ $data['provinsi'] ?? $data['daerah_pembanding'] ?? '-' }}</td>
                                <td class="px-4 py-4">{{ $data['sektor'] }}</td>
                                <td class="px-4 py-4 text-center">{{ $data['tahun'] }}</td>
                                <td class="px-4 py-4 text-center">{{ $data['nilai_lq'] }}</td>
                                <td class="px-4 py-4 leading-relaxed text-xs">
                                    {{ $data['keterangan'] }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($data['kategori'] === 'BASIS')
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">
                                            BASIS
                                        </span>
                                    @elseif($data['kategori'] === 'NON-BASIS')
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 shadow-sm">
                                            NON-BASIS
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 shadow-sm">
                                            SEIMBANG
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs whitespace-nowrap">{{ $data['riwayat'] }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('operator.lq.index', ['edit' => $data['id']]) }}" class="text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('operator.lq.destroy', $data['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline-block">
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
                                <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                    Belum ada data perhitungan LQ.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            <div class="mt-6 px-4">
                {{ $lqData->links('pagination::tailwind') }}
            </div>

            <div class="mt-8 border-t border-slate-200 pt-6">
                <button type="button" onclick="exportToExcel()" class="flex items-center gap-2 bg-[#0056b3] hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh Hasil Analisis (Excel)
                </button>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800">Unggah Data Massal</h3>
                <button type="button" onclick="document.getElementById('importModal').style.display='none'" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-slate-600">Pastikan format kolom tabel Excel yang Anda unggah sesuai dengan ketentuan agar sistem dapat memproses datanya.</p>
                <button type="button" onclick="downloadTemplate()" class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    1. Unduh Template Excel
                </button>
                
                <div class="border-t border-slate-200 pt-4 mt-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">2. Pilih File Excel (.xlsx)</label>
                    <input type="file" id="excelFileInput" accept=".xlsx, .xls" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                </div>
                <div id="importStatus" class="text-sm font-medium mt-2 hidden"></div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('importModal').style.display='none'" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Batal</button>
                <button type="button" onclick="processImport()" id="processBtn" class="px-4 py-2 text-sm font-medium bg-[#0056b3] text-white rounded-lg hover:bg-blue-800 transition-colors shadow-sm">Mulai Unggah</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>
    function downloadTemplate() {
        const wsData = [
            ["Kabupaten/Kota", "Provinsi", "Sektor", "Tahun", "PDRB Sektor Analisis", "Total PDRB Analisis", "PDRB Sektor Pembanding", "Total PDRB Pembanding"],
            ["Medan", "Sumatera Utara", "PERTANIAN, KEHUTANAN, DAN PERIKANAN", "2021", "15.000", "50.000", "20.000", "100.000"],
            ["-", "Sumatera Utara", "INDUSTRI PENGOLAHAN", "2021", "8.000", "50.000", "20.000", "100.000"]
        ];
        const ws = XLSX.utils.aoa_to_sheet(wsData);
        ws['!cols'] = [{wch:20}, {wch:20}, {wch:40}, {wch:10}, {wch:20}, {wch:20}, {wch:25}, {wch:25}];
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Template_LQ");
        XLSX.writeFile(wb, "Template_Import_LQ.xlsx");
    }

    async function processImport() {
        const fileInput = document.getElementById('excelFileInput');
        const statusEl = document.getElementById('importStatus');
        const processBtn = document.getElementById('processBtn');
        
        if (!fileInput.files.length) {
            statusEl.textContent = 'Silakan pilih file terlebih dahulu!';
            statusEl.className = 'text-sm font-medium mt-2 text-red-600 block';
            return;
        }

        processBtn.disabled = true;
        processBtn.textContent = 'Memproses...';
        statusEl.textContent = 'Membaca file Excel...';
        statusEl.className = 'text-sm font-medium mt-2 text-blue-600 block';

        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.onload = async function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const firstSheet = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheet];
                
                // Convert to array of objects
                const jsonData = XLSX.utils.sheet_to_json(worksheet);
                
                if (jsonData.length === 0) {
                    throw new Error('Data Excel kosong atau format tidak sesuai.');
                }
                
                statusEl.textContent = 'Menyimpan ' + jsonData.length + ' baris data ke sistem...';

                const response = await fetch("{{ route('operator.lq.import') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(jsonData)
                });

                const result = await response.json();
                
                if (result.success) {
                    statusEl.textContent = 'Berhasil! Memuat ulang halaman...';
                    statusEl.className = 'text-sm font-medium mt-2 text-green-600 block';
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(result.message || 'Terjadi kesalahan saat menyimpan data.');
                }
            } catch (err) {
                statusEl.textContent = 'Gagal: ' + err.message;
                statusEl.className = 'text-sm font-medium mt-2 text-red-600 block';
                processBtn.disabled = false;
                processBtn.textContent = 'Mulai Unggah';
            }
        };

        reader.onerror = function() {
            statusEl.textContent = 'Gagal membaca file dari komputer Anda.';
            statusEl.className = 'text-sm font-medium mt-2 text-red-600 block';
            processBtn.disabled = false;
            processBtn.textContent = 'Mulai Unggah';
        };

        reader.readAsArrayBuffer(file);
    }

    function exportToExcel() {
        var table = document.getElementById("lqTable");
        var clone = table.cloneNode(true);
        
        // Remove 'Aksi' column (last column) before exporting to avoid weird SVG characters
        var rows = clone.rows;
        for (var i = 0; i < rows.length; i++) {
            if(rows[i].cells.length > 0) {
                rows[i].deleteCell(-1);
            }
        }
        
        var wb = XLSX.utils.table_to_book(clone, {sheet: "Analisis LQ"});
        XLSX.writeFile(wb, "Hasil_Analisis_LQ.xlsx");
    }
</script>
@endsection
