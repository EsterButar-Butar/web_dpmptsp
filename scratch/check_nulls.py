import pandas as pd
df = pd.read_excel('test_import.xlsx')
print(df.isnull().sum())
print("Total rows:", len(df))
print(df[df['nama_provinsi'].notnull()].head(10))
