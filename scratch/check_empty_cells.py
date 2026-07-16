import pandas as pd
df = pd.read_excel('test_import.xlsx')
print(df.iloc[0:20, [1, 3, 5, 6, 7, 8]])
