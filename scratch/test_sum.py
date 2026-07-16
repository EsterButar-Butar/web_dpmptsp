import pandas as pd
import math
df = pd.read_excel('test_import.xlsx')
sektor_list = df['nama_sektor'].dropna().tolist()
kab_list = df['nama_kabupaten'].dropna().tolist()

total = 0.0
for k_idx in range(len(kab_list)):
    row_idx = k_idx * 85
    if row_idx < len(df):
        val = df.iloc[row_idx]['nilai.1']
        if not math.isnan(val):
            total += float(val)
        
print("Sum of nilai.1 across all kabupaten (excluding NaNs):", total)
