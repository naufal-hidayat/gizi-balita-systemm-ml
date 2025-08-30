from flask import Flask, request, jsonify
import pickle
import os
import numpy as np
import pandas as pd
import logging

app = Flask(__name__)

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Load model saat startup
model_path = os.path.join(os.path.dirname(__file__), 'model_rf.pkl')
metadata_path = os.path.join(os.path.dirname(__file__), 'model_metadata.pkl')

model = None
metadata = None

try:
    if not os.path.exists(model_path):
        raise FileNotFoundError(f"❌ File model_rf.pkl tidak ditemukan di {model_path}")

    with open(model_path, 'rb') as f:
        model = pickle.load(f)
    logger.info("✅ Model berhasil dimuat")
    
    # Load metadata jika ada
    if os.path.exists(metadata_path):
        with open(metadata_path, 'rb') as f:
            metadata = pickle.load(f)
        logger.info("✅ Metadata berhasil dimuat")
    
except Exception as e:
    logger.error(f"❌ Gagal memuat model: {str(e)}")
    model = None

@app.route('/', methods=['GET'])
def home():
    model_info = {}
    if model is not None:
        model_info['classes'] = model.classes_.tolist()
        model_info['n_features'] = model.n_features_in_
        if metadata:
            model_info['feature_names'] = metadata.get('features', [])
            model_info['class_distribution'] = metadata.get('class_distribution', {})
    
    return jsonify({
        "message": "✅ Random Forest API Aktif",
        "model_loaded": model is not None,
        "model_info": model_info,
        "endpoints": {
            "single_predict": "/predict",
            "bulk_predict": "/predict-bulk"
        }
    })

@app.route('/predict', methods=['POST'])
def predict():
    if model is None:
        return jsonify({'error': '❌ Model belum dimuat'}), 500
        
    try:
        data = request.get_json()
        
        # Validasi input
        required_fields = [
            'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'lingkar_lengan', 
            'usia', 'asi_eksklusif', 'status_imunisasi', 'riwayat_penyakit', 
            'akses_air_bersih', 'sanitasi_layak'
        ]
        
        for field in required_fields:
            if field not in data:
                return jsonify({'error': f'❌ Field {field} tidak ditemukan'}), 400
        
        # Siapkan input array dengan nama kolom yang benar
        input_data = pd.DataFrame([{
            'berat_badan': float(data['berat_badan']),
            'tinggi_badan': float(data['tinggi_badan']),
            'lingkar_kepala': float(data['lingkar_kepala']),
            'lingkar_lengan': float(data['lingkar_lengan']),
            'usia': float(data['usia']),
            'asi_eksklusif': int(data['asi_eksklusif']),
            'status_imunisasi': int(data['status_imunisasi']),
            'riwayat_penyakit': int(data['riwayat_penyakit']),
            'akses_air_bersih': int(data['akses_air_bersih']),
            'sanitasi_layak': int(data['sanitasi_layak'])
        }])

        # Prediksi
        prediction = model.predict(input_data)[0]
        probabilities = model.predict_proba(input_data)[0]
        confidence = probabilities.max()
        
        # Mapping hasil
        status_map = {
            0: 'Normal',
            1: 'Beresiko Stunting', 
            2: 'Stunting'
        }
        
        status = status_map.get(prediction, 'Unknown')

        return jsonify({
            'nama': data.get('nama', 'Unknown'),
            'area': data.get('area', 'Unknown'),
            'posyandu': data.get('posyandu', 'Unknown'),
            'desa': data.get('desa', 'Unknown'),
            'status_gizi': status,
            'code': int(prediction),
            'confidence': round(confidence * 100, 2),
            'probabilities': {
                'normal': round(probabilities[0] * 100, 2),
                'beresiko': round(probabilities[1] * 100, 2) if len(probabilities) > 1 else 0,
                'stunting': round(probabilities[2] * 100, 2) if len(probabilities) > 2 else 0
            }
        })
        
    except ValueError as e:
        return jsonify({'error': f'❌ Error konversi data: {str(e)}'}), 400
    except Exception as e:
        logger.error(f"Error dalam prediksi: {str(e)}")
        return jsonify({'error': f'❌ Gagal melakukan prediksi: {str(e)}'}), 500

