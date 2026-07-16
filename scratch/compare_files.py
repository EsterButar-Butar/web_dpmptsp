import pandas as pd
df1 = pd.read_excel('test_import.xlsx')
df2 = pd.read_excel('test_import_sektoral.xlsx')

print("test_import.xlsx row 0:")
print(df1.iloc[0])
print("\ntest_import_sektoral.xlsx row 0:")
print(df2.iloc[0])
