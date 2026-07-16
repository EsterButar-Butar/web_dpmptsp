import pandas as pd
df = pd.read_excel('test_import.xlsx')
non_null_nilai = df[df['nilai'].notnull()]
print("Count of non-null in column 'nilai':", len(non_null_nilai))
print(non_null_nilai.iloc[0:20, [1, 3, 5, 6, 7, 8]])
print(non_null_nilai.tail(20).iloc[:, [1, 3, 5, 6, 7, 8]])
print("Unique years in non-null 'nilai':", non_null_nilai['tahun'].unique())
