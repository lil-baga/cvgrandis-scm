import sys
import json
import pandas as pd
from statsmodels.tsa.statespace.sarimax import SARIMAX
from statsmodels.tools.sm_exceptions import ConvergenceWarning
import warnings

# Abaikan ConvergenceWarning dan UserWarning untuk output yang lebih bersih
warnings.simplefilter('ignore', ConvergenceWarning)
warnings.simplefilter('ignore', UserWarning)

def run_sarima_forecast(json_historical_data, forecast_steps, sarima_order_str, seasonal_order_str, time_frequency):
    try:
        historical_df = pd.read_json(json_historical_data)
        if historical_df.empty:
            return json.dumps({'error': 'No historical data provided for SARIMA.'})

        # Pastikan kolom 'date_period' ada dan 'value' ada
        if 'date_period' not in historical_df.columns or 'value' not in historical_df.columns:
            return json.dumps({'error': "JSON data must contain 'date_period' and 'value' columns."})

        historical_df['date_period'] = pd.to_datetime(historical_df['date_period'])
        historical_df = historical_df.set_index('date_period').sort_index()
        
        # Asumsikan data sudah diagregasi dengan frekuensi yang sesuai oleh Laravel
        # dan 'date_period' sudah merepresentasikan awal/akhir periode tersebut.
        # Kita akan menggunakan asfreq untuk memastikan data series memiliki frekuensi yang konsisten.
        
        pd_freq_map = {'D': 'D', 'W': 'W', 'M': 'MS'} # W bisa W-SUN, W-MON tergantung data
        pd_freq = pd_freq_map.get(time_frequency.upper(), 'D') # Default Harian

        historical_series = historical_df['value'].asfreq(pd_freq)
        historical_series = historical_series.fillna(method='ffill').fillna(0) # Isi NaN

        order = tuple(map(int, sarima_order_str.split(',')))
        seasonal_order_raw = list(map(int, seasonal_order_str.split(',')))
        if len(seasonal_order_raw) != 4:
            return json.dumps({'error': 'Seasonal order must have 4 components (P,D,Q,s).'})
        seasonal_order = tuple(seasonal_order_raw)

        min_data_points = 2 * seasonal_order[3] if seasonal_order[3] > 1 else 15 # Aturan umum
        if len(historical_series) < min_data_points:
            return json.dumps({'error': f'Not enough historical data for SARIMA. Need at least {min_data_points} periods for seasonality {seasonal_order[3]}. Found {len(historical_series)}.'})

        model = SARIMAX(historical_series,
                        order=order,
                        seasonal_order=seasonal_order,
                        enforce_stationarity=False,
                        enforce_invertibility=False,
                        initialization='approximate_diffuse')
        
        results = model.fit(disp=False)
        
        forecast_object = results.get_forecast(steps=int(forecast_steps))
        forecast_values = forecast_object.predicted_mean.round().astype(int).tolist()
        forecast_values = [max(0, val) for val in forecast_values] # Pastikan tidak negatif

        last_date_historical = historical_series.index[-1]
        
        # Penyesuaian untuk DateOffset berdasarkan frekuensi Pandas
        if pd_freq == 'D':
            offset_kwargs = {'days': 1}
        elif pd_freq.startswith('W'): # W, W-SUN, W-MON, dll.
            offset_kwargs = {'weeks': 1}
        elif pd_freq == 'MS' or pd_freq == 'M': # MS untuk awal bulan, M untuk akhir bulan (defaultnya akhir)
            offset_kwargs = {'months': 1}
            pd_freq = 'MS' # Konsistenkan ke awal bulan untuk forecast_index
        else:
            offset_kwargs = {'days': 1} # Fallback

        forecast_index = pd.date_range(
            start=last_date_historical + pd.DateOffset(**offset_kwargs),
            periods=int(forecast_steps), 
            freq=pd_freq
        )
        forecast_dates = [d.strftime('%Y-%m-%d') for d in forecast_index]

        return json.dumps({
            'historical_dates': historical_series.index.strftime('%Y-%m-%d').tolist(),
            'historical_values': historical_series.values.tolist(),
            'forecast_dates': forecast_dates,
            'forecast_values': forecast_values
        })

    except Exception as e:
        import traceback
        return json.dumps({'error': str(e), 'trace': traceback.format_exc(), 'type': type(e).__name__})

if __name__ == '__main__':
    if len(sys.argv) != 6: # Script name + 5 arguments
        print(json.dumps({'error': 'Usage: python sarima_forecaster.py <json_historical_data_string> <forecast_steps> <sarima_order_p,d,q> <seasonal_order_P,D,Q,s> <time_frequency D/W/M>'}))
        sys.exit(1)
    
    json_data_arg = sys.argv[1]
    steps_arg = sys.argv[2]
    sarima_order_arg = sys.argv[3]
    seasonal_order_arg = sys.argv[4]
    time_freq_arg = sys.argv[5]
    
    print(run_sarima_forecast(json_data_arg, steps_arg, sarima_order_arg, seasonal_order_arg, time_freq_arg))