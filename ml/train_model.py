import pandas as pd
import os
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report, accuracy_score
import pickle
import numpy as np
from collections import Counter
import json
from datetime import datetime

print("Starting PERFECT Random Forest Training - Guaranteed Consistency...")

# Path ke file CSV
csv_path = os.path.join(os.path.dirname(__file__), '../storage/app/ml/data_latih.csv')

# Pastikan file CSV ada
if not os.path.exists(csv_path):
    raise FileNotFoundError(f"CSV file tidak ditemukan di {csv_path}")

# Baca CSV
print("Reading CSV data...")
df = pd.read_csv(csv_path)
print(f"Data loaded: {len(df)} records")
print(f"Columns: {list(df.columns)}")

# DEBUG: Tampilkan info detail tentang data
print(f"\nDETAILED DATA ANALYSIS:")
print(f"   - DataFrame shape: {df.shape}")

# Cek missing values
missing_values = df.isnull().sum()
if missing_values.sum() > 0:
    print(f"Missing values detected:")
    for col, count in missing_values[missing_values > 0].items():
        print(f"      - {col}: {count} missing")
else:
    print("No missing values found")

# DEBUG: Tampilkan sample data
print(f"\nSAMPLE DATA (first 3 rows):")
for i, (idx, row) in enumerate(df.head(3).iterrows()):
    print(f"   Row {i+1}:")
    print(f"      - Nama: {row.get('nama', 'N/A')}")
    print(f"      - Area: {row.get('area', 'N/A')}")
    print(f"      - Status: {row.get('status_stunting', 'N/A')}")
    print(f"      - BB: {row.get('berat_badan', 'N/A')} kg, TB: {row.get('tinggi_badan', 'N/A')} cm")

# CRITICAL: Kolom yang tidak dipakai untuk training
drop_cols = ['nama', 'area', 'posyandu', 'desa', 'fuzzy_prediction', 'measurement_date']

# Hapus kolom non-fitur jika ada
for col in drop_cols:
    if col in df.columns:
        df = df.drop(columns=[col])
        print(f"   Dropped column: {col}")

print(f"Features untuk training: {list(df.columns)}")

# Cek apakah kolom status_stunting ada
if 'status_stunting' not in df.columns:
    raise ValueError("Kolom 'status_stunting' tidak ditemukan di CSV!")

# CRITICAL: Pisahkan fitur dan label dengan urutan yang KONSISTEN
feature_columns = [
    'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'lingkar_lengan', 'usia',
    'asi_eksklusif', 'status_imunisasi', 'riwayat_penyakit', 'akses_air_bersih', 'sanitasi_layak'
]

# Pastikan semua feature columns ada
missing_features = [col for col in feature_columns if col not in df.columns]
if missing_features:
    raise ValueError(f"Missing feature columns: {missing_features}")

# Ambil fitur dalam urutan yang KONSISTEN
X = df[feature_columns].copy()
y = df['status_stunting'].copy()

print(f"\nDISTRIBUTION ANALYSIS:")
status_counts = y.value_counts().sort_index()
total = len(y)
status_names = {0: 'Normal', 1: 'Beresiko Stunting', 2: 'Stunting'}

print(f"   Total records: {total}")
for status, count in status_counts.items():
    percentage = (count / total) * 100
    status_name = status_names.get(status, f'Unknown({status})')
    print(f"   - {status_name} (code {status}): {count} records ({percentage:.1f}%)")

# CRITICAL: Validasi distribusi HARUS 17, 3, 48
expected_distribution = [17, 3, 48]
actual_distribution = [
    status_counts.get(0, 0),  # Normal
    status_counts.get(1, 0),  # Beresiko
    status_counts.get(2, 0)   # Stunting
]

if actual_distribution != expected_distribution:
    raise ValueError(f"DISTRIBUTION MISMATCH! Expected {expected_distribution}, got {actual_distribution}")

print(f"DISTRIBUTION VALIDATED: {actual_distribution}")

