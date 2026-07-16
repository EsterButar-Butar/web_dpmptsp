import json
import pandas as pd
import numpy as np

try:
    df = pd.read_excel('test_import_sektoral.xlsx')
    
    # Standardize columns to match client-side cleanHeaders mapping
    # Clean header maps:
    # 'provinsi' -> nama_provinsi
    # 'kabupaten' -> nama_kabupaten
    # 'sektor' -> nama_sektor
    # 'tahun' -> tahun
    # first value -> nilai (index 7) -> PDRB Sektor Pembanding
    # second value -> nilai.1 (index 8) -> PDRB Sektor Analisis
    
    # In pandas:
    # df.columns are ['provinsi_id', 'nama_provinsi', 'kab_id', 'nama_kabupaten', 'id_sektor', 'nama_sektor', 'tahun', 'nilai', 'nilai.1']
    # Let's verify columns:
    print("Columns:", list(df.columns))
    
    # Let's clean headers to match what Javascript does:
    # JavaScript logic maps columns based on name:
    # provIdx: 'nama_provinsi'
    # kabIdx: 'nama_kabupaten'
    # sektorIdx: 'nama_sektor'
    # tahunIdx: 'tahun'
    # nilaiProvIdx: index 7 ('nilai' -> first value column)
    # nilaiKabIdx: index 8 ('nilai.1' -> second value column)
    
    # Let's group and combine:
    years = sorted(df['tahun'].unique())
    print("Years:", years)
    tahunAwal = int(years[0])
    tahunAkhir = int(years[-1])
    
    # Calculate totals
    # totalKab = total of nilaiKab (second value column) per kabupaten per year
    # totalProv = total of nilaiProv (first value column) per provinsi per year
    
    totalKab = df.groupby(['nama_kabupaten', 'tahun'])['nilai.1'].sum().to_dict()
    totalProv = df.groupby(['nama_provinsi', 'tahun'])['nilai'].sum().to_dict()
    
    combined = {}
    for idx, row in df.iterrows():
        prov = str(row['nama_provinsi']).strip()
        kab = str(row['nama_kabupaten']).strip()
        sektor = str(row['nama_sektor']).strip()
        tahun = int(row['tahun'])
        
        if tahun not in (tahunAwal, tahunAkhir):
            continue
            
        group_key = f"{prov}_{kab}_{sektor}"
        
        if group_key not in combined:
            combined[group_key] = {
                'Provinsi': prov,
                'Kabupaten/Kota': kab,
                'Sektor': sektor,
                'Tahun Awal': tahunAwal,
                'Tahun Akhir': tahunAkhir,
                'PDRB Sektor Analisis Awal': 0.0,
                'PDRB Sektor Analisis Akhir': 0.0,
                'Total PDRB Analisis Awal': float(totalKab.get((kab, tahunAwal), 0)),
                'Total PDRB Analisis Akhir': float(totalKab.get((kab, tahunAkhir), 0)),
                'PDRB Sektor Pembanding Awal': 0.0,
                'PDRB Sektor Pembanding Akhir': 0.0,
                'Total PDRB Pembanding Awal': float(totalProv.get((prov, tahunAwal), 0)),
                'Total PDRB Pembanding Akhir': float(totalProv.get((prov, tahunAkhir), 0))
            }
            
        nilaiProv = float(row['nilai'])
        nilaiKab = float(row['nilai.1'])
        
        if tahun == tahunAwal:
            combined[group_key]['PDRB Sektor Analisis Awal'] = nilaiKab
            combined[group_key]['PDRB Sektor Pembanding Awal'] = nilaiProv
        elif tahun == tahunAkhir:
            combined[group_key]['PDRB Sektor Analisis Akhir'] = nilaiKab
            combined[group_key]['PDRB Sektor Pembanding Akhir'] = nilaiProv
            
    payload = list(combined.values())
    print("Payload rows:", len(payload))
    
    with open('scratch/parsed_payload.json', 'w') as f:
        json.dump(payload, f, indent=2)
    print("Saved payload to scratch/parsed_payload.json")

except Exception as e:
    print("Error:", e)