@app.route('/predict-bulk', methods=['POST'])
def predict_bulk():
    if model is None:
        return jsonify({'error': '❌ Model belum dimuat'}), 500
        
    try:
        data_list = request.get_json()
        
        if not isinstance(data_list, list):
            return jsonify({'error': '❌ Input harus berupa array'}), 400
            
        if len(data_list) == 0:
            return jsonify({'error': '❌ Data kosong'}), 400
        
        results = []
        processed_count = 0
        
        # Siapkan DataFrame untuk batch prediction
        batch_data = []
        batch_metadata = []
        
        for i, data in enumerate(data_list):
            try:
                # Validasi setiap data
                required_fields = [
                    'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'lingkar_lengan', 
                    'usia', 'asi_eksklusif', 'status_imunisasi', 'riwayat_penyakit', 
                    'akses_air_bersih', 'sanitasi_layak'
                ]
                
                # Skip data yang tidak lengkap
                if not all(field in data for field in required_fields):
                    logger.warning(f"Data ke-{i+1} tidak lengkap, dilewati")
                    continue
                
                # Tambahkan ke batch
                batch_data.append({
                    'berat_badan': float(data['berat_badan']),
                    'tinggi_badan': float(data['tinggi_badan']),
                    'lingkar_kepala': float(data['lingkar_kepala']),
                    'lingkar_lengan': float(data['lingkar_lengan']),
                    'usia': float(data['usia']),
                    'asi_eksklusif': int(data['asi_eksklusif']),
                    'status_imunisasi': int(data['status_imunisasi']),
                    'riwayat_penyakit': int(data['riwayat_penyakit']),
                    'akses_air_bersih': int(data['akses_air_bersih']),
                    'sanitasi_layak': int(data['sanitasi_layak'])
                })
                
                batch_metadata.append({
                    'nama': data.get('nama', f'Data-{i+1}'),
                    'area': data.get('area', 'Unknown'),
                    'posyandu': data.get('posyandu', 'Unknown'),
                    'desa': data.get('desa', 'Unknown'),
                    'index': i
                })
                
                processed_count += 1
                
            except (ValueError, TypeError) as e:
                logger.warning(f"Error pada data ke-{i+1}: {str(e)}")
                continue
            except Exception as e:
                logger.error(f"Error tidak terduga pada data ke-{i+1}: {str(e)}")
                continue

        if len(batch_data) == 0:
            return jsonify({'error': '❌ Tidak ada data yang berhasil diproses'}), 400

        # Batch prediction
        df_batch = pd.DataFrame(batch_data)
        predictions = model.predict(df_batch)
        probabilities = model.predict_proba(df_batch)
        
        # Mapping hasil
        status_map = {
            0: 'Normal',
            1: 'Beresiko Stunting',
            2: 'Stunting'
        }
        
        # Compile results
        for i, (pred, probs, meta) in enumerate(zip(predictions, probabilities, batch_metadata)):
            confidence = probs.max()
            status = status_map.get(pred, 'Unknown')
            
            results.append({
                'nama': meta['nama'],
                'area': meta['area'],
                'posyandu': meta['posyandu'],
                'desa': meta['desa'],
                'status_gizi': status,
                'code': int(pred),
                'confidence': round(confidence * 100, 2),
                'probabilities': {
                    'normal': round(probs[0] * 100, 2),
                    'beresiko': round(probs[1] * 100, 2) if len(probs) > 1 else 0,
                    'stunting': round(probs[2] * 100, 2) if len(probs) > 2 else 0
                }
            })

        # Analisis hasil
        prediction_counts = {}
        for result in results:
            status = result['status_gizi']
            prediction_counts[status] = prediction_counts.get(status, 0) + 1

        logger.info(f"✅ Berhasil memproses {len(results)} dari {len(data_list)} data")
        logger.info(f"📊 Distribusi prediksi: {prediction_counts}")
        
        return jsonify({
            'data': results,
            'summary': {
                'total_input': len(data_list),
                'total_processed': len(results),
                'success_rate': round((len(results) / len(data_list)) * 100, 2),
                'prediction_distribution': prediction_counts
            }
        })
        
    except Exception as e:
        logger.error(f"Error dalam bulk prediksi: {str(e)}")
        return jsonify({'error': f'❌ Gagal melakukan bulk prediksi: {str(e)}'}), 500

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
        'status': 'healthy',
        'model_loaded': model is not None,
        'timestamp': str(pd.Timestamp.now()),
        'model_info': {
            'classes': model.classes_.tolist() if model is not None else None,
            'features': metadata.get('features', []) if metadata else []
        }
    })

if __name__ == '__main__':
    print("🚀 Starting Flask API...")
    print(f"📁 Model path: {model_path}")
    print(f"✅ Model loaded: {model is not None}")
    if model is not None:
        print(f"📊 Model classes: {model.classes_}")
        if metadata:
            print(f"📊 Features: {metadata.get('features', [])}")
    app.run(debug=True, host='127.0.0.1', port=5000)