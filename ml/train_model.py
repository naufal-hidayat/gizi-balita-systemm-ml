import pandas as pd
import os
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report, confusion_matrix
import pickle
import numpy as np
from collections import Counter

print("🚀 Starting model training with improved handling...")

# Path ke file CSV
csv_path = os.path.join(os.path.dirname(__file__), '../storage/app/ml/data_latih.csv')

# Pastikan file CSV ada
if not os.path.exists(csv_path):
    raise FileNotFoundError(f"❌ File CSV tidak ditemukan di {csv_path}")

# Baca CSV
print("📖 Reading data...")
df = pd.read_csv(csv_path)
print(f"✅ Data loaded: {len(df)} records")

# Kolom yang tidak dipakai untuk training
drop_cols = ['nama', 'area', 'posyandu', 'desa']

# Hapus kolom non-fitur jika ada
for col in drop_cols:
    if col in df.columns:
        df = df.drop(columns=[col])

# Cek apakah kolom status_stunting ada
if 'status_stunting' not in df.columns:
    raise ValueError("❌ Kolom 'status_stunting' tidak ditemukan di CSV!")

# Pisahkan fitur dan label
X = df.drop(columns=['status_stunting'])
y = df['status_stunting']

# Tampilkan distribusi data
print(f"\n📊 Data distribution:")
status_counts = y.value_counts().sort_index()
total = len(y)
status_names = ['Normal', 'Beresiko Stunting', 'Stunting']
for status, count in status_counts.items():
    percentage = (count / total) * 100
    print(f"   - {status_names[status]}: {count} records ({percentage:.1f}%)")

# Handle missing values
if X.isnull().values.any() or y.isnull().values.any():
    print("⚠️  Cleaning missing values...")
    X = X.fillna(X.median())
    y = y.fillna(0)
else:
    print("✅ No missing values")

# PERBAIKAN 1: Cek apakah data cukup untuk split
print(f"\n🔍 Analyzing data sufficiency...")
min_samples_per_class = y.value_counts().min()
print(f"   - Minimum samples per class: {min_samples_per_class}")

if total < 30:
    print("⚠️  WARNING: Very small dataset! Using simplified approach.")
    use_validation = False
else:
    use_validation = True

# PERBAIKAN 2: Handle class weights dengan lebih konservatif
print("\n⚖️  Computing balanced class weights...")
classes = np.unique(y)
class_counts = Counter(y)

# Manual balanced weights yang tidak terlalu ekstrem
total_samples = len(y)
n_classes = len(classes)

balanced_weights = {}
for cls in classes:
    # Formula: total_samples / (n_classes * class_count)
    # Tapi dibatasi agar tidak terlalu ekstrem
    weight = total_samples / (n_classes * class_counts[cls])
    # Batasi weight maksimal 5x untuk mencegah overfitting
    weight = min(weight, 5.0)
    balanced_weights[cls] = weight

print(f"   - Balanced class weights: {balanced_weights}")

# PERBAIKAN 3: Model dengan parameter yang lebih konservatif
print("\n🤖 Training Random Forest with conservative parameters...")
model = RandomForestClassifier(
    n_estimators=50,              # Kurangi dari 100 untuk dataset kecil
    max_depth=4,                  # Batasi depth untuk mencegah overfitting
    min_samples_split=5,          # Minimal 5 samples untuk split
    min_samples_leaf=3,           # Minimal 3 samples per leaf
    max_features='sqrt',          # Gunakan subset features
    class_weight=balanced_weights, # Gunakan balanced weights yang sudah diperbaiki
    random_state=42,
    bootstrap=True,               # Gunakan bootstrap untuk generalisasi lebih baik
    oob_score=True               # Out-of-bag score untuk evaluasi
)

# PERBAIKAN 4: Split yang lebih robust
if use_validation and min_samples_per_class >= 2:
    try:
        # Coba stratified split dulu
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.15, random_state=42, stratify=y  # Kurangi test size
        )
        print(f"✅ Stratified split successful")
        print(f"   - Training: {len(X_train)} samples")
        print(f"   - Testing: {len(X_test)} samples")
        
        # Verifikasi distribusi di test set
        test_distribution = Counter(y_test)
        print(f"   - Test set distribution: {test_distribution}")
        
    except ValueError as e:
        print(f"⚠️  Stratified split failed: {e}")
        print("   Using simple random split...")
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.15, random_state=42
        )
        
else:
    print("⚠️  Dataset too small for validation split. Using full dataset for training.")
    X_train, X_test, y_train, y_test = X, X.iloc[:5], y, y.iloc[:5]  # Dummy test set

# Train model
model.fit(X_train, y_train)
print("✅ Model training completed!")

