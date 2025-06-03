import sys
import json
import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense
import warnings

# Abaikan beberapa peringatan umum dari TensorFlow/Keras
warnings.simplefilter('ignore', FutureWarning)
warnings.simplefilter('ignore', UserWarning)


def create_sequences(data, sequence_length):
    xs, ys = [], []
    for i in range(len(data) - sequence_length):
        x = data[i:(i + sequence_length)]
        y = data[i + sequence_length]
        xs.append(x)
        ys.append(y)
    return np.array(xs), np.array(ys)

def run_lstm_forecast(json_historical_data, forecast_steps, time_frequency, sequence_length=12, epochs=50, batch_size=1):
    try:
        historical_df = pd.read_json(json_historical_data)
        if historical_df.empty or len(historical_df) < sequence_length + 5: # Butuh data minimal
            return json.dumps({'error': f'Not enough historical data. Need at least {sequence_length + 5} data points. Found {len(historical_df)}.'})

        historical_df['date_period'] = pd.to_datetime(historical_df['date_period'])
        historical_df = historical_df.set_index('date_period').sort_index()
        
        # Gunakan kolom 'value' untuk forecasting
        time_series_data = historical_df['value'].values.astype(float)

        # 1. Normalisasi Data
        scaler = MinMaxScaler(feature_range=(0, 1))
        scaled_data = scaler.fit_transform(time_series_data.reshape(-1, 1))

        # 2. Buat Sekuens untuk LSTM
        X, y = create_sequences(scaled_data, sequence_length)
        if X.shape[0] == 0: # Tidak cukup data untuk membuat sekuens
            return json.dumps({'error': f'Not enough data to create sequences with length {sequence_length}. Found {len(scaled_data)} after scaling.'})

        # Reshape input menjadi [samples, time steps, features]
        X = X.reshape((X.shape[0], X.shape[1], 1))

        # 3. Definisikan Model LSTM Sederhana
        model = Sequential()
        model.add(LSTM(units=50, activation='relu', input_shape=(sequence_length, 1))) # units=50 bisa disesuaikan
        model.add(Dense(units=1))
        model.compile(optimizer='adam', loss='mean_squared_error')

        # 4. Latih Model
        # Untuk data nyata, verbose=0 agar tidak banyak output. Untuk debug, bisa verbose=1
        model.fit(X, y, epochs=int(epochs), batch_size=int(batch_size), verbose=0)

        # 5. Lakukan Forecasting
        last_sequence = scaled_data[-sequence_length:] # Ambil sekuens terakhir dari data historis
        current_batch = last_sequence.reshape((1, sequence_length, 1))
        forecast_predictions_scaled = []

        for _ in range(int(forecast_steps)):
            current_pred = model.predict(current_batch, verbose=0)[0]
            forecast_predictions_scaled.append(current_pred)
            # Update batch: buang nilai pertama, tambahkan prediksi baru di akhir
            current_batch = np.append(current_batch[:, 1:, :], [[current_pred]], axis=1)
        
        # 6. Inverse Transform Prediksi
        forecast_predictions = scaler.inverse_transform(np.array(forecast_predictions_scaled).reshape(-1,1))
        forecast_values = np.maximum(0, forecast_predictions.round().astype(int)).flatten().tolist() # Pastikan non-negatif & integer

        # 7. Buat Label Tanggal untuk Forecast
        last_date_historical = historical_df.index[-1]
        freq_map = {'D': 'D', 'W': 'W-SUN', 'M': 'MS'} # Mapping ke frekuensi Pandas
        pd_freq = freq_map.get(time_frequency.upper(), 'D') # Default ke Harian

        forecast_index = pd.date_range(
            start=last_date_historical + pd.DateOffset(**{pd_freq.lower().replace('-', '').replace('sun','s'): 1}), # pd.DateOffset(days=1), weeks=1, months=1
            periods=int(forecast_steps), 
            freq=pd_freq
        )
        forecast_dates = [d.strftime('%Y-%m-%d') for d in forecast_index]
        
        return json.dumps({
            'historical_dates': historical_df.index.strftime('%Y-%m-%d').tolist(),
            'historical_values': time_series_data.tolist(), # Kirim data asli, bukan yang di-scale
            'forecast_dates': forecast_dates,
            'forecast_values': forecast_values
        })

    except Exception as e:
        import traceback
        return json.dumps({'error': str(e), 'trace': traceback.format_exc(), 'type': type(e).__name__})

if __name__ == '__main__':
    if len(sys.argv) < 4: # json_data, steps, frequency. Lainnya bisa default.
        print(json.dumps({'error': 'Usage: python lstm_forecaster.py <json_historical_data_string> <forecast_steps> <time_frequency D/W/M> [sequence_length] [epochs] [batch_size]'}))
        sys.exit(1)
    
    json_data_arg = sys.argv[1]
    steps_arg = sys.argv[2]
    time_freq_arg = sys.argv[3]
    seq_len_arg = sys.argv[4] if len(sys.argv) > 4 else 12 # Default sequence length
    epochs_arg = sys.argv[5] if len(sys.argv) > 5 else 50    # Default epochs
    batch_arg = sys.argv[6] if len(sys.argv) > 6 else 1      # Default batch size
    
    print(run_lstm_forecast(json_data_arg, steps_arg, time_freq_arg, seq_len_arg, epochs_arg, batch_arg))