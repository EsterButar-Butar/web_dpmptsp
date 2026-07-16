import json
import pandas as pd
import numpy as np

try:
    df = pd.read_excel('test_import.xlsx')
    print("Columns:", list(df.columns))
    
    years = sorted(df['tahun'].unique())
    print("Years:", years)
    tahunAwal = int(years[0])
    tahunAkhir = int(years[-1])
    
    # In JS, the first 'nilai' column (index 7) is nilaiProvVal, and the second (index 8) is nilaiKabVal
    # Let's map it exactly like JS does:
    # index 7 in df is 'nilai', index 8 is 'nilai.1' (since pandas renames duplicate columns)
    
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
    
    with open('scratch/parsed_test_import_payload.json', 'w') as f:
        json.dump(payload, f, indent=2)
    print("Saved payload to scratch/parsed_test_import_payload.json")

except Exception as e:
    print("Error:", e)
