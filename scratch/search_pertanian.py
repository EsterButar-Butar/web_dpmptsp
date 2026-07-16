import pandas as pd
df2 = pd.read_excel('test_import_sektoral.xlsx')
row = df2[df2['nama_sektor'].str.contains('PERTANIAN', na=False) & (df2['tahun'] == 2021)]
print(row)
