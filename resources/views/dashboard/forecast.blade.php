@extends('layout.master')

@section('title', 'Stock Forecast (LSTM)')
@section('content')
    <div class="pt-20 p-4 sm:ml-0">
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Stock Deduction Forecast (LSTM)</h2>

            <form method="GET" action="{{ route('forecast') }}"
                class="mb-6 space-y-4 md:space-y-0 md:flex md:items-end md:space-x-3">
                <div>
                    <label for="stock_id" class="block mb-1 text-sm font-medium text-gray-700">Select Stock Item:</label>
                    <select name="stock_id" id="stock_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-72 p-2.5"
                        required>
                        <option value="">-- Pilih Barang Stok --</option>
                        @foreach ($stocks as $stock)
                            <option value="{{ $stock->id }}"
                                {{ (string) ($selectedStockId ?? '') === (string) $stock->id ? 'selected' : '' }}>
                                {{ $stock->name }} (Stok: {{ $stock->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="duration" class="block mb-1 text-sm font-medium text-gray-700">Forecast Duration:</label>
                    <select name="duration" id="duration"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-48 p-2.5">
                        <option value="30" {{ ($forecastDurationInput ?? 30) == 30 ? 'selected' : '' }}>Next 30 Days
                        </option>
                        <option value="7" {{ ($forecastDurationInput ?? 30) == 7 ? 'selected' : '' }}>Next 7 Days
                        </option>
                        <option value="90" {{ ($forecastDurationInput ?? 30) == 90 ? 'selected' : '' }}>Next 3 Months
                        </option>
                        <option value="180" {{ ($forecastDurationInput ?? 30) == 180 ? 'selected' : '' }}>Next 6 Months
                        </option>
                        <option value="365" {{ ($forecastDurationInput ?? 30) == 365 ? 'selected' : '' }}>Next 1 Year
                        </option>
                    </select>
                </div>
                <div>
                    <label for="frequency" class="block mb-1 text-sm font-medium text-gray-700">Time Frequency:</label>
                    <select name="frequency" id="frequency"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-48 p-2.5">
                        <option value="D" {{ ($timeFrequency ?? 'M') == 'D' ? 'selected' : '' }}>Daily</option>
                        <option value="W" {{ ($timeFrequency ?? 'M') == 'W' ? 'selected' : '' }}>Weekly</option>
                        <option value="M" {{ ($timeFrequency ?? 'M') == 'M' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 h-fit">
                    Generate Forecast
                </button>
            </form>

            @if ($errorForecast)
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    <span class="font-medium">Error!</span> {{ $errorForecast }}
                </div>
            @endif

            @if ($selectedStockId && !$errorForecast)
                <h3 class="text-xl font-semibold text-gray-700 mb-3">Forecast for:
                    {{ $selectedStockName ?? 'Selected Item' }}</h3>
            @endif

            <div
                class="chart-container bg-gray-50 p-4 rounded-lg shadow-inner {{ !$historicalData && !$forecastData ? 'hidden' : '' }}">
                <canvas id="forecastChart"></canvas>
            </div>
            @if (!$selectedStockId && !$errorForecast && !$historicalData && !$forecastData)
                <p class="text-center text-gray-500 py-10">Please select a stock item, duration, and frequency to generate a
                    forecast.</p>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow {{ !$historicalData && !$forecastData ? 'hidden' : '' }}">
            <h3 class="text-xl font-semibold text-gray-700 mb-3">Forecast Summary</h3>
            <div class="text-gray-600" id="forecastSummaryText">
                @if ($forecastData && isset($forecastData['data']) && $historicalData && isset($historicalData['data']))
                    @php
                        $totalForecasted = array_sum($forecastData['data']);
                        $averageForecasted =
                            count($forecastData['data']) > 0 ? $totalForecasted / count($forecastData['data']) : 0;
                        $freqText =
                            strtolower($timeFrequency ?? 'M') == 'd'
                                ? 'daily'
                                : (strtolower($timeFrequency ?? 'M') == 'w'
                                    ? 'weekly'
                                    : 'monthly');
                    @endphp
                    Based on the <strong>{{ $forecastDurationInput ?? 30 }}-day forecast period</strong> (approx.
                    {{ count($forecastData['data']) }} {{ $freqText }} steps) for
                    <strong>{{ $selectedStockName ?? 'N/A' }}</strong>:
                    <ul class="list-disc list-inside mt-2 text-gray-500">
                        <li>Total forecasted deduction: <strong>{{ $totalForecasted }}</strong> units</li>
                        <li>Average {{ $freqText }} forecasted deduction:
                            <strong>{{ number_format($averageForecasted, 1) }}</strong> units</li>
                    </ul>
                @else
                    Select an item, duration, and frequency, then generate forecast to see the summary.
                @endif
            </div>
        </div>
    </div>

    {{-- Chart.js dan Flowbite sudah di-include di master atau di sini --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    @if (isset($historicalData) && isset($forecastData) && !$errorForecast)
        <script>
            let forecastChartInstance = null;

            // Ambil data dari variabel PHP yang di-encode sebagai JSON oleh controller
            const historicalLabels = @json($historicalData['labels'] ?? []);
            const historicalValues = @json($historicalData['data'] ?? []);
            const forecastLabels = @json($forecastData['labels'] ?? []);
            const forecastValues = @json($forecastData['data'] ?? []);
            const currentSelectedStockName = @json($selectedStockName ?? 'Selected Item');

            function renderActualForecastChart() {
                const allLabels = [...historicalLabels, ...forecastLabels];

                const historicalDataset = {
                    label: `Actual Deductions: ${currentSelectedStockName}`,
                    data: historicalValues,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: false,
                    tension: 0.1,
                    pointRadius: 3,
                };

                const forecastDataset = {
                    label: `Forecasted Deductions: ${currentSelectedStockName}`,
                    // Pad data forecast dengan null sebanyak data historis agar dimulai setelahnya
                    data: [...Array(historicalValues.length).fill(null), ...forecastValues],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
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
                                    text: 'Units Deducted'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time Period'
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
                                intersect: false
                            }
                        }
                    }
                };

                if (forecastChartInstance) {
                    forecastChartInstance.destroy();
                }
                const chartCanvas = document.getElementById('forecastChart');
                if (chartCanvas && (historicalLabels.length > 0 || forecastLabels.length > 0)) { // Hanya render jika ada data
                    forecastChartInstance = new Chart(chartCanvas, config);
                } else if (chartCanvas) {
                    // Kosongkan canvas jika tidak ada data
                    const ctx = chartCanvas.getContext('2d');
                    ctx.clearRect(0, 0, chartCanvas.width, chartCanvas.height);
                    if (document.getElementById('selectedStockId') && document.getElementById('selectedStockId').value !== '') {
                        ctx.textAlign = 'center';
                        ctx.fillText('No data available to display chart for the selected item.', chartCanvas.width / 2,
                            chartCanvas.height / 2);
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                // Render chart hanya jika data relevan ada (dikirim dari controller)
                if (typeof historicalLabels !== 'undefined' && typeof forecastLabels !== 'undefined') {
                    renderActualForecastChart();
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
