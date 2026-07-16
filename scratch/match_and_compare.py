import pandas as pd
df1 = pd.read_excel('test_import.xlsx')
df2 = pd.read_excel('test_import_sektoral.xlsx')

# Let's inspect the first 17 rows of test_import.xlsx
# We know the first 17 rows are for KAB. TAPANULI TENGAH, Year 2021, sectors 1 to 17.
# Let's match them with test_import_sektoral.xlsx rows for KAB. TAPANULI TENGAH, Year 2021
df1_sub = df1.iloc[0:17][['nama_sektor', 'tahun', 'nilai', 'nilai.1']].copy()
df1_sub.columns = ['sektor', 'tahun', 'test_import_col7', 'test_import_col8']

# In test_import_sektoral.xlsx, we filter for Tapanuli Tengah and 2021
df2_sub = df2[(df2['nama_kabupaten'] == 'KAB. TAPANULI TENGAH') & (df2['tahun'] == 2021)][['nama_sektor', 'nilai', 'nilai.1']].copy()
df2_sub.columns = ['sektor', 'sektoral_col7', 'sektoral_col8']

# Merge on sector name
merged = pd.merge(df1_sub, df2_sub, on='sektor', how='outer')
pd.set_option('display.max_columns', None)
pd.set_option('display.width', 1000)
print(merged)