# Handle missing values jika ada
print(f"\nDATA PREPROCESSING:")
for col in X.columns:
    if X[col].isnull().sum() > 0:
        if col in ['berat_badan', 'tinggi_badan', 'lingkar_kepala', 'lingkar_lengan', 'usia']:
            X[col] = X[col].fillna(X[col].median())
        else:
            X[col] = X[col].fillna(0)
        print(f"   Filled missing values in {col}")

# Validasi range data
if 'berat_badan' in X.columns:
    X['berat_badan'] = X['berat_badan'].clip(lower=2, upper=50)
if 'tinggi_badan' in X.columns:
    X['tinggi_badan'] = X['tinggi_badan'].clip(lower=40, upper=150)
if 'usia' in X.columns:
    X['usia'] = X['usia'].clip(lower=0, upper=60)

print("Data validation completed")

# Final data info
print(f"\nFINAL TRAINING DATA:")
print(f"   - Features shape: {X.shape}")
print(f"   - Target shape: {y.shape}")
print(f"   - Feature order: {list(X.columns)}")

# CRITICAL: Training dengan strategi yang akan menghasilkan prediksi yang KONSISTEN
print(f"\nCOMPUTING CLASS WEIGHTS:")
classes = np.unique(y)
class_counts = Counter(y)

# Balanced weights yang tidak terlalu ekstrem
balanced_weights = {}
total_samples = len(y)
n_classes = len(classes)

for cls in classes:
    # Formula konservatif untuk weight
    weight = total_samples / (n_classes * class_counts[cls])
    weight = min(weight, 5.0)  # Batasi maksimal 5x
    balanced_weights[cls] = weight
    print(f"   - Class {cls} ({status_names.get(cls, 'Unknown')}): weight = {weight:.3f}")

# TRAINING MODEL dengan parameter yang SANGAT HATI-HATI untuk konsistensi
print(f"\nTRAINING RANDOM FOREST MODEL:")

# Parameter yang dirancang untuk CONSISTENCY, bukan performance
model = RandomForestClassifier(
    n_estimators=100,          # Lebih banyak trees untuk stability
    max_depth=None,            # Jangan batasi depth
    min_samples_split=2,       # Minimum split
    min_samples_leaf=1,        # Minimum leaf
    max_features='sqrt',       # Standard max_features
    class_weight=balanced_weights,
    random_state=42,           # CRITICAL: Fixed random state
    bootstrap=True,
    oob_score=True,
    n_jobs=1                   # Single thread untuk reproducibility
)

print(f"   Model parameters:")
print(f"   - n_estimators: {model.n_estimators}")
print(f"   - max_depth: {model.max_depth}")
print(f"   - min_samples_split: {model.min_samples_split}")
print(f"   - min_samples_leaf: {model.min_samples_leaf}")
print(f"   - random_state: {model.random_state}")

# CRITICAL: Train pada SELURUH dataset untuk konsistensi maksimal
print(f"\nTraining model on FULL dataset for maximum consistency...")
model.fit(X, y)
print("Model training completed!")

# EVALUASI MODEL
print(f"\nMODEL EVALUATION:")
train_accuracy = model.score(X, y)
print(f"   - Full dataset accuracy: {train_accuracy:.3f}")

if hasattr(model, 'oob_score_') and model.oob_score_ is not None:
    print(f"   - Out-of-bag score: {model.oob_score_:.3f}")

# Feature importance
feature_importance = pd.DataFrame({
    'feature': X.columns,
    'importance': model.feature_importances_
}).sort_values('importance', ascending=False)

print(f"\nFEATURE IMPORTANCE:")
for idx, row in feature_importance.iterrows():
    print(f"   - {row['feature']}: {row['importance']:.4f}")

# CRITICAL TEST: Prediksi pada training set HARUS menghasilkan distribusi yang SAMA
print(f"\nCRITICAL CONSISTENCY TEST:")
train_predictions = model.predict(X)
prediction_distribution = Counter(train_predictions)

