import pandas as pd
import json
import math

try:
    df = pd.read_excel('test_import.xlsx')
    
    # Extract list of kabupaten from column D (nama_kabupaten)
    kab_list = df['nama_kabupaten'].dropna().tolist()
    # Extract list of sectors from column F (nama_sektor)
    sektor_list = df['nama_sektor'].dropna().tolist()
    
    print("Kabupaten count:", len(kab_list))
    print("Sektor count:", len(sektor_list))
    
    # Pre-extract province PDRB values from the first 85 rows
    # Key: (sektor_name, tahun) -> nilai
    prov_pdrb_map = {}
    for i in range(len(sektor_list) * 5): # first 85 rows
        row = df.iloc[i]
        sektor_name = sektor_list[i % len(sektor_list)]
        tahun = int(row['tahun'])
        val = float(row['nilai'])
        if not math.isnan(val):
            prov_pdrb_map[(sektor_name, tahun)] = val
            
    print("Pre-extracted province PDRB mapping count:", len(prov_pdrb_map))
    
    # Reconstruct each row
    data_rows = []
    
    for i in range(len(df)):
        row = df.iloc[i]
        
        # Determine kabupaten index and name
        kab_idx = i // (len(sektor_list) * 5) # 17 sectors * 5 years = 85
        if kab_idx >= len(kab_list):
            continue
        kab_name = kab_list[kab_idx]
        
        # Determine sector index and name
        sektor_idx = i % len(sektor_list)
        sektor_name = sektor_list[sektor_idx]
        
        tahun = int(row['tahun'])
        
        # Look up province PDRB value
        nilai_prov = prov_pdrb_map.get((sektor_name, tahun), 0.0)
        
        # Kabupaten PDRB is in the second 'nilai' column (index 8, pandas 'nilai.1')
        nilai_kab = float(row['nilai.1'])
        if math.isnan(nilai_kab):
            nilai_kab = 0.0
            
        data_rows.append({
            'provinsi': 'SUMATERA UTARA',
            'kabupaten': kab_name,
            'sektor': sektor_name,
            'tahun': tahun,
            'nilaiProv': nilai_prov,
            'nilaiKab': nilai_kab
        })
        
    print("Parsed data rows:", len(data_rows))
    print("Sample 0:", data_rows[0])
    print("Sample 85:", data_rows[85])
    
    # Calculate totals
    years = sorted(list(set([r['tahun'] for r in data_rows])))
    tahunAwal = years[0]
    tahunAkhir = years[-1]
    print("Tahun Awal:", tahunAwal, "Tahun Akhir:", tahunAkhir)
    
    totalKab = {}
    totalProv = {}
    for r in data_rows:
        kabKey = f"{r['kabupaten']}_{r['tahun']}"
        provKey = f"{r['provinsi']}_{r['tahun']}"
        totalKab[kabKey] = totalKab.get(kabKey, 0.0) + r['nilaiKab']
        totalProv[provKey] = totalProv.get(provKey, 0.0) + r['nilaiProv']
        
    combined = {}
    for r in data_rows:
        if r['tahun'] not in (tahunAwal, tahunAkhir):
            continue
        groupKey = f"{r['provinsi']}_{r['kabupaten']}_{r['sektor']}"
        if groupKey not in combined:
            combined[groupKey] = {
                'Provinsi': r['provinsi'],
                'Kabupaten/Kota': r['kabupaten'],
                'Sektor': r['sektor'],
                'Tahun Awal': tahunAwal,
                'Tahun Akhir': tahunAkhir,
                'PDRB Sektor Analisis Awal': 0.0,
                'PDRB Sektor Analisis Akhir': 0.0,
                'Total PDRB Analisis Awal': totalKab.get(f"{r['kabupaten']}_{tahunAwal}", 0.0),
                'Total PDRB Analisis Akhir': totalKab.get(f"{r['kabupaten']}_{tahunAkhir}", 0.0),
                'PDRB Sektor Pembanding Awal': 0.0,
                'PDRB Sektor Pembanding Akhir': 0.0,
                'Total PDRB Pembanding Awal': totalProv.get(f"{r['provinsi']}_{tahunAwal}", 0.0),
                'Total PDRB Pembanding Akhir': totalProv.get(f"{r['provinsi']}_{tahunAkhir}", 0.0)
            }
        if r['tahun'] == tahunAwal:
            combined[groupKey]['PDRB Sektor Analisis Awal'] = r['nilaiKab']
            combined[groupKey]['PDRB Sektor Pembanding Awal'] = r['nilaiProv']
        elif r['tahun'] == tahunAkhir:
            combined[groupKey]['PDRB Sektor Analisis Akhir'] = r['nilaiKab']
            combined[groupKey]['PDRB Sektor Pembanding Akhir'] = r['nilaiProv']
            
    print("Combined rows count:", len(combined))
    first_key = list(combined.keys())[0]
    print("First combined row:", combined[first_key])
    
    # Save first 5 combined rows to check
    keys = list(combined.keys())[:5]
    for k in keys:
        print(k, "=>", combined[k])
        
except Exception as e:
    print("Error:", e)
