<div>
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Laporan Inventori</h2>
            <button class="px-4 py-2 bg-gray-800 text-white rounded-lg">Cetak Informasi</button>
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
                    <p class="text-sm">Sesi Belanja Persediaan</p>
                    <h3 class="text-2xl font-bold">{{ number_format($totalExpense, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-gray-200 p-2 rounded-xl">
                    <flux:icon icon="shopping-cart" variant="solid" class="text-[#666666]" />
                </div>
            </div>
            <p class="text-xs {{ $diffStats['totalExpense']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                {{ $diffStats['totalExpense']['percentage'] }}%
                ({{ $diffStats['totalExpense']['diff'] > 0 ? '+' : '' }}{{ $diffStats['totalExpense']['diff'] }})
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-lg shadow bg-white flex flex-col">
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <p class="text-sm">Nilai Persediaan</p>
                        <h3 class="text-xl font-bold">Rp{{ number_format($grandTotal, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="building-library" variant="solid" class="text-[#666666]" />
                    </div>
                </div>
                <p class="text-xs {{ $diffStats['grandTotal']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diffStats['grandTotal']['percentage'] }}%
                    ({{ $diffStats['grandTotal']['diff'] > 0 ? '+' : '' }}{{ $diffStats['grandTotal']['diff'] }})
                </p>
            </div>
            <div class="p-4 rounded-lg shadow bg-white flex flex-col">
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <p class="text-sm">Nilai Persediaan Terpakai</p>
                        <h3 class="text-xl font-bold">Rp{{ number_format($usedGrandTotal, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="building-library" variant="solid" class="text-[#666666]" />
                    </div>
                </div>
                <p class="text-xs {{ $diffStats['usedGrandTotal']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diffStats['usedGrandTotal']['percentage'] }}%
                    ({{ $diffStats['usedGrandTotal']['diff'] > 0 ? '+' : '' }}{{ $diffStats['usedGrandTotal']['diff'] }})
                </p>
            </div>
            <div class="p-4 rounded-lg shadow bg-white flex flex-col">
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <p class="text-sm">Nilai Persediaan Tersisa</p>
                        <h3 class="text-xl font-bold">Rp{{ number_format($remainGrandTotal, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-gray-200 p-2 rounded-xl">
                        <flux:icon icon="building-library" variant="solid" class="text-[#666666]" />
                    </div>
                </div>
                <p
                    class="text-xs {{ $diffStats['remainGrandTotal']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diffStats['remainGrandTotal']['percentage'] }}%
                    ({{ $diffStats['remainGrandTotal']['diff'] > 0 ? '+' : '' }}{{ $diffStats['remainGrandTotal']['diff'] }})
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 p-4 bg-white rounded-lg shadow h-full">
                <p class="font-semibold mb-4">10 Persediaan Terlaris</p>
                <canvas id="topMaterialChart"></canvas>
            </div>

            <div class="h-full gap-2 flex flex-col md:col-span-1">
                <div class="p-4 bg-white rounded-lg shadow h-1/2 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm text-gray-500">Persediaan Terlaris</p>
                            <h3 class="text-lg font-bold">
                                {{ number_format($bestMaterial['total'] ?? 0, 0, ',', '.') }}
                                pcs</h3>
                        </div>
                        <div class="bg-gray-200 p-2 rounded-xl">
                            <flux:icon icon="hand-thumb-up" variant="solid" class="text-[#666666]" />
                        </div>
                    </div>
                    <p class="text-sm">{{ $bestMaterial['name'] ?? '-' }}</p>
                    <p class="text-xs {{ $diffStats['best']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $diffStats['best']['percentage'] }}%
                        ({{ $diffStats['best']['diff'] > 0 ? '+' : '' }}{{ $diffStats['best']['diff'] }})
                    </p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow h-1/2 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm text-gray-500">Persediaan Tersepi</p>
                            <h3 class="text-lg font-bold">
                                {{ number_format($worstMaterial['total'] ?? 0, 0, ',', '.') }}
                                pcs</h3>
                        </div>
                        <div class="bg-gray-200 p-2 rounded-xl">
                            <flux:icon icon="hand-thumb-down" variant="solid" class="text-[#666666]" />
                        </div>
                    </div>
                    <p class="text-sm">{{ $worstMaterial['name'] ?? '-' }}</p>
                    <p class="text-xs {{ $diffStats['worst']['diff'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $diffStats['worst']['percentage'] }}%
                        ({{ $diffStats['worst']['diff'] > 0 ? '+' : '' }}{{ $diffStats['worst']['diff'] }})
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white p-4 rounded-lg shadow">
            <p class="font-semibold mb-4">Persediaan</p>
            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="py-2 px-3">Produk</th>
                        <th class="py-2 px-3">Jumlah Belanja</th>
                        <th class="py-2 px-3">Modal Belanja</th>
                        <th class="py-2 px-3">Jumlah Terpakai</th>
                        <th class="py-2 px-3">Modal Terpakai</th>
                        <th class="py-2 px-3">Jumlah Tersisa</th>
                        <th class="py-2 px-3">Modal Tersisa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materialTables as $item)
                        <tr class="border-b">
                            <td class="py-2 px-3">{{ $item->name }}</td>
                            <td class="py-2 px-3">{{ $item->total }} {{ $item->total_alias }}</td>
                            <td class="py-2 px-3">Rp{{ number_format($item->total_price, 0, ',', '.') }}</td>
                            <td class="py-2 px-3">-{{ $item->used }} {{ $item->used_alias }}</td>
                            <td class="py-2 px-3">Rp{{ number_format($item->used_price, 0, ',', '.') }}</td>
                            <td class="py-2 px-3">{{ $item->remain }} {{ $item->remain_alias }}</td>
                            <td class="py-2 px-3">Rp{{ number_format($item->remain_price, 0, ',', '.') }}</td>
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
        let topMaterialChart, paymentChart;

        function initTopProductsChart(labels, data) {
            if (topMaterialChart) topMaterialChart.destroy();

            const ctx = document.getElementById('topMaterialChart').getContext('2d');
            topMaterialChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Modal Terjual (Rp)',
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

        window.addEventListener('update-charts', event => {
            const {
                topMaterialChartData,
            } = event.detail[0];

            // console.log('event yang diterima:', event.detail[0]);


            if (topMaterialChartData) {
                initTopProductsChart(topMaterialChartData.labels, topMaterialChartData.data);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi pertama pakai data awal dari Blade
            initTopProductsChart(
                @json($topMaterialChartData['labels']),
                @json($topMaterialChartData['data']),
            );
        });
    </script>


</div>
