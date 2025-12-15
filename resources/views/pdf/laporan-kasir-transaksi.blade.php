<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kasir - Transaksi - {{ $dateRange }}</title>
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
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Kasir - Transaksi Pembayaran</h1>
        <p>Periode: {{ $dateRange }}</p>
        @if ($workerName !== 'Semua Pekerja')
            <p>Pekerja: {{ $workerName }}</p>
        @endif
        @if ($methodName !== 'Semua Metode')
            <p>Metode: {{ $methodName }}</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Daftar Transaksi</div>
        @if (count($transaksiData) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Invoice</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Kasir</th>
                        <th width="30%">Produk</th>
                        <th width="15%" class="text-right">Total</th>
                        <th width="10%">Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksiData as $index => $trx)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $trx['invoice'] }}</td>
                            <td>{{ $trx['tanggal'] }}</td>
                            <td>{{ $trx['kasir'] }}</td>
                            <td>{{ $trx['products'] }}</td>
                            <td class="text-right">Rp {{ number_format($trx['total'], 0, ',', '.') }}</td>
                            <td>{{ $trx['method'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 20px; color: #999;">Tidak ada data transaksi</p>
        @endif
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</body>

</html>
