import pandas as pd
df = pd.read_excel('test_import.xlsx')
valid_df = df.dropna(subset=['nama_provinsi', 'nama_kabupaten'])
print("Total rows:", len(df))
print("Valid rows (non-null prov and kab):", len(valid_df))
print("Unique provinces in valid rows:", valid_df['nama_provinsi'].unique())
print("Unique kabupaten in valid rows:", valid_df['nama_kabupaten'].unique())
print("Years in valid rows:", valid_df['tahun'].unique())
