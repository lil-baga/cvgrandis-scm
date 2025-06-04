@extends('layout.master')

@section('title', 'Stock Forecast')

@section('content')
    <div class="pt-20 p-4 sm:ml-0">
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Prediksi Kebutuhan Stok (SARIMA)</h2>
            <form method="GET" action="{{ route('forecast') }}"
                class="mb-6 space-y-4 md:space-y-0 md:flex md:items-end md:space-x-3">
                <div>
                    <label for="stock_id" class="block mb-1 text-sm font-medium text-gray-700">Pilih Barang Stok:</label>
                    <select name="stock_id" id="stock_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-72 p-2.5"
                        required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($stocks as $stock)
                            <option value="{{ $stock->id }}"
                                {{ (string) ($selectedStockId ?? '') === (string) $stock->id ? 'selected' : '' }}>
                                {{ $stock->name }} (Stok: {{ $stock->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="duration" class="block mb-1 text-sm font-medium text-gray-700">Durasi Prediksi:</label>
                    <select name="duration" id="duration"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-48 p-2.5">
                        <option value="7" {{ ($forecastDurationInput ?? 30) == 7 ? 'selected' : '' }}>7 Hari Kedepan
                        </option>
                        <option value="30" {{ ($forecastDurationInput ?? 30) == 30 ? 'selected' : '' }}>30 Hari Kedepan
                        </option>
                        <option value="90" {{ ($forecastDurationInput ?? 30) == 90 ? 'selected' : '' }}>3 Bulan Kedepan
                        </option>
                        <option value="180" {{ ($forecastDurationInput ?? 30) == 180 ? 'selected' : '' }}>6 Bulan Kedepan
                        </option>
                        <option value="365" {{ ($forecastDurationInput ?? 30) == 365 ? 'selected' : '' }}>1 Tahun Kedepan
                        </option>
                    </select>
                </div>
                <div>
                    <label for="frequency" class="block mb-1 text-sm font-medium text-gray-700">Frekuensi Data:</label>
                    <select name="frequency" id="frequency"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-48 p-2.5">
                        <option value="D" {{ ($timeFrequency ?? 'M') == 'D' ? 'selected' : '' }}>Harian</option>
                        <option value="W" {{ ($timeFrequency ?? 'M') == 'W' ? 'selected' : '' }}>Mingguan</option>
                        <option value="M" {{ ($timeFrequency ?? 'M') == 'M' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 h-fit">
                    Buat Prediksi
                </button>
            </form>

            {{-- Tampilkan Error jika Ada --}}
            @if ($errorForecast)
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    <span class="font-medium">Error!</span> {{ $errorForecast }}
                </div>
            @endif

            {{-- Judul Chart jika ada data --}}
            @if ($selectedStockId && !$errorForecast && $historicalData && $forecastData)
                <h3 class="text-xl font-semibold text-gray-700 mb-3">Prediksi untuk:
                    {{ $selectedStockName ?? 'Barang Terpilih' }}</h3>
            @endif

            {{-- Kontainer Chart --}}
            <div
                class="chart-container bg-gray-50 p-4 rounded-lg shadow-inner {{ !$historicalData && !$forecastData && !$errorForecast ? 'hidden' : '' }}">
                <canvas id="forecastChart"></canvas>
            </div>
            {{-- Pesan jika belum ada item dipilih --}}
            @if (!$selectedStockId && !$errorForecast && !$historicalData && !$forecastData)
                <p class="text-center text-gray-500 py-10">Silakan pilih barang stok, durasi, dan frekuensi untuk membuat
                    prediksi.</p>
            @endif
        </div>

        {{-- Ringkasan Forecast --}}
        <div
            class="bg-white p-6 rounded-lg shadow {{ !$historicalData && !$forecastData && !$errorForecast ? 'hidden' : '' }}">
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Ringkasan Prediksi</h3>
            <div class="text-gray-600" id="forecastSummaryText">
                @if ($forecastData && isset($forecastData['data']) && $historicalData && isset($historicalData['data']))
                    @php
                        $totalForecasted = array_sum($forecastData['data']);
                        $averageForecasted =
                            count($forecastData['data']) > 0 ? $totalForecasted / count($forecastData['data']) : 0;
                        $freqText = '';
                        switch (strtolower($timeFrequency ?? 'm')) {
                            case 'd':
                                $freqText = 'harian';
                                break;
                            case 'w':
                                $freqText = 'mingguan';
                                break;
                            case 'm':
                                $freqText = 'bulanan';
                                break;
                            default:
                                $freqText = 'per periode';
                                break;
                        }
                        $durationDaysText = $forecastDurationInput ?? 30;
                        $actualStepsText = count($forecastData['data']);
                    @endphp
                    Berdasarkan periode prediksi <strong>{{ $durationDaysText }} hari kedepan</strong> (sekitar
                    {{ $actualStepsText }} langkah {{ $freqText }}) untuk
                    <strong>{{ $selectedStockName ?? 'N/A' }}</strong>:
                    <ul class="list-disc list-inside mt-2 text-gray-500">
                        <li>Total prediksi pengurangan: <strong>{{ $totalForecasted }}</strong> unit</li>
                        <li>Rata-rata prediksi pengurangan {{ $freqText }}:
                            <strong>{{ number_format($averageForecasted, 1) }}</strong> unit
                        </li>
                    </ul>
                    <p class="mt-2 text-xs text-gray-400">Catatan: Ini adalah hasil prediksi berdasarkan model SARIMA dan
                        data historis yang tersedia.</p>
                @else
                    Pilih item, durasi, dan frekuensi, lalu klik "Buat Prediksi" untuk melihat ringkasan.
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    {{-- Script untuk merender chart dengan data dari controller --}}
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
                const allLabels = [...new Set([...historicalLabels, ...forecastLabels])]
                    .sort(); // Gabung dan urutkan label unik

                // Map data historis dan forecast ke semua label, isi dengan null jika tidak ada data
                const historicalMappedData = allLabels.map(label => {
                    const index = historicalLabels.indexOf(label);
                    return index !== -1 ? historicalValues[index] : null;
                });

                const forecastMappedData = allLabels.map(label => {
                    const index = forecastLabels.indexOf(label);
                    // Hanya tampilkan data forecast untuk label yang ada di forecastLabels
                    return index !== -1 ? forecastValues[index] : null;
                });


                const historicalDataset = {
                    label: `Penggunaan Aktual: ${currentSelectedStockName}`,
                    data: historicalMappedData, // Gunakan data yang sudah di-map
                    borderColor: 'rgb(59, 130, 246)', // biru
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    fill: false,
                    tension: 0.1,
                    pointRadius: 3,
                };
                
                const forecastDataset = {
                    label: `Prediksi Penggunaan: ${currentSelectedStockName}`,
                    data: forecastMappedData, // Gunakan data yang sudah di-map
                    borderColor: 'rgb(34, 197, 94)', // hijau
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.1,
                    pointRadius: 3,
                };

                const chartData = {
                    labels: allLabels,
                    datasets: [historicalDataset, forecastDataset]
                };

                const config = {
                    type: 'line',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Unit Digunakan'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: `Periode Waktu (${currentFrequency.toUpperCase()})`
                                },
                                ticks: {
                                    // Kurangi jumlah tick jika label terlalu banyak
                                    callback: function(value, index, values) {
                                        if (allLabels.length > 30 && (index % Math.ceil(allLabels.length / 15) !==
                                                0)) { // Tampilkan sekitar 15 label
                                            return null;
                                        }
                                        return allLabels[
                                            value]; // allLabels[value] karena 'value' adalah index dari labels
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    // Kustomisasi tooltip agar tidak menampilkan nilai null
                                    filter: function(tooltipItem) {
                                        return tooltipItem.raw !== null;
                                    }
                                }
                            }
                        }
                    }
                };

                if (forecastChartInstance) {
                    forecastChartInstance.destroy();
                }
                const chartCanvas = document.getElementById('forecastChart');
                if (chartCanvas && (historicalValues.length > 0 || forecastValues.length > 0)) {
                    forecastChartInstance = new Chart(chartCanvas, config);
                } else if (chartCanvas) {
                    const ctx = chartCanvas.getContext('2d');
                    ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
                    if (document.getElementById('stock_id') && document.getElementById('stock_id').value !== '') {
                        ctx.font = "14px Inter";
                        ctx.textAlign = 'center';
                        ctx.fillText('Tidak ada data historis/prediksi yang cukup untuk ditampilkan pada grafik.', chartCanvas
                            .width / 2, chartCanvas.height / 2);
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                // Render chart hanya jika data relevan ada (dikirim dari controller dan bukan error)
                if (typeof historicalValues !== 'undefined' && typeof forecastValues !== 'undefined' && !
                    {{ isset($errorForecast) && $errorForecast ? 'true' : 'false' }}) {
                    renderActualForecastChart();
                } else if (document.getElementById('stock_id') && document.getElementById('stock_id').value === '' && !
                    {{ isset($errorForecast) && $errorForecast ? 'true' : 'false' }}) {
                    // Kosongkan canvas jika belum ada item dipilih dan tidak ada error
                    const chartCanvas = document.getElementById('forecastChart');
                    if (chartCanvas) {
                        const ctx = chartCanvas.getContext('2d');
                        ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
                    }
                }
            });
        </script>
    @endif
    <style>
        .chart-container {
            position: relative;
            height: 60vh;
            width: 100%;
            max-width: 900px;
            margin: auto;
        }
    </style>
@endsection
