<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Produksi - {{ $dateRange }}</title>
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
            border-bottom: 2px solid #7c3aed;
        }

        .header h1 {
            font-size: 18px;
            color: #7c3aed;
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
            color: #7c3aed;
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
            background-color: #7c3aed;
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
            background: #7c3aed;
            color: white;
        }

        .summary-box.highlight .stat-label {
            color: rgba(255, 255, 255, 0.7);
        }

        .summary-box.highlight .stat-value {
            color: white;
        }

        .summary-box.success {
            background: #16a34a;
            color: white;
        }

        .summary-box.success .stat-label {
            color: rgba(255, 255, 255, 0.7);
        }

        .summary-box.success .stat-value {
            color: white;
        }

        .summary-box.danger {
            background: #dc2626;
            color: white;
        }

        .summary-box.danger .stat-label {
            color: rgba(255, 255, 255, 0.7);
        }

        .summary-box.danger .stat-value {
            color: white;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Produksi</h1>
        <p>Periode: {{ $dateRange }}</p>
        @if ($workerName !== 'Semua Pekerja')
            <p>Pekerja: {{ $workerName }}</p>
        @endif
        @if ($methodName !== 'Semua Metode')
            <p>Metode: {{ $methodName }}</p>
        @endif
    </div>

    {{-- Ringkasan Produksi --}}
    <div class="section">
        <div class="section-title">Ringkasan Produksi</div>
        <table class="summary-table">
            <tr>
                <td width="33%">
                    <div class="summary-box highlight">
                        <div class="stat-label">Total Produksi</div>
                        <div class="stat-value">{{ number_format($totalProduction, 0, ',', '.') }} pcs</div>
                        <div class="stat-diff" style="color: rgba(255,255,255,0.7);">
                            {{ $diffStats['totalProduction']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['totalProduction']['percentage'] }}%
                            ({{ $diffStats['totalProduction']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['totalProduction']['diff'], 0, ',', '.') }})
                        </div>
                    </div>
                </td>
                <td width="33%">
                    <div class="summary-box success">
                        <div class="stat-label">Produksi Berhasil</div>
                        <div class="stat-value">{{ number_format($successProduction, 0, ',', '.') }} pcs</div>
                        <div class="stat-diff" style="color: rgba(255,255,255,0.7);">
                            {{ $diffStats['successProduction']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['successProduction']['percentage'] }}%
                            ({{ $diffStats['successProduction']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['successProduction']['diff'], 0, ',', '.') }})
                        </div>
                    </div>
                </td>
                <td width="33%">
                    <div class="summary-box danger">
                        <div class="stat-label">Produksi Gagal</div>
                        <div class="stat-value">{{ number_format($failedProduction, 0, ',', '.') }} pcs</div>
                        <div class="stat-diff" style="color: rgba(255,255,255,0.7);">
                            {{ $diffStats['failedProduction']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['failedProduction']['percentage'] }}%
                            ({{ $diffStats['failedProduction']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['failedProduction']['diff'], 0, ',', '.') }})
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Statistik Produk --}}
    <div class="section">
        <div class="section-title">Statistik Produk</div>
        <table class="stats-grid">
            <tr>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Produk Terbanyak Diproduksi</div>
                        <div class="stat-value">{{ $bestProduction['name'] ?? '-' }}</div>
                        <div class="stat-diff">{{ number_format($bestProduction['total'] ?? 0, 0, ',', '.') }} pcs
                            diproduksi</div>
                    </div>
                </td>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Produk Tersedikit Diproduksi</div>
                        <div class="stat-value">{{ $worstProduction['name'] ?? '-' }}</div>
                        <div class="stat-diff">{{ number_format($worstProduction['total'] ?? 0, 0, ',', '.') }} pcs
                            diproduksi</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Top 10 Produksi --}}
    @if (count($topProductions) > 0)
        <div class="section">
            <div class="section-title">10 Produk Terbanyak Diproduksi</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="60%">Nama Produk</th>
                        <th width="30%" class="text-right">Jumlah Produksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topProductions as $product)
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

    {{-- Tabel Detail Produksi --}}
    @if (count($productionProducts) > 0)
        <div class="section">
            <div class="section-title">Detail Produksi per Produk</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="8%">No</th>
                        <th width="32%">Produk</th>
                        <th width="20%">Kategori</th>
                        <th width="14%" class="text-center">Berhasil</th>
                        <th width="13%" class="text-center">Gagal</th>
                        <th width="13%" class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productionProducts as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['category'] }}</td>
                            <td class="text-center">{{ number_format($item['produced'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($item['failed'], 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($item['total'], 0, ',', '.') }}</td>
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
