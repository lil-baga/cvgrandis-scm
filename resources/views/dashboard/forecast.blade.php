@extends('layout.master')

@section('title', 'Stock Forecast')

@section('content')
    <div class="pt-20 p-4 sm:p-6"> {{-- Padding disesuaikan --}}
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg">
            {{-- Header dan Form digabungkan agar lebih compact --}}
            <div class="mb-4 pb-4 border-b border-gray-200">
                <form method="GET" action="{{ route('forecast') }}" class="w-full">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        {{-- Judul dan Pemilihan Barang di satu kolom besar --}}
                        <div class="md:col-span-2">
                            <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-2">Prediksi Kebutuhan Stok</h2>
                            <label for="stock_id" class="sr-only">Pilih Barang Stok:</label>
                            <select name="stock_id" id="stock_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                <option value="">-- Pilih Barang Stok untuk Prediksi --</option>
                                @foreach ($stocks as $stock)
                                    <option value="{{ $stock->id }}" {{ (string)($selectedStockId ?? '') === (string)$stock->id ? 'selected' : '' }}>
                                        {{ $stock->name }} (Stok: {{ $stock->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Lain dan Tombol Submit di kolom terpisah --}}
                        <div>
                            <label for="duration" class="block mb-1 text-xs font-medium text-gray-700">Durasi</label>
                            <select name="duration" id="duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="7" {{ ($forecastDurationInput ?? 7) == 7 ? 'selected' : '' }}>7 Hari</option>
                                <option value="30" {{ ($forecastDurationInput ?? 7) == 30 ? 'selected' : '' }}>30 Hari</option>
                                <option value="90" {{ ($forecastDurationInput ?? 7) == 90 ? 'selected' : '' }}>3 Bulan</option>
                                <option value="180" {{ ($forecastDurationInput ?? 7) == 180 ? 'selected' : '' }}>6 Bulan</option>
                                <option value="365" {{ ($forecastDurationInput ?? 7) == 365 ? 'selected' : '' }}>1 Tahun</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <div class="flex-grow hidden">
                                <label for="frequency" class="block mb-1 text-xs font-medium text-gray-700">Frekuensi</label>
                                <select name="frequency" id="frequency" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option selected value="D" {{ ($timeFrequency ?? 'D') == 'D' ? 'selected' : '' }}>Harian</option>
                                </select>
                            </div>
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 h-fit self-end">
                                Buat Prediksi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            {{-- Konten Utama (Error, Chart, Summary) --}}
            <div class="mt-4">
                @if ($errorForecast)
                    <div class="p-4 text-sm text-red-700 bg-red-100 rounded-lg text-center" role="alert">
                        <span class="font-medium">Gagal Membuat Prediksi!</span> {{ $errorForecast }}
                    </div>
                @elseif (!$selectedStockId)
                    <div class="text-center text-gray-500 py-16">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5z"/></svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Data</h3>
                        <p class="mt-1 text-sm text-gray-500">Pilih barang stok untuk memulai membuat prediksi.</p>
                    </div>
                @else
                    {{-- Judul Chart & Ringkasan disatukan di sini --}}
                    <div class="mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Hasil Prediksi untuk: {{ $selectedStockName ?? 'Barang Terpilih' }}</h3>
                        <div class="text-sm text-gray-600" id="forecastSummaryText">
                            @if ($forecastData && isset($forecastData['data']))
                                @php
                                    $totalForecasted = array_sum($forecastData['data']);
                                    $averageForecasted = count($forecastData['data']) > 0 ? $totalForecasted / count($forecastData['data']) : 0;
                                    $freqText = strtolower($timeFrequency ?? 'm') == 'd' ? 'harian' : (strtolower($timeFrequency ?? 'm') == 'w' ? 'mingguan' : 'bulanan');
                                @endphp
                                Prediksi total pengurangan <strong>{{ $totalForecasted }} unit</strong> dalam {{ count($forecastData['data']) }} periode {{ $freqText }} kedepan.
                            @endif
                        </div>
                    </div>

                    {{-- Kontainer Chart --}}
                    <div class="h-full chart-container bg-gray-50 p-2 sm:p-4 rounded-lg shadow-inner">
                        <canvas id="forecastChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    @if (isset($historicalData) && isset($forecastData) && !$errorForecast)
    <script>
        let forecastChartInstance = null; 
        const historicalLabels = @json($historicalData['labels'] ?? []);
        const historicalValues = @json($historicalData['data'] ?? []);
        const forecastLabels = @json($forecastData['labels'] ?? []);
        const forecastValues = @json($forecastData['data'] ?? []);
        const currentSelectedStockName = @json($selectedStockName ?? 'Barang Terpilih');
        const currentFrequency = @json($timeFrequency ?? 'Periode');

        function renderActualForecastChart() {
            const allLabels = [...new Set([...historicalLabels, ...forecastLabels])].sort();
            const historicalMappedData = allLabels.map(label => {
                const index = historicalLabels.indexOf(label);
                return index !== -1 ? historicalValues[index] : null;
            });
            const forecastMappedData = allLabels.map(label => {
                const index = forecastLabels.indexOf(label);
                return index !== -1 ? forecastValues[index] : null;
            });

            // Menyambungkan garis forecast dengan historis
            if (historicalMappedData.length > 0 && forecastMappedData.length > 0) {
                 const lastHistoricalIndex = historicalMappedData.findLastIndex(v => v !== null);
                 if(lastHistoricalIndex !== -1) {
                     forecastMappedData[lastHistoricalIndex] = historicalMappedData[lastHistoricalIndex];
                 }
            }

            const historicalDataset = {
                label: `Penggunaan Aktual`,
                data: historicalMappedData,
                borderColor: 'rgb(59, 130, 246)', 
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: false,
                tension: 0.2,
                pointRadius: 3,
                pointHoverRadius: 5,
            };
            
            const forecastDataset = {
                label: `Prediksi Penggunaan`,
                data: forecastMappedData, 
                borderColor: 'rgb(234, 88, 12)', // oranye
                backgroundColor: 'rgba(234, 88, 12, 0.1)',
                borderDash: [5, 5], 
                fill: false,
                tension: 0.2,
                pointRadius: 3,
                pointHoverRadius: 5,
            };
            
            const chartData = { labels: allLabels, datasets: [historicalDataset, forecastDataset] };

            const config = {
                type: 'line', data: chartData,
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Jumlah Unit Digunakan' } },
                        x: { title: { display: true, text: `Periode Waktu (${currentFrequency})` },
                            ticks: {
                                maxRotation: 45, minRotation: 45,
                                callback: function(value, index, values) {
                                    if (allLabels.length > 30 && (index % Math.ceil(allLabels.length / 15) !== 0)) return null;
                                    return allLabels[value];
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: { mode: 'index', intersect: false, filter: (item) => item.raw !== null }
                    }
                }
            };
            if (forecastChartInstance) forecastChartInstance.destroy();
            const chartCanvas = document.getElementById('forecastChart');
            if (chartCanvas) forecastChartInstance = new Chart(chartCanvas, config);
        }

        document.addEventListener('DOMContentLoaded', renderActualForecastChart);
    </script>
    @endif
    <style>
        .chart-container { position: relative; height: 50vh; width: 100%; } /* Tinggi chart disesuaikan */
    </style>
@endsection