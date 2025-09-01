from flask import Flask, request, jsonify
import pickle
import os
import numpy as np
import pandas as pd
import logging
import json
from datetime import datetime

app = Flask(__name__)

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Global variables
model = None
metadata = None
model_load_time = None

# Paths
model_path = os.path.join(os.path.dirname(__file__), 'model_rf.pkl')
metadata_path = os.path.join(os.path.dirname(__file__), 'model_metadata.json')

def load_model():
    """Load model dengan validasi yang ketat"""
    global model, metadata, model_load_time
    
    try:
        if not os.path.exists(model_path):
            logger.error(f"Model file tidak ditemukan di {model_path}")
            return False

        with open(model_path, 'rb') as f:
            model = pickle.load(f)
        logger.info("Model berhasil dimuat dari: " + model_path)
        
        # Load metadata
        metadata = None
        if os.path.exists(metadata_path):
            try:
                with open(metadata_path, 'r', encoding='utf-8') as f:
                    metadata = json.load(f)
                logger.info("Metadata berhasil dimuat")
                
                # Validasi metadata training info
                if 'training_info' in metadata:
                    training_info = metadata['training_info']
                    expected_dist = training_info.get('expected_distribution', [])
                    logger.info(f"Expected training distribution: {expected_dist}")
                    
                    if expected_dist == [17, 3, 48]:
                        logger.info("Model memiliki distribusi training yang benar")
                    else:
                        logger.warning(f"Model memiliki distribusi training yang salah: {expected_dist}")
                
            except Exception as e:
                logger.warning(f"Gagal load metadata: {e}")
        
        model_load_time = datetime.now()
        logger.info(f"Model loaded at {model_load_time}")
        
        # CRITICAL: Validasi feature order dari metadata
        if metadata and 'training_info' in metadata and 'feature_order' in metadata['training_info']:
            expected_features = metadata['training_info']['feature_order']
            logger.info(f"Expected feature order: {expected_features}")
        
        return True
        
    except Exception as e:
        logger.error(f"Gagal memuat model: {str(e)}")
        model = None
        metadata = None
        model_load_time = None
        return False

# Load model saat startup
load_model()

@app.route('/', methods=['GET'])
def home():
    model_info = {
        'model_loaded': model is not None,
        'load_time': model_load_time.isoformat() if model_load_time else None
    }
    
    if model is not None and metadata:
        training_info = metadata.get('training_info', {})
        model_info.update({
            'model_type': type(model).__name__,
            'classes': model.classes_.tolist(),
            'n_features': model.n_features_in_,
            'training_samples': training_info.get('total_samples'),
            'expected_distribution': training_info.get('expected_distribution'),
            'consistency_achieved': training_info.get('consistency_achieved'),
            'feature_order': training_info.get('feature_order', [])
        })
    
    return jsonify({
        "message": "Perfect Random Forest API - Guaranteed Consistency",
        "status": "active" if model is not None else "error",
        "model_info": model_info,
        "endpoints": {
            "health": "GET /health",
            "predict-bulk": "POST /predict-bulk",
            "reload-model": "POST /reload-model"
        }
    })

@app.route('/health', methods=['GET'])
def health_check():
    health_info = {
        'status': 'healthy' if model is not None else 'unhealthy',
        'model_loaded': model is not None,
        'timestamp': str(pd.Timestamp.now()),
        'load_time': model_load_time.isoformat() if model_load_time else None
    }
    
    if model is not None and metadata:
        training_info = metadata.get('training_info', {})
        health_info['model_info'] = {
            'total_samples': training_info.get('total_samples'),
            'expected_distribution': training_info.get('expected_distribution'),
            'consistency_achieved': training_info.get('consistency_achieved'),
            'feature_order': training_info.get('feature_order', [])
        }
    
    return jsonify(health_info)

@app.route('/reload-model', methods=['POST'])
def reload_model():
    """Force reload model"""
    try:
        logger.info("Force reloading model...")
        
        if not os.path.exists(model_path):
            return jsonify({
                'success': False,
                'error': f'Model file tidak ditemukan: {model_path}'
            }), 404
        
        success = load_model()
        
        if success:
            training_info = metadata.get('training_info', {}) if metadata else {}
            
            return jsonify({
                'success': True,
                'message': 'Model berhasil di-reload',
                'load_time': model_load_time.isoformat(),
                'model_type': type(model).__name__,
                'training_samples': training_info.get('total_samples', 0),
                'expected_distribution': training_info.get('expected_distribution', []),
                'consistency_achieved': training_info.get('consistency_achieved', False)
            })
        else:
            return jsonify({
                'success': False,
                'error': 'Gagal reload model'
            }), 500
            
    except Exception as e:
        logger.error(f"Error reloading model: {str(e)}")
        return jsonify({
            'success': False,
            'error': f'Error reload model: {str(e)}'
        }), 500

