import pandas as pd
df = pd.read_excel('test_import.xlsx')
non_empty_nulls = df[df['nama_provinsi'].isnull() & df['nilai.1'].notnull()]
print("Count of rows where nama_provinsi is null but nilai.1 is NOT null:", len(non_empty_nulls))
print(non_empty_nulls.head(10))
