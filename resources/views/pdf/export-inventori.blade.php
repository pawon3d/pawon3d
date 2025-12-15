<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Export Inventori - {{ ucfirst($reportContent) }} - {{ $dateRange }}</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th {
            background: #3f4e4f;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }

        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background: #56c568;
            color: white;
        }

        .badge-warning {
            background: #f59e0b;
            color: white;
        }

        .badge-danger {
            background: #eb5757;
            color: white;
        }

        .badge-info {
            background: #3b82f6;
            color: white;
        }

        .badge-secondary {
            background: #6b7280;
            color: white;
        }

        .expense-box {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .expense-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .expense-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 3px;
            font-size: 10px;
        }

        .expense-info strong {
            display: inline-block;
            width: 100px;
        }

        .summary-card {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #3f4e4f;
        }

        .grid-3 {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .grid-3-col {
            display: table-cell;
            width: 33.33%;
            padding: 0 5px;
        }

        .text-green {
            color: #56c568;
        }

        .text-red {
            color: #eb5757;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Export Inventori - {{ ucfirst($reportContent) }}</h1>
        <p>Periode: {{ $dateRange }}</p>
        <p>Pekerja: {{ $workerName }}</p>
    </div>

    @if ($reportContent === 'belanja')
        {{-- Belanja Report --}}
        <div class="section">
            <div class="section-title">Data Belanja</div>

            @forelse ($expenseData as $expense)
                <div class="expense-box">
                    <div class="expense-header">
                        <div class="expense-info">
                            <strong>No. Belanja:</strong> {{ $expense['expense_number'] }}
                        </div>
                        <div class="expense-info">
                            <strong>Tanggal:</strong> {{ $expense['expense_date'] }}
                        </div>
                    </div>
                    <div class="expense-header">
                        <div class="expense-info">
                            <strong>Supplier:</strong> {{ $expense['supplier_name'] }}
                        </div>
                        <div class="expense-info">
                            <strong>Pekerja:</strong> {{ $expense['worker_name'] }}
                        </div>
                    </div>
                    <div class="expense-header">
                        <div class="expense-info">
                            <strong>Status:</strong>
                            <span
                                class="badge {{ $expense['status'] === 'Lunas' ? 'badge-success' : 'badge-warning' }}">
                                {{ $expense['status'] }}
                            </span>
                        </div>
                        <div class="expense-info">
                            <strong>Total:</strong> Rp {{ number_format($expense['grand_total'], 0, ',', '.') }}
                        </div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Satuan</th>
                                <th class="text-right">Qty Harap</th>
                                <th class="text-right">Qty Dapat</th>
                                <th class="text-right">Harga Harap</th>
                                <th class="text-right">Harga Dapat</th>
                                <th class="text-right">Total Harap</th>
                                <th class="text-right">Total Dapat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expense['details'] as $detail)
                                <tr>
                                    <td>{{ $detail['material_name'] }}</td>
                                    <td>{{ $detail['unit_name'] }}</td>
                                    <td class="text-right">{{ $detail['quantity_expect'] }}</td>
                                    <td class="text-right">{{ $detail['quantity_get'] }}</td>
                                    <td class="text-right">Rp {{ number_format($detail['price_expect'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">Rp {{ number_format($detail['price_get'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">Rp {{ number_format($detail['total_expect'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">Rp {{ number_format($detail['total_actual'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <p style="text-align: center; color: #999; padding: 20px;">Tidak ada data belanja</p>
            @endforelse
        </div>
    @elseif($reportContent === 'persediaan')
        {{-- Persediaan Report --}}
        <div class="section">
            <div class="section-title">Ringkasan Nilai Persediaan</div>

            <div class="grid-3">
                <div class="grid-3-col">
                    <div class="summary-card">
                        <div class="summary-label">Nilai Persediaan</div>
                        <div class="summary-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="grid-3-col">
                    <div class="summary-card">
                        <div class="summary-label">Nilai Terpakai</div>
                        <div class="summary-value">Rp {{ number_format($usedGrandTotal, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="grid-3-col">
                    <div class="summary-card">
                        <div class="summary-label">Nilai Saat Ini</div>
                        <div class="summary-value">Rp {{ number_format($remainGrandTotal, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Detail Pemakaian Material</div>

            <table>
                <thead>
                    <tr>
                        <th>Material</th>
                        <th class="text-right">Quantity Terpakai</th>
                        <th class="text-right">Nilai Terpakai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materialUsage as $material)
                        <tr>
                            <td>{{ $material['material_name'] }}</td>
                            <td class="text-right">{{ number_format($material['quantity_used'], 2, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($material['value_used'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center" style="color: #999;">Tidak ada data pemakaian</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @elseif($reportContent === 'alur')
        {{-- Alur Report --}}
        <div class="section">
            <div class="section-title">Alur Persediaan</div>

            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Material</th>
                        <th>Batch</th>
                        <th>Satuan</th>
                        <th>Aksi</th>
                        <th class="text-right">Perubahan</th>
                        <th class="text-right">Stok Setelah</th>
                        <th>Pekerja</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($flowData as $flow)
                        <tr>
                            <td>{{ $flow['created_at'] }}</td>
                            <td>{{ $flow['material_name'] }}</td>
                            <td>{{ $flow['batch_number'] }}</td>
                            <td>{{ $flow['unit_name'] }}</td>
                            <td>
                                @php
                                    $badgeClass = 'badge-secondary';
                                    if ($flow['action'] === 'belanja') {
                                        $badgeClass = 'badge-success';
                                    } elseif ($flow['action'] === 'produksi') {
                                        $badgeClass = 'badge-info';
                                    } elseif ($flow['action'] === 'rusak' || $flow['action'] === 'hilang') {
                                        $badgeClass = 'badge-danger';
                                    } elseif ($flow['action'] === 'hitung') {
                                        $badgeClass = 'badge-warning';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $flow['action'] }}</span>
                            </td>
                            <td class="text-right {{ $flow['quantity_change'] > 0 ? 'text-green' : 'text-red' }}">
                                {{ $flow['quantity_change'] > 0 ? '+' : '' }}{{ $flow['quantity_change'] }}
                            </td>
                            <td class="text-right">{{ $flow['quantity_after'] }}</td>
                            <td>{{ $flow['user_name'] }}</td>
                            <td>{{ $flow['note'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center" style="color: #999;">Tidak ada data alur</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>
</body>

</html>
