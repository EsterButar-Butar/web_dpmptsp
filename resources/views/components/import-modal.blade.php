@props(['action'])

<div id="importModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden relative">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Unggah Data Analisis</h3>
            <button type="button" onclick="document.getElementById('importModal').style.display='none'" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-sm text-slate-600">Pastikan format kolom tabel Excel yang Anda unggah sesuai dengan ketentuan agar sistem dapat memproses datanya.</p>
            <button type="button" onclick="downloadMasterTemplate()" class="op-btn-outline">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                1. Unduh Template Master
            </button>
            
            <div class="border-t border-slate-200 pt-4 mt-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">2. Pilih File Excel (.xlsx)</label>
                <input type="file" id="excelFileInput" accept=".xlsx, .xls" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
            </div>
            <div id="importStatus" class="text-sm font-medium mt-2 hidden"></div>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('importModal').style.display='none'" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Batal</button>
            <button type="button" onclick="processImport()" id="processBtn" class="flex items-center gap-2 bg-[#145239] hover:bg-[#0F8A5F] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">Mulai Unggah</button>
        </div>
    </div>
</div>

<!-- Load SheetJS only if it hasn't been loaded -->
@once
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>
    function downloadMasterTemplate() {
        const wsData = [
            [
                "Provinsi", "Kabupaten/Kota", "Sektor", "Tahun Awal", "Tahun Akhir", 
                "PDRB Sektor Analisis Awal", "PDRB Sektor Analisis Akhir", 
                "Total PDRB Analisis Awal", "Total PDRB Analisis Akhir", 
                "PDRB Sektor Pembanding Awal", "PDRB Sektor Pembanding Akhir", 
                "Total PDRB Pembanding Awal", "Total PDRB Pembanding Akhir"
            ],
            [
                "Sumatera Utara", "Medan", "PERTANIAN, KEHUTANAN, DAN PERIKANAN", "2021", "2022", 
                "15000", "16000", "100000", "110000", "50000", "52000", "200000", "210000"
            ],
            [
                "Sumatera Utara", "-", "INDUSTRI PENGOLAHAN", "2021", "2022", 
                "25000", "27000", "300000", "320000", "80000", "85000", "400000", "420000"
            ]
        ];
        const ws = XLSX.utils.aoa_to_sheet(wsData);
        // Set column widths
        ws['!cols'] = Array(13).fill({wch: 25});
        
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Master_Template");
        XLSX.writeFile(wb, "Template_Master_Analisis.xlsx");
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
        statusEl.className = 'text-sm font-medium mt-2 text-emerald-600 block';

        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.onload = async function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const firstSheet = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheet];
                
                const jsonData = XLSX.utils.sheet_to_json(worksheet);
                
                if (jsonData.length === 0) {
                    throw new Error('Data Excel kosong atau format tidak sesuai.');
                }
                
                statusEl.textContent = 'Menyimpan ' + jsonData.length + ' baris data ke sistem...';

                const response = await fetch("{{ $action }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(jsonData)
                });

                const result = await response.json();
                
                if (result.success) {
                    statusEl.textContent = result.message || 'Berhasil! Memuat ulang halaman...';
                    statusEl.className = 'text-sm font-medium mt-2 text-emerald-600 block';
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
</script>
@endonce
