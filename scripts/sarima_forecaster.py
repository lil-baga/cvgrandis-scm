import sys
import json
import pandas as pd
from statsmodels.tsa.statespace.sarimax import SARIMAX
from statsmodels.tools.sm_exceptions import ConvergenceWarning
import warnings

# Abaikan ConvergenceWarning untuk output yang lebih bersih di produksi, tapi perhatikan saat development
warnings.simplefilter('ignore', ConvergenceWarning)
warnings.simplefilter('ignore', UserWarning) # Juga untuk UserWarning dari statsmodels

def run_forecast(json_historical_data, forecast_steps, sarima_order_str, seasonal_order_str, time_frequency):
    try:
        # Load data historis (dikirim sebagai JSON string dari PHP)
        # Format: [{"date": "YYYY-MM-DD", "value": X}, {"date": "YYYY-MM-DD", "value": Y}, ...]
        # atau [{"time_period": "YYYY-WW", "value": X}, ...] jika mingguan
        historical_df = pd.read_json(json_historical_data)
        if historical_df.empty:
            return json.dumps({'error': 'No historical data provided.'})

        historical_df['date_period'] = pd.to_datetime(historical_df['date_period'])
        historical_df = historical_df.set_index('date_period').sort_index()
        
        # Pastikan ada frekuensi jika memungkinkan (misalnya 'W' untuk mingguan, 'D' untuk harian, 'MS' untuk awal bulan)
        # Jika tidak ada frekuensi alami, SARIMA mungkin kurang optimal atau butuh resampling.
        # Untuk contoh ini, kita asumsikan data sudah diagregasi dengan frekuensi yang sesuai.
        # Jika time_frequency adalah 'W' atau 'M', Pandas bisa menanganinya.
        if time_frequency and time_frequency in ['D', 'W', 'M', 'MS']:
            historical_series = historical_df['value'].asfreq(time_frequency.upper())
        else: # Jika tidak ada frekuensi atau tidak dikenal, coba infer atau biarkan tanpa frekuensi eksplisit
            historical_series = historical_df['value']
        
        # Isi nilai NaN jika ada setelah asfreq (misalnya dengan forward fill atau interpolasi)
        historical_series = historical_series.fillna(method='ffill').fillna(0) # Isi NaN dengan 0 setelah ffill

        # Konversi order dari string "p,d,q" menjadi tuple (p,d,q)
        order = tuple(map(int, sarima_order_str.split(',')))
        seasonal_order_raw = list(map(int, seasonal_order_str.split(',')))
        if len(seasonal_order_raw) != 4:
            return json.dumps({'error': 'Seasonal order must have 4 components (P,D,Q,s).'})
        seasonal_order = tuple(seasonal_order_raw)

        # Minimal data untuk SARIMA (aturan umum: 2 * musim)
        min_data_points = 2 * seasonal_order[3] if seasonal_order[3] > 1 else 20 # Contoh
        if len(historical_series) < min_data_points:
            return json.dumps({'error': f'Not enough historical data. Need at least {min_data_points} data points for SARIMA with seasonality {seasonal_order[3]}. Found {len(historical_series)}.'})

        # Buat dan latih model SARIMA
        model = SARIMAX(historical_series,
                        order=order,
                        seasonal_order=seasonal_order,
                        enforce_stationarity=False,
                        enforce_invertibility=False,
                        initialization='approximate_diffuse') # Coba inisialisasi berbeda jika ada error
        
        results = model.fit(disp=False) # disp=False untuk tidak menampilkan output konvergensi

        # Lakukan forecast
        forecast_object = results.get_forecast(steps=int(forecast_steps))
        forecast_values = forecast_object.predicted_mean.round().astype(int).tolist() # Bulatkan dan jadikan integer
        
        # Buat label tanggal untuk forecast
        last_date = historical_series.index[-1]
        if time_frequency == 'W':
            forecast_index = pd.date_range(start=last_date + pd.DateOffset(weeks=1), periods=int(forecast_steps), freq='W-SUN') # Contoh: Minggu akhir pekan
        elif time_frequency in ['M', 'MS']:
            forecast_index = pd.date_range(start=last_date + pd.DateOffset(months=1), periods=int(forecast_steps), freq='MS') # Awal bulan
        else: # Default harian
            forecast_index = pd.date_range(start=last_date + pd.Timedelta(days=1), periods=int(forecast_steps), freq='D')
        
        forecast_dates = [d.strftime('%Y-%m-%d') for d in forecast_index]

        return json.dumps({
            'historical_dates': historical_series.index.strftime('%Y-%m-%d').tolist(),
            'historical_values': historical_series.values.tolist(),
            'forecast_dates': forecast_dates,
            'forecast_values': forecast_values
        })

    except Exception as e:
        return json.dumps({'error': str(e), 'type': type(e).__name__})

if __name__ == '__main__':
    if len(sys.argv) < 6:
        print(json.dumps({'error': 'Usage: python sarima_forecaster.py <json_historical_data_string> <forecast_steps> <sarima_order_p,d,q> <seasonal_order_P,D,Q,s> <time_frequency D/W/M>'}))
        sys.exit(1)
    
    json_data_arg = sys.argv[1]
    steps_arg = sys.argv[2]
    sarima_order_arg = sys.argv[3]
    seasonal_order_arg = sys.argv[4]
    time_freq_arg = sys.argv[5]
    
    print(run_forecast(json_data_arg, steps_arg, sarima_order_arg, seasonal_order_arg, time_freq_arg))