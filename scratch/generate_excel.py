import pandas as pd
import numpy as np

# Define regions in Sumatera Utara
kabupaten_list = [
    {"id": "12.01", "name": "KAB. TAPANULI TENGAH"},
    {"id": "12.02", "name": "KAB. TAPANULI UTARA"},
    {"id": "12.03", "name": "KAB. TAPANULI SELATAN"},
    {"id": "12.04", "name": "KAB. NIAS"},
    {"id": "12.05", "name": "KAB. LANGKAT"}
]

# Define sectors from database
sectors = [
    {"id": 1, "name": "PERTAMBANGAN DAN PENGGALIAN"},
    {"id": 2, "name": "PERTANIAN, KEHUTANAN, DAN PERIKANAN"},
    {"id": 3, "name": "INDUSTRI PENGOLAHAN"},
    {"id": 4, "name": "PENGADAAN AIR, PENGELOLAAN SAMPAH, LIMBAH DAN DAUR ULANG"},
    {"id": 5, "name": "PENGADAAN LISTRIK DAN GAS"},
    {"id": 6, "name": "KOSNTRUKSI"},
    {"id": 7, "name": "PERDAGANGAN BESAR DAN ECERAN"},
    {"id": 8, "name": "TRANSPORTASI DAN PERGUDANGAN"},
    {"id": 9, "name": "PENYEDIAAN AKOMODASI DAN MAKAN MINUM"},
    {"id": 10, "name": "INFORMASI DAN KOMUNIKASI"},
    {"id": 11, "name": "JASA KEUANGAN DAN ASURANSI"},
    {"id": 12, "name": "REAL ESTAT"},
    {"id": 13, "name": "JASA PERUSAHAAN"},
    {"id": 14, "name": "ADMINISTRASI PEMERINTAHAN, PERTAHANAN DAN JAMINAN SOSIAL WAJIB"},
    {"id": 15, "name": "JASA PENDIDIKAN"},
    {"id": 16, "name": "JASA KESEHATAN DAN KEGIATAN SOSIAL"},
    {"id": 17, "name": "JASA LAINNYA"}
]

years = [2021, 2022]

data = []

# Base values for sectors
# Sector base PDRB Provinsi (in billions)
prov_base = {s["id"]: np.random.uniform(5000, 20000) for s in sectors}
# Sector base PDRB Kabupaten (in billions)
kab_base = {
    (kab["id"], s["id"]): np.random.uniform(100, 1000)
    for kab in kabupaten_list
    for s in sectors
}

for yr in years:
    # Year multiplier (e.g. 5% growth in 2022)
    mult = 1.05 if yr == 2022 else 1.00
    
    for kab in kabupaten_list:
        for s in sectors:
            # PDRB Sektor Pembanding (Provinsi)
            val_prov = prov_base[s["id"]] * mult
            # PDRB Sektor Analisis (Kabupaten)
            val_kab = kab_base[(kab["id"], s["id"])] * mult
            
            data.append({
                "provinsi_id": 12.0,
                "nama_provinsi": "SUMATERA UTARA",
                "kab_id": kab["id"],
                "nama_kabupaten": kab["name"],
                "id_sektor": s["id"],
                "nama_sektor": s["name"],
                "tahun": yr,
                "nilai": val_prov,  # Will be 'nilai'
                "nilai.1": val_kab  # Will be 'nilai' in output Excel
            })

df = pd.DataFrame(data)

# Create sheet with duplicate 'nilai' column headers
with pd.ExcelWriter('d:\\Magang\\web_dpmptsp\\test_import_sektoral.xlsx', engine='openpyxl') as writer:
    df.to_excel(writer, index=False, sheet_name='Sheet1')
    
# Re-open with openpyxl to rename the column 'nilai.1' to 'nilai' so both are exactly 'nilai'
import openpyxl
wb = openpyxl.load_workbook('d:\\Magang\\web_dpmptsp\\test_import_sektoral.xlsx')
ws = wb.active
ws.cell(row=1, column=9).value = 'nilai'
wb.save('d:\\Magang\\web_dpmptsp\\test_import_sektoral.xlsx')

print("Successfully generated complete test_import_sektoral.xlsx!")
