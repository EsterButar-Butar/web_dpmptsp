import pandas as pd
df = pd.read_excel('test_import.xlsx')
sektor_list = df['nama_sektor'].dropna().tolist()
kab_list = df['nama_kabupaten'].dropna().tolist()

print("Sectors list:", sektor_list)
print("Kabupaten list:", kab_list)

# We want to print Row i where i = kab_idx * 85 (which is Sector 1, Year 2021 for each Kabupaten)
rows_to_check = []
for k_idx, k_name in enumerate(kab_list):
    row_idx = k_idx * 85
    if row_idx < len(df):
        row = df.iloc[row_idx]
        rows_to_check.append({
            'kab_idx': k_idx,
            'kab_name': k_name,
            'row_index': row_idx,
            'nilai_col7': row['nilai'],
            'nilai_col8': row['nilai.1']
        })

print(pd.DataFrame(rows_to_check))
