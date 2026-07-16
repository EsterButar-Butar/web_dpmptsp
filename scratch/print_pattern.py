import pandas as pd
df = pd.read_excel('test_import.xlsx')
pd.set_option('display.max_columns', None)
pd.set_option('display.max_rows', 100)
pd.set_option('display.width', 1000)
print(df.iloc[0:50])