# PERBAIKAN 5: Evaluasi yang lebih robust
print("\n📊 Model evaluation...")
train_accuracy = model.score(X_train, y_train)
print(f"   - Training accuracy: {train_accuracy:.3f}")

if hasattr(model, 'oob_score_') and model.oob_score_ is not None:
    print(f"   - Out-of-bag score: {model.oob_score_:.3f}")

if use_validation:
    test_accuracy = model.score(X_test, y_test)
    print(f"   - Test accuracy: {test_accuracy:.3f}")
    
    # Prediksi untuk evaluasi
    y_pred = model.predict(X_test)
    
    # Classification report yang aman
    unique_labels_test = np.unique(y_test)
    unique_labels_pred = np.unique(y_pred)
    all_labels = np.unique(np.concatenate([unique_labels_test, unique_labels_pred]))
    
    print(f"\n📋 Classification Report:")
    print(f"   Classes in test set: {[status_names[i] for i in unique_labels_test]}")
    
    try:
        # Hanya tampilkan report untuk kelas yang ada di test set
        target_names_subset = [status_names[i] for i in all_labels]
        report = classification_report(
            y_test, y_pred, 
            labels=all_labels,
            target_names=target_names_subset, 
            zero_division=0
        )
        print(report)
    except Exception as e:
        print(f"   ⚠️  Could not generate full report: {e}")
        print(f"   Simple accuracy: {test_accuracy:.3f}")

# Feature importance
feature_importance = pd.DataFrame({
    'feature': X.columns,
    'importance': model.feature_importances_
}).sort_values('importance', ascending=False)

print("\n🔝 Top 5 Important Features:")
for idx, row in feature_importance.head().iterrows():
    print(f"   - {row['feature']}: {row['importance']:.3f}")

# PERBAIKAN 6: Test predictions untuk memastikan model bekerja
print("\n🧪 Testing prediction variety...")
train_predictions = model.predict(X_train)
prediction_distribution = Counter(train_predictions)

print(f"   Prediction distribution on training set:")
for cls, count in sorted(prediction_distribution.items()):
    percentage = (count / len(train_predictions)) * 100
    print(f"   - {status_names[cls]}: {count} predictions ({percentage:.1f}%)")

# Cek apakah model menghasilkan variasi prediksi
unique_predictions = len(prediction_distribution)
if unique_predictions == 1:
    print("⚠️  WARNING: Model only predicts one class! This indicates severe overfitting.")
    print("   Recommendation: Add more diverse training data.")
elif unique_predictions < len(classes):
    print(f"⚠️  WARNING: Model only predicts {unique_predictions} out of {len(classes)} classes.")
else:
    print("✅ Model produces varied predictions across all classes.")

# Simpan model
print("\n💾 Saving model...")
model_path = os.path.join(os.path.dirname(__file__), 'model_rf.pkl')
with open(model_path, 'wb') as f:
    pickle.dump(model, f)

print(f"✅ Model saved to: {model_path}")

# PERBAIKAN 7: Simpan metadata lengkap
metadata = {
    'features': list(X.columns),
    'classes': status_names,
    'class_distribution': status_counts.to_dict(),
    'class_weights': balanced_weights,
    'model_params': model.get_params(),
    'training_info': {
        'total_samples': total,
        'train_accuracy': train_accuracy,
        'oob_score': getattr(model, 'oob_score_', None),
        'feature_importance': feature_importance.to_dict('records')
    }
}

metadata_path = os.path.join(os.path.dirname(__file__), 'model_metadata.pkl')
with open(metadata_path, 'wb') as f:
    pickle.dump(metadata, f)

print(f"✅ Metadata saved to: {metadata_path}")

# Summary dan rekomendasi
print(f"\n🎯 TRAINING SUMMARY:")
print(f"   - Dataset size: {total} samples")
print(f"   - Training accuracy: {train_accuracy:.3f}")
if hasattr(model, 'oob_score_') and model.oob_score_ is not None:
    print(f"   - Out-of-bag score: {model.oob_score_:.3f}")
print(f"   - Prediction variety: {unique_predictions}/{len(classes)} classes")

print(f"\n💡 RECOMMENDATIONS:")
if train_accuracy > 0.95:
    print("   ⚠️  High training accuracy suggests possible overfitting")
    print("   → Consider collecting more diverse training data")
if unique_predictions < len(classes):
    print("   ⚠️  Model doesn't predict all classes")
    print("   → Add more samples for underrepresented classes")
if total < 100:
    print("   ⚠️  Small dataset detected")
    print("   → Collect more data for better generalization")

print("\n🎉 Model training completed!")
print("   Next: Restart Flask API to load the new model")