import pandas as pd
df = pd.read_excel('test_import.xlsx')
row = df.iloc[0]
print("nilai (col 7):", repr(row['nilai']))
print("nilai.1 (col 8):", repr(row['nilai.1']))
