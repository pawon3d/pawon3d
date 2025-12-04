<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kasir - {{ $dateRange }}</title>
    <style>
        @page {
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3f4e4f;
        }

        .header h1 {
            font-size: 18px;
            color: #3f4e4f;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #3f4e4f;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .stats-grid {
            width: 100%;
            margin-bottom: 15px;
        }

        .stats-grid td {
            width: 50%;
            vertical-align: top;
            padding: 5px;
        }

        .stat-box {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .stat-diff {
            font-size: 9px;
            margin-top: 3px;
        }

        .positive {
            color: #16a34a;
        }

        .negative {
            color: #dc2626;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table.data-table th {
            background-color: #3f4e4f;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }

        table.data-table td {
            font-size: 10px;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-table td {
            padding: 8px;
            vertical-align: top;
        }

        .summary-box {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 5px;
        }

        .summary-box.highlight {
            background: #3f4e4f;
            color: white;
        }

        .summary-box.highlight .stat-label {
            color: rgba(255, 255, 255, 0.7);
        }

        .summary-box.highlight .stat-value {
            color: white;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Kasir</h1>
        <p>Periode: {{ $dateRange }}</p>
        @if ($workerName !== 'Semua Pekerja')
            <p>Pekerja: {{ $workerName }}</p>
        @endif
        @if ($methodName !== 'Semua Metode')
            <p>Metode: {{ $methodName }}</p>
        @endif
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="section">
        <div class="section-title">Ringkasan Statistik</div>
        <table class="stats-grid">
            <tr>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Sesi Penjualan</div>
                        <div class="stat-value">{{ number_format($sessionCount, 0, ',', '.') }}</div>
                        <div class="stat-diff {{ $diffStats['sessionCount']['diff'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $diffStats['sessionCount']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['sessionCount']['percentage'] }}%
                            ({{ $diffStats['sessionCount']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['sessionCount']['diff'], 0, ',', '.') }})
                        </div>
                    </div>
                </td>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Pelanggan</div>
                        <div class="stat-value">{{ number_format($customerCount, 0, ',', '.') }} orang</div>
                        <div
                            class="stat-diff {{ $diffStats['customerCount']['diff'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $diffStats['customerCount']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['customerCount']['percentage'] }}%
                            ({{ $diffStats['customerCount']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['customerCount']['diff'], 0, ',', '.') }})
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Total Transaksi</div>
                        <div class="stat-value">{{ number_format($transactionCount, 0, ',', '.') }}</div>
                        <div
                            class="stat-diff {{ $diffStats['transactionCount']['diff'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $diffStats['transactionCount']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['transactionCount']['percentage'] }}%
                            ({{ $diffStats['transactionCount']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['transactionCount']['diff'], 0, ',', '.') }})
                        </div>
                    </div>
                </td>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Produk Terjual</div>
                        <div class="stat-value">{{ number_format($productSold, 0, ',', '.') }} pcs</div>
                        <div class="stat-diff {{ $diffStats['productSold']['diff'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $diffStats['productSold']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['productSold']['percentage'] }}%
                            ({{ $diffStats['productSold']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['productSold']['diff'], 0, ',', '.') }})
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Produk Terlaris</div>
                        <div class="stat-value">{{ $bestProduct['name'] ?? '-' }}</div>
                        <div class="stat-diff">{{ number_format($bestProduct['total'] ?? 0, 0, ',', '.') }} pcs terjual
                        </div>
                    </div>
                </td>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Produk Tersepi</div>
                        <div class="stat-value">{{ $worstProduct['name'] ?? '-' }}</div>
                        <div class="stat-diff">{{ number_format($worstProduct['total'] ?? 0, 0, ',', '.') }} pcs
                            terjual</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Ringkasan Pendapatan --}}
    <div class="section">
        <div class="section-title">Ringkasan Pendapatan</div>
        <table class="summary-table">
            <tr>
                <td width="33%">
                    <div class="summary-box">
                        <div class="stat-label">Pendapatan Kotor</div>
                        <div class="stat-value">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</div>
                        <div class="stat-diff {{ $diffStats['grossRevenue']['diff'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $diffStats['grossRevenue']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['grossRevenue']['percentage'] }}%
                        </div>
                    </div>
                </td>
                <td width="33%">
                    <div class="summary-box">
                        <div class="stat-label">Potongan Harga</div>
                        <div class="stat-value">Rp {{ number_format($discountTotal, 0, ',', '.') }}</div>
                        <div
                            class="stat-diff {{ $diffStats['discountTotal']['diff'] >= 0 ? 'negative' : 'positive' }}">
                            {{ $diffStats['discountTotal']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['discountTotal']['percentage'] }}%
                        </div>
                    </div>
                </td>
                <td width="33%">
                    <div class="summary-box">
                        <div class="stat-label">Refund</div>
                        <div class="stat-value">Rp {{ number_format($refundTotal, 0, ',', '.') }}</div>
                        <div class="stat-diff {{ $diffStats['refundTotal']['diff'] >= 0 ? 'negative' : 'positive' }}">
                            {{ $diffStats['refundTotal']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['refundTotal']['percentage'] }}%
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <div class="summary-box highlight">
                        <div class="stat-label">Pendapatan Bersih</div>
                        <div class="stat-value">Rp {{ number_format($netRevenue, 0, ',', '.') }}</div>
                        <div class="stat-diff" style="color: rgba(255,255,255,0.7);">
                            {{ $diffStats['netRevenue']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['netRevenue']['percentage'] }}%
                        </div>
                    </div>
                </td>
                <td width="50%">
                    <div class="summary-box highlight">
                        <div class="stat-label">Keuntungan</div>
                        <div class="stat-value">Rp {{ number_format($profit, 0, ',', '.') }}</div>
                        <div class="stat-diff" style="color: rgba(255,255,255,0.7);">
                            {{ $diffStats['profit']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['profit']['percentage'] }}%
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Top 10 Produk Terlaris --}}
    @if (count($topProducts) > 0)
        <div class="section">
            <div class="section-title">10 Produk Terlaris</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="60%">Nama Produk</th>
                        <th width="30%" class="text-right">Jumlah Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topProducts as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $product['name'] }}</td>
                            <td class="text-right">{{ number_format($product['total'], 0, ',', '.') }} pcs</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Tabel Penjualan Produk --}}
    @if (count($productSales) > 0)
        <div class="section">
            <div class="section-title">Penjualan Produk</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="8%">No</th>
                        <th width="30%">Produk</th>
                        <th width="20%">Kategori</th>
                        <th width="14%" class="text-center">Terjual</th>
                        <th width="14%" class="text-right">Harga</th>
                        <th width="14%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productSales as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['category'] }}</td>
                            <td class="text-center">{{ number_format($item['sold'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</body>

</html>
