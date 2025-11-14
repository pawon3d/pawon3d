<div>
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Laporan Produksi</h2>
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
                <option value="semua">Semua Produksi</option>
                <!-- Tambahkan kasir jika ingin disaring per user -->
            </select>

            <select wire:model.live="selectedYear" class="border rounded-lg bg-white">
                @for ($i = now()->year; $i >= now()->year - 5; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="flex items-center border bg-white shadow-lg rounded-lg p-4">
            <flux:icon icon="message-square-warning" class="size-16" />
            <div class="ml-3">
                <p class="mt-1 text-sm text-gray-500">
                    Lorem ipsum dolor sit amet consectetur. Sed pharetra netus gravida non curabitur fermentum etiam.
                    Lorem orci auctor adipiscing vel blandit. In in integer viverra proin risus eu eleifend.
                </p>
            </div>
        </div>
        <div class="p-4 rounded-lg shadow bg-white flex flex-col">
            <div class="flex flex-row justify-between items-center">
                <div>
                    <p class="text-sm">Produksi Produk</p>
                    <h3 class="text-2xl font-bold">{{ number_format($totalProduction, 0, ',', '.') }} pcs</h3>
                </div>
                <div class="bg-gray-200 p-2 rounded-xl">
                    <flux:icon icon="cube" variant="solid" class="text-[#666666]" />
                </div>
            </div>
            <p class="text-xs {{ $diffStats['totalProduction']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                {{ $diffStats['totalProduction']['percentage'] }}%
                ({{ $diffStats['totalProduction']['diff'] > 0 ? '+' : '' }}{{ $diffStats['totalProduction']['diff'] }})
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-lg shadow bg-white flex flex-col">
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <p class="text-sm">Produksi Berhasil</p>
                        <h3 class="text-xl font-bold">{{ number_format($successProduction, 0, ',', '.') }} pcs</h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="check-circle" variant="solid" class="text-[#666666]" />
                    </div>
                </div>
                <p
                    class="text-xs {{ $diffStats['successProduction']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diffStats['successProduction']['percentage'] }}%
                    ({{ $diffStats['successProduction']['diff'] > 0 ? '+' : '' }}{{ $diffStats['successProduction']['diff'] }})
                </p>
            </div>
            <div class="p-4 rounded-lg shadow bg-white flex flex-col">
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <p class="text-sm">Produksi Gagal</p>
                        <h3 class="text-xl font-bold">{{ number_format($failedProduction, 0, ',', '.') }} pcs</h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="x-circle" variant="solid" class="text-[#666666]" />
                    </div>
                </div>
                <p
                    class="text-xs {{ $diffStats['failedProduction']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diffStats['failedProduction']['percentage'] }}%
                    ({{ $diffStats['failedProduction']['diff'] > 0 ? '+' : '' }}{{ $diffStats['failedProduction']['diff'] }})
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 p-4 bg-white rounded-lg shadow h-full">
                <p class="font-semibold mb-4">10 Produksi Tertinggi</p>
                <canvas id="topProductionsChart"></canvas>
            </div>

            <div class="h-full gap-2 flex flex-col md:col-span-1">
                <div class="p-4 bg-white rounded-lg shadow h-1/2 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm text-gray-500">Produksi Tertinggi</p>
                            <h3 class="text-lg font-bold">
                                {{ number_format($bestProduction['total'] ?? 0, 0, ',', '.') }}
                                pcs</h3>
                        </div>
                        <div class="bg-gray-200 p-2 rounded-xl">
                            <flux:icon icon="arrow-up" variant="solid" class="text-[#666666]" />
                        </div>
                    </div>
                    <p class="text-sm">{{ $bestProduction['name'] ?? '-' }}</p>
                    <p class="text-xs {{ $diffStats['best']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $diffStats['best']['percentage'] }}%
                        ({{ $diffStats['best']['diff'] > 0 ? '+' : '' }}{{ $diffStats['best']['diff'] }})
                    </p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow h-1/2 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm text-gray-500">Produksi Terendah</p>
                            <h3 class="text-lg font-bold">
                                {{ number_format($worstProduction['total'] ?? 0, 0, ',', '.') }}
                                pcs</h3>
                        </div>
                        <div class="bg-gray-200 p-2 rounded-xl">
                            <flux:icon icon="arrow-down" variant="solid" class="text-[#666666]" />
                        </div>
                    </div>
                    <p class="text-sm">{{ $worstProduction['name'] ?? '-' }}</p>
                    <p class="text-xs {{ $diffStats['worst']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $diffStats['worst']['percentage'] }}%
                        ({{ $diffStats['worst']['diff'] > 0 ? '+' : '' }}{{ $diffStats['worst']['diff'] }})
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mt-6">
            <div class="p-4 bg-white rounded-lg shadow">
                <p class="text-sm font-semibold mb-2">Metode Produksi Teratas</p>
                @if (count($productionChartData['labels']) == 0 || count($productionChartData['data']) == 0)
                    <p class="text-gray-500">Tidak ada data untuk ditampilkan</p>
                @endif
                <div class="w-full h-96 flex justify-center">
                    <canvas id="productionMethodChart" class="p-4"></canvas>
                </div>
            </div>
        </div>
        <div class="mt-6 bg-white p-4 rounded-lg shadow">
            <p class="font-semibold mb-4">Produksi Produk</p>
            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="py-2 px-3">Produk</th>
                        <th class="py-2 px-3">Produksi</th>
                        <th class="py-2 px-3">Berhasil</th>
                        <th class="py-2 px-3">Gagal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productionProducts as $item)
                        <tr class="border-b">
                            <td class="py-2 px-3">{{ $item->name }}</td>
                            <td class="py-2 px-3">{{ $item->total }}</td>
                            <td class="py-2 px-3">{{ $item->success }}</td>
                            <td class="py-2 px-3">{{ $item->fail }}</td>
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
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    @endassets

    <script>
        let topProductionsChart, paymentChart;

        function initTopProductsChart(labels, data) {
            if (topProductionsChart) topProductionsChart.destroy();

            const ctx = document.getElementById('topProductionsChart').getContext('2d');
            topProductionsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Produksi (Pcs)',
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
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        window.addEventListener('update-charts', event => {
            const {
                topProductionsChartData,
                productionChartData,
            } = event.detail[0];

            // console.log('event yang diterima:', event.detail[0]);


            if (topProductionsChartData) {
                initTopProductsChart(topProductionsChartData.labels, topProductionsChartData.data);
            }
            if (productionChartData) {
                paymentChart = initPieChart('productionMethodChart', productionChartData.labels, productionChartData
                    .data,
                    paymentChart, 'Metode Produksi');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi pertama pakai data awal dari Blade
            initTopProductsChart(
                @json($topProductionsChartData['labels']),
                @json($topProductionsChartData['data']),
            );
            paymentChart = initPieChart(
                'productionMethodChart',
                @json($productionChartData['labels']),
                @json($productionChartData['data']),
                null,
                'Metode Produksi'
            );
        });
    </script>


</div>