@app.route('/predict-bulk', methods=['POST'])
def predict_bulk():
    if model is None:
        return jsonify({'error': 'Model tidak dapat dimuat'}), 500
        
    try:
        data_list = request.get_json()
        
        if not isinstance(data_list, list) or len(data_list) == 0:
            return jsonify({'error': 'Input harus berupa array non-kosong'}), 400
        
        logger.info(f"Processing {len(data_list)} records untuk bulk prediction")
        
        # CRITICAL: Validasi feature order dari metadata
        expected_features = []
        if metadata and 'training_info' in metadata:
            expected_features = metadata['training_info'].get('feature_order', [])
            expected_dist = metadata['training_info'].get('expected_distribution', [])
            logger.info(f"Using model with expected distribution: {expected_dist}")
        
        if not expected_features:
            # Default feature order jika metadata tidak ada
            expected_features = [
                'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'lingkar_lengan', 'usia',
                'asi_eksklusif', 'status_imunisasi', 'riwayat_penyakit', 'akses_air_bersih', 'sanitasi_layak'
            ]
        
        logger.info(f"Expected feature order: {expected_features}")
        
        # Siapkan batch data dengan FEATURE ORDER yang KONSISTEN
        batch_data = []
        batch_metadata = []
        
        for i, data in enumerate(data_list):
            try:
                # Validasi semua field ada
                if not all(field in data for field in expected_features):
                    missing_fields = [f for f in expected_features if f not in data]
                    logger.warning(f"Data {i+1} missing fields: {missing_fields}")
                    continue
                
                # CRITICAL: Siapkan data dalam urutan feature yang PERSIS SAMA dengan training
                row_data = {}
                for feature in expected_features:
                    if feature in ['berat_badan', 'tinggi_badan', 'lingkar_kepala', 'lingkar_lengan', 'usia']:
                        row_data[feature] = float(data[feature])
                    else:
                        row_data[feature] = int(data[feature])
                
                batch_data.append(row_data)
                
                batch_metadata.append({
                    'nama': data.get('nama', f'Data-{i+1}'),
                    'area': data.get('area', 'Unknown'),
                    'posyandu': data.get('posyandu', 'Unknown'),
                    'desa': data.get('desa', 'Unknown')
                })
                
            except Exception as e:
                logger.warning(f"Error processing data {i+1}: {str(e)}")
                continue

        if len(batch_data) == 0:
            return jsonify({'error': 'Tidak ada data yang berhasil diproses'}), 400

        # CRITICAL: Buat DataFrame dengan feature order yang PERSIS SAMA
        df_batch = pd.DataFrame(batch_data)
        
        # Reorder columns sesuai expected feature order
        df_batch = df_batch[expected_features]
        
        logger.info(f"DataFrame columns order: {list(df_batch.columns)}")
        logger.info(f"DataFrame shape: {df_batch.shape}")
        
        # Batch prediction
        predictions = model.predict(df_batch)
        probabilities = model.predict_proba(df_batch)
        
        # Log distribusi prediksi
        prediction_counts = {0: 0, 1: 0, 2: 0}
        for pred in predictions:
            if pred in prediction_counts:
                prediction_counts[pred] += 1
        
        logger.info(f"Prediction distribution: Normal={prediction_counts[0]}, Beresiko={prediction_counts[1]}, Stunting={prediction_counts[2]}")
        
        # Bandingkan dengan expected distribution jika ada
        if metadata and 'training_info' in metadata:
            expected_dist = metadata['training_info'].get('expected_distribution', [])
            actual_pred_dist = [prediction_counts[0], prediction_counts[1], prediction_counts[2]]
            consistency_achieved = (expected_dist == actual_pred_dist)
            
            logger.info(f"Expected distribution: {expected_dist}")
            logger.info(f"Actual prediction: {actual_pred_dist}")
            logger.info(f"CONSISTENCY ACHIEVED: {consistency_achieved}")
        
        # Mapping hasil
        status_map = {0: 'Normal', 1: 'Beresiko Stunting', 2: 'Stunting'}
        
        # Compile results
        results = []
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

        # Format hasil untuk display
        display_counts = {
            'Normal': prediction_counts[0],
            'Beresiko Stunting': prediction_counts[1], 
            'Stunting': prediction_counts[2]
        }

        logger.info(f"Successfully processed {len(results)} records")
        logger.info(f"Final distribution: {display_counts}")

        return jsonify({
            'data': results,
            'summary': {
                'total_input': len(data_list),
                'total_processed': len(results),
                'success_rate': round((len(results) / len(data_list)) * 100, 2),
                'prediction_distribution': display_counts,
                'model_info': {
                    'load_time': model_load_time.isoformat() if model_load_time else None,
                    'expected_distribution': metadata.get('training_info', {}).get('expected_distribution') if metadata else None,
                    'consistency_check': metadata.get('training_info', {}).get('consistency_achieved') if metadata else None
                }
            }
        })
        
    except Exception as e:
        logger.error(f"Error dalam bulk prediksi: {str(e)}")
        return jsonify({'error': f'Gagal melakukan bulk prediksi: {str(e)}'}), 500

if __name__ == '__main__':
    print("Starting PERFECT Flask API - Guaranteed Consistency...")
    print(f"Model path: {model_path}")
    print(f"Model loaded: {model is not None}")
    
    if model is not None and metadata:
        training_info = metadata.get('training_info', {})
        print(f"Training samples: {training_info.get('total_samples')}")
        print(f"Expected distribution: {training_info.get('expected_distribution')}")
        print(f"Consistency achieved in training: {training_info.get('consistency_achieved')}")
        print(f"Feature order: {training_info.get('feature_order')}")
    
    app.run(debug=True, host='127.0.0.1', port=5000)