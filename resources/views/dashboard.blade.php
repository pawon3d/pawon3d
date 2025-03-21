<div>
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <!-- Icon bisa diganti sesuai preferensi -->
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3V3z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Penjualan Hari Ini</p>
                        <p class="text-2xl font-semibold">Rp {{ number_format($stats['today_sales'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Pendapatan Bulan Ini</p>
                        <p class="text-2xl font-semibold">Rp {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 6h18M3 14h18" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Order Pending</p>
                        <p class="text-2xl font-semibold">{{ $stats['pending_orders'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Produksi Selesai</p>
                        <p class="text-2xl font-semibold">{{ $stats['completed_productions'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Chart -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Transaksi 30 Hari Terakhir</h2>
            <canvas id="transactionsChart" width="400" height="150"></canvas>
        </div>

        <!-- Latest Orders -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Transaksi Terbaru</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp
                                {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($transaction->status) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $transactions->links(data: ['scrollTo' => false]) }}
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Produk Stok Rendah</h2>
            <ul class="divide-y divide-gray-200">
                @foreach ($lowStockProducts as $product)
                <li class="py-2 flex justify-between">
                    <span>{{ $product->name }}</span>
                    <span class="text-red-500 font-bold">{{ $product->stock }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Top Selling Products -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Produk Terlaris</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Terjual
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($topSellingProducts as $detail)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $detail->product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $detail->total_quantity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Productions -->
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Produksi Dalam Proses</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($productions as $production)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $production->product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($production->status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $production->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @section('scripts')
    <script src="{{ asset('scripts/chart.js') }}"></script>
    <script>
        function initializeChart() {
            const ctx = document.getElementById('transactionsChart').getContext('2d');
            const chartData = {!! $transactions_chart !!};
        
            const labels = chartData.map(item => item.date);
            const totals = chartData.map(item => item.total);
        
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Penjualan',
                        data: totals,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener('livewire:navigated', function () {
            initializeChart();
        },{ once: true });

    </script>
    @endsection
</div>