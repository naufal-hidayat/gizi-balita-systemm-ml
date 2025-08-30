from flask import Flask, request, jsonify
import pickle
from flask_cors import CORS

# Inisialisasi aplikasi Flask
app = Flask(__name__)
CORS(app)  # Aktifkan akses dari luar (misalnya Laravel)

# Load model Random Forest
try:
    with open("model_rf.pkl", "rb") as f:
        model = pickle.load(f)
    print("✅ Model berhasil dimuat.")
except FileNotFoundError:
    print("❌ model_rf.pkl tidak ditemukan. Jalankan train_model.py terlebih dahulu.")
    model = None

# Endpoint root: cek server
@app.route("/", methods=["GET"])
def home():
    return "✅ Flask API for Prediksi Gizi is running."

# Endpoint GET untuk predict (info saja)
@app.route("/predict", methods=["GET"])
def predict_info():
    return "Gunakan metode POST dengan data: umur, berat, tinggi"

# Endpoint POST untuk prediksi
@app.route("/predict", methods=["POST"])
def predict():
    if model is None:
        return jsonify({"error": "Model belum tersedia"}), 500

    try:
        data = request.get_json()
        umur = float(data["umur"])
        berat = float(data["berat"])
        tinggi = float(data["tinggi"])

        prediction = model.predict([[umur, berat, tinggi]])
        return jsonify({"status_gizi": prediction[0]})
    except Exception as e:
        return jsonify({"error": str(e)}), 400

# Jalankan server
if __name__ == "__main__":
    app.run(debug=True)
