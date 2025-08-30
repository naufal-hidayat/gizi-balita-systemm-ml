import pandas as pd
import os

# Path awal & akhir
input_path = os.path.join(os.path.dirname(__file__), '../storage/app/ml/data_latih.csv')
output_path = os.path.join(os.path.dirname(__file__), '../storage/app/ml/data_latih_bersih.csv')

# Baca file
df = pd.read_csv(input_path)
print("✅ Jumlah data awal:", len(df))

# Normalisasi kolom status
if 'status_stunting' in df.columns:
    df['status_stunting'] = df['status_stunting'].astype(str).str.strip().str.lower()
else:
    raise ValueError("❌ Kolom 'status_stunting' tidak ditemukan.")

# Kolom minimal yang WAJIB diisi
required_cols = ['berat_badan', 'tinggi_badan', 'usia', 'status_stunting']

# Hapus baris jika kolom Wajib ada NaN
df_cleaned = df.dropna(subset=required_cols)

print("✅ Jumlah data setelah dibersihkan:", len(df_cleaned))
print("✅ Label yang digunakan:", df_cleaned['status_stunting'].unique().tolist())

# Simpan hasil
df_cleaned.to_csv(output_path, index=False)
print("✅ File bersih disimpan di:", output_path)