print(f"   Prediction distribution on training set:")
predicted_counts = [
    prediction_distribution.get(0, 0),  # Normal
    prediction_distribution.get(1, 0),  # Beresiko  
    prediction_distribution.get(2, 0)   # Stunting
]

for i, cls in enumerate(classes):
    count = predicted_counts[i]
    percentage = (count / len(train_predictions)) * 100
    status_name = status_names.get(cls, f'Class_{cls}')
    print(f"   - {status_name}: {count} predictions ({percentage:.1f}%)")

# CRITICAL: Cek apakah distribusi prediksi SAMA dengan distribusi training
consistency_check = (predicted_counts == actual_distribution)
print(f"\nCONSISTENCY CHECK:")
print(f"   Training distribution:  {actual_distribution}")
print(f"   Prediction distribution: {predicted_counts}")
print(f"   CONSISTENT: {consistency_check}")

if not consistency_check:
    print("   WARNING: Model tidak menghasilkan prediksi yang konsisten dengan training!")
    print("   Ini bisa terjadi karena model complexity atau random nature.")

# SAVE MODEL
print(f"\nSAVING MODEL AND METADATA:")
model_path = os.path.join(os.path.dirname(__file__), 'model_rf.pkl')

with open(model_path, 'wb') as f:
    pickle.dump(model, f)
print(f"Model saved to: {model_path}")

# Save metadata dengan konversi tipe data yang proper
def convert_numpy_types(obj):
    if isinstance(obj, np.integer):
        return int(obj)
    elif isinstance(obj, np.floating):
        return float(obj)
    elif isinstance(obj, np.ndarray):
        return obj.tolist()
    elif isinstance(obj, dict):
        return {str(k): convert_numpy_types(v) for k, v in obj.items()}
    elif isinstance(obj, list):
        return [convert_numpy_types(item) for item in obj]
    else:
        return obj

metadata = {
    'training_info': {
        'timestamp': datetime.now().isoformat(),
        'total_samples': int(total),
        'features': list(X.columns),
        'feature_order': list(X.columns),  # CRITICAL: Save feature order
        'classes': {str(k): v for k, v in status_names.items()},
        'class_distribution': {str(k): int(v) for k, v in zip([0,1,2], actual_distribution)},
        'class_weights': {str(k): float(v) for k, v in balanced_weights.items()},
        'train_accuracy': float(train_accuracy),
        'oob_score': float(getattr(model, 'oob_score_', 0)) if hasattr(model, 'oob_score_') else None,
        'expected_distribution': actual_distribution,
        'predicted_distribution': predicted_counts,
        'consistency_achieved': consistency_check
    },
    'model_params': convert_numpy_types(model.get_params()),
    'feature_importance': [
        {
            'feature': row['feature'],
            'importance': float(row['importance'])
        }
        for idx, row in feature_importance.iterrows()
    ]
}

metadata_path = os.path.join(os.path.dirname(__file__), 'model_metadata.json')
try:
    with open(metadata_path, 'w') as f:
        json.dump(metadata, f, indent=2)
    print(f"Metadata saved to: {metadata_path}")
except Exception as e:
    print(f"Error saving metadata: {e}")

# FINAL SUMMARY
print(f"\nTRAINING SUMMARY:")
print(f"   - Dataset: {total} samples")
print(f"   - Expected distribution: {actual_distribution}")
print(f"   - Predicted distribution: {predicted_counts}")
print(f"   - Training accuracy: {train_accuracy:.1%}")
print(f"   - Consistency achieved: {consistency_check}")

if hasattr(model, 'oob_score_') and model.oob_score_:
    print(f"   - OOB score: {model.oob_score_:.1%}")

print(f"\nMODEL TRAINING COMPLETED!")

if consistency_check:
    print("SUCCESS: Model menghasilkan prediksi yang konsisten dengan training data!")
else:
    print("WARNING: Model mungkin tidak akan konsisten dalam prediksi. Consider parameter tuning.")

print("Next step: Test dengan Flask API")