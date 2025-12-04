<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Inventori - {{ $dateRange }}</title>
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
        <h1>Laporan Inventori</h1>
        <p>Periode: {{ $dateRange }}</p>
        @if ($workerName !== 'Semua Pekerja')
            <p>Pekerja: {{ $workerName }}</p>
        @endif
    </div>

    {{-- Ringkasan Modal --}}
    <div class="section">
        <div class="section-title">Ringkasan Modal Persediaan</div>
        <table class="summary-table">
            <tr>
                <td width="33%">
                    <div class="summary-box highlight">
                        <div class="stat-label">Total Belanja</div>
                        <div class="stat-value">Rp {{ number_format($expenseGrandTotal, 0, ',', '.') }}</div>
                        <div
                            class="stat-diff {{ $diffStats['expenseGrandTotal']['diff'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $diffStats['expenseGrandTotal']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['expenseGrandTotal']['percentage'] }}%
                        </div>
                    </div>
                </td>
                <td width="33%">
                    <div class="summary-box">
                        <div class="stat-label">Modal Terpakai</div>
                        <div class="stat-value">Rp {{ number_format($usedGrandTotal, 0, ',', '.') }}</div>
                        <div
                            class="stat-diff {{ $diffStats['usedGrandTotal']['diff'] >= 0 ? 'negative' : 'positive' }}">
                            {{ $diffStats['usedGrandTotal']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['usedGrandTotal']['percentage'] }}%
                        </div>
                    </div>
                </td>
                <td width="33%">
                    <div class="summary-box">
                        <div class="stat-label">Modal Tersisa</div>
                        <div class="stat-value">Rp {{ number_format($remainGrandTotal, 0, ',', '.') }}</div>
                        <div
                            class="stat-diff {{ $diffStats['remainGrandTotal']['diff'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $diffStats['remainGrandTotal']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['remainGrandTotal']['percentage'] }}%
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Statistik --}}
    <div class="section">
        <div class="section-title">Statistik Persediaan</div>
        <table class="stats-grid">
            <tr>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Total Belanja</div>
                        <div class="stat-value">{{ number_format($totalExpense, 0, ',', '.') }} kali</div>
                        <div class="stat-diff {{ $diffStats['totalExpense']['diff'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $diffStats['totalExpense']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['totalExpense']['percentage'] }}%
                            ({{ $diffStats['totalExpense']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['totalExpense']['diff'], 0, ',', '.') }})
                        </div>
                    </div>
                </td>
                <td>
                    <div class="stat-box">
                        <div class="stat-label">Persediaan Terbanyak Digunakan</div>
                        <div class="stat-value">{{ $bestMaterial['name'] ?? '-' }}</div>
                        <div class="stat-diff">Rp {{ number_format($bestMaterial['total'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="stat-box">
                        <div class="stat-label">Persediaan Tersedikit Digunakan</div>
                        <div class="stat-value">{{ $worstMaterial['name'] ?? '-' }}</div>
                        <div class="stat-diff">Rp {{ number_format($worstMaterial['total'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Top 10 Persediaan Terpakai --}}
    @if (count($topMaterials) > 0)
        <div class="section">
            <div class="section-title">10 Persediaan Terpakai Terbanyak (Berdasarkan Nilai)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="60%">Nama Persediaan</th>
                        <th width="30%" class="text-right">Nilai Terpakai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topMaterials as $material)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $material['name'] }}</td>
                            <td class="text-right">Rp {{ number_format($material['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Tabel Detail Persediaan --}}
    @if (count($materialTables) > 0)
        <div class="section">
            <div class="section-title">Detail Persediaan</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Persediaan</th>
                        <th width="12%">Satuan</th>
                        <th width="15%" class="text-right">Harga Beli</th>
                        <th width="12%" class="text-right">Min. Stok</th>
                        <th width="12%" class="text-right">Tersisa</th>
                        <th width="14%" class="text-right">Nilai Sisa</th>
                        <th width="10%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materialTables as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['unit'] }}</td>
                            <td class="text-right">Rp {{ number_format($item['supply_price'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item['min_stock'], 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item['remain'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['remain_value'], 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span
                                    style="color: {{ $item['status'] === 'Rendah' ? '#dc2626' : '#16a34a' }}; font-weight: 600;">
                                    {{ $item['status'] }}
                                </span>
                            </td>
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
