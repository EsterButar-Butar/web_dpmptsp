import pandas as pd
df = pd.read_excel('test_import.xlsx')
print("Total rows in Excel:", len(df))
print("Unique provinces in Excel:", df['nama_provinsi'].unique())
print("Unique kabupaten in Excel:", df['nama_kabupaten'].unique())
print("Unique sectors in Excel:", len(df['nama_sektor'].unique()))
print("Years in Excel:", df['tahun'].unique())
