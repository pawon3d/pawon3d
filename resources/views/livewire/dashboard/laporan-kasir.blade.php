<div>
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Laporan Kasir</h2>
            <button class="px-4 py-2 bg-gray-800 text-white rounded-lg">Cetak Informasi</button>
        </div>

        <div class="flex flex-wrap gap-2">
            <button
                class="px-4 py-2 rounded-lg border {{ $selectedMethod == 'semua' ? 'bg-[#74512D] text-white' : 'bg-white' }}"
                wire:click="$set('selectedMethod', 'semua')">Semua</button>
            <button
                class="px-4 py-2 rounded-lg border {{ $selectedMethod == 'pesanan-reguler' ? 'bg-[#74512D] text-white' : 'bg-white' }}"
                wire:click="$set('selectedMethod', 'pesanan-reguler')">Pesanan Reguler</button>
            <button
                class="px-4 py-2 rounded-lg border {{ $selectedMethod == 'pesanan-kotak' ? 'bg-[#74512D] text-white' : 'bg-white' }}"
                wire:click="$set('selectedMethod', 'pesanan-kotak')">Pesanan Kotak</button>
            <button
                class="px-4 py-2 rounded-lg border {{ $selectedMethod == 'siap-beli' ? 'bg-[#74512D] text-white' : 'bg-white' }}"
                wire:click="$set('selectedMethod', 'siap-beli')">Siap Saji</button>

            <select wire:model="selectedMethod" class="ml-auto border rounded-lg bg-white">
                <option value="semua">Semua Kasir</option>
                <!-- Tambahkan kasir jika ingin disaring per user -->
            </select>

            <select wire:model.live="selectedYear" class="border rounded-lg bg-white">
                @for ($i = now()->year; $i >= now()->year - 5; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-lg shadow bg-white flex flex-col">
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <p class="text-sm">Sesi Penjualan</p>
                        <h3 class="text-xl font-bold">{{ $sessionCount }}</h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="cashier" class="text-[#666666]" />
                    </div>
                </div>
                <p class="text-xs {{ $diffStats['sessionCount']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diffStats['sessionCount']['percentage'] }}%
                    ({{ $diffStats['sessionCount']['diff'] > 0 ? '+' : '' }}{{ $diffStats['sessionCount']['diff'] }})
                </p>
            </div>
            <div class="p-4 rounded-lg shadow bg-white flex flex-col">
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <p class="text-sm">Transaksi</p>
                        <h3 class="text-xl font-bold">{{ number_format($transactionCount, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="archive-box" variant="solid" class="text-[#666666]" />
                    </div>
                </div>
                <p
                    class="text-xs {{ $diffStats['transactionCount']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diffStats['transactionCount']['percentage'] }}%
                    ({{ $diffStats['transactionCount']['diff'] > 0 ? '+' : '' }}{{ $diffStats['transactionCount']['diff'] }})
                </p>
            </div>
            <div class="p-4 rounded-lg shadow bg-white flex flex-col">
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <p class="text-sm">Pelanggan Baru</p>
                        <h3 class="text-xl font-bold">{{ $customerCount }} orang</h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="user" variant="solid" class="text-[#666666]" />
                    </div>
                </div>
                <p class="text-xs {{ $diffStats['customerCount']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diffStats['customerCount']['percentage'] }}%
                    ({{ $diffStats['customerCount']['diff'] > 0 ? '+' : '' }}{{ $diffStats['customerCount']['diff'] }})
                </p>
            </div>
        </div>

        <div class="p-4 rounded-lg shadow bg-white flex flex-col">
            <div class="flex flex-row justify-between items-center">
                <div>
                    <p class="text-sm">Produk Terjual</p>
                    <h3 class="text-2xl font-bold">{{ number_format($productSold, 0, ',', '.') }} pcs</h3>
                </div>
                <div class="bg-gray-200 p-2 rounded-xl">
                    <flux:icon icon="cube" variant="solid" class="text-[#666666]" />
                </div>
            </div>
            <p class="text-xs {{ $diffStats['productSold']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                {{ $diffStats['productSold']['percentage'] }}%
                ({{ $diffStats['productSold']['diff'] > 0 ? '+' : '' }}{{ $diffStats['productSold']['diff'] }})
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 p-4 bg-white rounded-lg shadow h-full">
                <p class="font-semibold mb-4">10 Produk Terlaris</p>
                <canvas id="topProductsChart"></canvas>
            </div>

            <div class="h-full gap-2 flex flex-col md:col-span-1">
                <div class="p-4 bg-white rounded-lg shadow h-1/2 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm text-gray-500">Produk Terlaris</p>
                            <h3 class="text-lg font-bold">{{ $bestProduct['total'] ?? 0 }} pcs</h3>
                        </div>
                        <div class="bg-gray-200 p-2 rounded-xl">
                            <flux:icon icon="hand-thumb-up" variant="solid" class="text-[#666666]" />
                        </div>
                    </div>
                    <p class="text-sm">{{ $bestProduct['name'] ?? '-' }}</p>
                    <p class="text-xs {{ $diffStats['best']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $diffStats['best']['percentage'] }}%
                        ({{ $diffStats['best']['diff'] > 0 ? '+' : '' }}{{ $diffStats['best']['diff'] }})
                    </p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow h-1/2 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm text-gray-500">Produk Tersepi</p>
                            <h3 class="text-lg font-bold">{{ $worstProduct['total'] ?? 0 }} pcs</h3>
                        </div>
                        <div class="bg-gray-200 p-2 rounded-xl">
                            <flux:icon icon="hand-thumb-down" variant="solid" class="text-[#666666]" />
                        </div>
                    </div>
                    <p class="text-sm">{{ $worstProduct['name'] ?? '-' }}</p>
                    <p class="text-xs {{ $diffStats['worst']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $diffStats['worst']['percentage'] }}%
                        ({{ $diffStats['worst']['diff'] > 0 ? '+' : '' }}{{ $diffStats['worst']['diff'] }})
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-6 bg-white p-4 rounded-lg shadow">
            <p class="font-semibold mb-4">Penjualan Produk</p>
            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="py-2 px-3">Produk</th>
                        <th class="py-2 px-3">Terjual</th>
                        <th class="py-2 px-3">Tidak Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productSales as $item)
                        <tr class="border-b">
                            <td class="py-2 px-3">{{ $item->name }}</td>
                            <td class="py-2 px-3">{{ $item->sold }}</td>
                            <td class="py-2 px-3">{{ $item->unsold }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($totalPages > 1)
                <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                    <div>
                        Menampilkan
                        {{ ($currentPage - 1) * $perPage + 1 }}
                        hingga
                        {{ min($currentPage * $perPage, $totalProductSales) }}
                        dari {{ $totalProductSales }} baris data
                    </div>
                    @php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);

                        if ($currentPage <= 3) {
                            $endPage = min(5, $totalPages);
                        }
                        if ($currentPage >= $totalPages - 2) {
                            $startPage = max(1, $totalPages - 4);
                        }
                    @endphp

                    <div class="flex items-center gap-1">
                        {{-- Tombol Sebelumnya --}}
                        <button wire:click="$set('currentPage', {{ $currentPage - 1 }})"
                            class="px-2 py-1 border rounded-lg hover:bg-gray-100"
                            @if ($currentPage == 1) disabled @endif>
                            &lsaquo;
                        </button>

                        {{-- Tampilkan halaman awal + titik jika jauh --}}
                        @if ($startPage > 1)
                            <button wire:click="$set('currentPage', 1)"
                                class="px-3 py-1 border rounded-lg hover:bg-gray-100 {{ $currentPage === 1 ? 'bg-gray-300 font-bold' : '' }}">
                                1
                            </button>
                            @if ($startPage > 2)
                                <span class="px-2">...</span>
                            @endif
                        @endif

                        {{-- Nomor halaman dinamis --}}
                        @for ($page = $startPage; $page <= $endPage; $page++)
                            <button wire:click="$set('currentPage', {{ $page }})"
                                class="px-3 py-1 border rounded-lg hover:bg-gray-100 {{ $currentPage === $page ? 'bg-gray-300 font-bold' : '' }}">
                                {{ $page }}
                            </button>
                        @endfor

                        {{-- Tampilkan titik + halaman akhir jika jauh --}}
                        @if ($endPage < $totalPages)
                            @if ($endPage < $totalPages - 1)
                                <span class="px-2">...</span>
                            @endif
                            <button wire:click="$set('currentPage', {{ $totalPages }})"
                                class="px-3 py-1 border rounded-lg hover:bg-gray-100 {{ $currentPage === $totalPages ? 'bg-gray-300 font-bold' : '' }}">
                                {{ $totalPages }}
                            </button>
                        @endif

                        {{-- Tombol Selanjutnya --}}
                        <button wire:click="$set('currentPage', {{ $currentPage + 1 }})"
                            class="px-2 py-1 border rounded-lg hover:bg-gray-100"
                            @if ($currentPage == $totalPages) disabled @endif>
                            &rsaquo;
                        </button>

                    </div>
                </div>
            @endif
        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 p-4 bg-white rounded-lg shadow h-full">
                <div class="flex items-center justify-between mb-4">
                    <p class="font-semibold w-full">Grafik Data</p>
                    <flux:select wire:model.live="selectedChart" class="border rounded-lg px-3 py-1">
                        <option value="gross">Pendapatan Kotor</option>
                        <option value="net">Pendapatan Bersih</option>
                    </flux:select>
                </div>
                <canvas id="chartRevenue"></canvas>
            </div>
            <div class="h-full gap-2 flex flex-col md:col-span-1">
                <div class="p-4 bg-white rounded-lg shadow h-1/3 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm text-gray-500">Pendapatan Kotor</p>
                            <h3 class="text-xl font-bold">Rp{{ number_format($grossRevenue, 0, ',', '.') }}</h3>
                        </div>
                        <div class="bg-gray-200 p-2 rounded-xl">
                            <flux:icon icon="banknotes" variant="solid" class="text-[#666666]" />
                        </div>
                    </div>
                    <p class="text-xs text-green-500">
                        +{{ $diffStats['grossRevenue']['percentage'] }}%
                        (Rp{{ number_format($diffStats['grossRevenue']['diff'], 0, ',', '.') }})
                    </p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow h-1/3 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm text-gray-500">Diskon</p>
                            <h3 class="text-xl font-bold">Rp{{ number_format($discountTotal, 0, ',', '.') }}</h3>
                        </div>
                        <div class="bg-gray-200 p-2 rounded-xl">
                            <flux:icon icon="percent-badge" variant="solid" class="text-[#666666]" />
                        </div>
                    </div>
                    <p class="text-xs text-red-500">
                        +{{ $diffStats['discount']['percentage'] }}%
                        (Rp{{ number_format($diffStats['discount']['diff'], 0, ',', '.') }})
                    </p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow h-1/3 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm text-gray-500">Refund</p>
                            <h3 class="text-xl font-bold">Rp{{ number_format($refundTotal, 0, ',', '.') }}</h3>
                        </div>
                        <div class="bg-gray-200 p-2 rounded-xl">
                            <flux:icon icon="receipt-refund" variant="solid" class="text-[#666666]" />
                        </div>
                    </div>
                    <p class="text-xs text-red-500">
                        -{{ $diffStats['refund']['percentage'] }}%
                        (Rp{{ number_format($diffStats['refund']['diff'], 0, ',', '.') }})
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="p-4 bg-white rounded-lg shadow flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="text-sm text-gray-500">Pendapatan Bersih</p>
                        <h3 class="text-xl font-bold">Rp{{ number_format($netRevenue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="money" class="text-[#666666]" />
                    </div>
                </div>
                <p class="text-xs text-green-500">
                    +{{ $diffStats['netRevenue']['percentage'] }}%
                    (Rp{{ number_format($diffStats['netRevenue']['diff'], 0, ',', '.') }})
                </p>
            </div>
            <div class="p-4 bg-white rounded-lg shadow flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="text-sm text-gray-500">Keuntungan</p>
                        <h3 class="text-xl font-bold">Rp{{ number_format($netRevenue - $grossRevenue, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="chart-line" class="text-[#666666]" />
                    </div>
                </div>
                <p class="text-xs text-green-500">
                    +{{ $diffStats['netRevenue']['percentage'] - $diffStats['grossRevenue']['percentage'] }}%
                    (Rp{{ number_format($diffStats['netRevenue']['diff'] - $diffStats['grossRevenue']['diff'], 0, ',', '.') }})
                </p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <div class="p-4 bg-white rounded-lg shadow">
                <p class="text-sm font-semibold mb-2">Metode Pembayaran Teratas</p>
                <canvas id="paymentMethodChart"></canvas>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <p class="text-sm font-semibold mb-2">Metode Penjualan Teratas</p>
                <canvas id="salesMethodChart"></canvas>
            </div>
        </div>
        <div class="mt-6 bg-white p-4 rounded-lg shadow">
            <p class="font-semibold mb-4">Rincian Penjualan</p>
            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="py-2 px-3">Waktu</th>
                        <th class="py-2 px-3">Penjualan</th>
                        <th class="py-2 px-3">Refund</th>
                        <th class="py-2 px-3">Diskon</th>
                        <th class="py-2 px-3">Modal</th>
                        <th class="py-2 px-3">Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($monthlyReports as $month => $data)
                        <tr class="border-b">
                            <td class="py-2 px-3">{{ $month }}</td>
                            <td class="py-2 px-3">Rp{{ number_format($data['penjualan'], 0, ',', '.') }}</td>
                            <td class="py-2 px-3">Rp{{ number_format($data['refund'], 0, ',', '.') }}</td>
                            <td class="py-2 px-3">Rp{{ number_format($data['diskon'], 0, ',', '.') }}</td>
                            <td class="py-2 px-3">Rp{{ number_format($data['modal'], 0, ',', '.') }}</td>
                            <td class="py-2 px-3">Rp{{ number_format($data['keuntungan'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                <div>
                    Menampilkan
                    1
                    hingga
                    12
                    dari 12 baris data
                </div>
                <div class="flex items-center gap-1">
                    {{-- Tombol Sebelumnya --}}
                    <button type="button" class="px-2 py-1 border rounded-lg hover:bg-gray-100" disabled>
                        &lsaquo;
                    </button>

                    <button type="button" class="px-3 py-1 border rounded-lg hover:bg-gray-100 bg-gray-300 font-bold"
                        disabled>
                        1
                    </button>

                    {{-- Tombol Selanjutnya --}}
                    <button type="button" class="px-2 py-1 border rounded-lg hover:bg-gray-100" disabled>
                        &rsaquo;
                    </button>

                </div>
            </div>
        </div>

    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    @endassets

    <script>
        let topProductsChart, paymentChart, salesChart, revenueChart;

        function initTopProductsChart(labels, data) {
            if (topProductsChart) topProductsChart.destroy();

            const ctx = document.getElementById('topProductsChart').getContext('2d');
            topProductsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Produk (Pcs)',
                        data: data,
                        backgroundColor: '#74512D',
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }

        function initPieChart(canvasId, labels, data, chartRef, labelText) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            if (chartRef && chartRef.destroy) {
                chartRef.destroy();
            }

            const ctx = canvas.getContext('2d');
            return new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: labelText,
                        data: data,
                        backgroundColor: [
                            '#FFB100', '#74512D', '#FF6969', '#82A0D8', '#B0D9B1',
                            '#E1AFD1', '#E5E483'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        datalabels: {
                            color: '#fff',
                            formatter: function(value, context) {
                                const label = context.chart.data.labels[context.dataIndex];
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1) + '%';
                                return `${label}\n${percentage}`;
                            },
                            font: {
                                weight: 'bold',
                                size: 14,
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        function initLineChart(canvasId, data) {
            if (revenueChart) revenueChart.destroy();

            const ctx = document.getElementById(canvasId).getContext('2d');
            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
                    ],
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: data,
                        borderColor: '#74512D',
                        backgroundColor: '#F3D7CA88',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }

        window.addEventListener('update-charts', event => {
            const {
                topProductsChartData,
                paymentChartData,
                salesChartData,
                chartRevenue
            } = event.detail[0];

            // console.log('event yang diterima:', event.detail[0]);


            if (topProductsChartData) {
                initTopProductsChart(topProductsChartData.labels, topProductsChartData.data);
            }
            if (paymentChartData) {
                paymentChart = initPieChart('paymentMethodChart', paymentChartData.labels, paymentChartData.data,
                    paymentChart, 'Metode Pembayaran');
            }
            if (salesChartData) {
                salesChart = initPieChart('salesMethodChart', salesChartData.labels, salesChartData.data,
                    salesChart, 'Metode Penjualan');
            }
            if (chartRevenue) {
                initLineChart('chartRevenue', chartRevenue);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi pertama pakai data awal dari Blade
            initTopProductsChart(
                @json($topProductsChartData['labels']),
                @json($topProductsChartData['data']),
            );
            paymentChart = initPieChart(
                'paymentMethodChart',
                @json($paymentChartData['labels']),
                @json($paymentChartData['data']),
                null,
                'Metode Pembayaran'
            );
            salesChart = initPieChart(
                'salesMethodChart',
                @json($salesChartData['labels']),
                @json($salesChartData['data']),
                null,
                'Metode Penjualan'
            );
            initLineChart('chartRevenue', @json($chartRevenue));
        });
    </script>


</div>
