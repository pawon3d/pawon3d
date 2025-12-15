<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kasir - Keuangan - {{ $dateRange }}</title>
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

        .text-green {
            color: #16a34a;
        }

        .text-red {
            color: #dc2626;
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
        <h1>Laporan Kasir - Keuangan</h1>
        <p>Periode: {{ $dateRange }}</p>
        @if ($workerName !== 'Semua Pekerja')
            <p>Pekerja: {{ $workerName }}</p>
        @endif
        @if ($methodName !== 'Semua Metode')
            <p>Metode: {{ $methodName }}</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Rincian Keuangan Bulanan</div>
        @if (count($keuanganData) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Waktu</th>
                        <th width="13%" class="text-right">Pendapatan Kotor</th>
                        <th width="13%" class="text-right">Refund</th>
                        <th width="13%" class="text-right">Potongan Harga</th>
                        <th width="13%" class="text-right">Pendapatan Bersih</th>
                        <th width="13%" class="text-right">Modal</th>
                        <th width="15%" class="text-right">Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($keuanganData as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item['waktu'] }}</td>
                            <td class="text-right">Rp {{ number_format($item['pendapatanKotor'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['refund'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['potonganHarga'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['pendapatanBersih'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['modal'], 0, ',', '.') }}</td>
                            <td class="text-right {{ $item['keuntungan'] >= 0 ? 'text-green' : 'text-red' }}">
                                Rp {{ number_format($item['keuntungan'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; padding: 20px; color: #999;">Tidak ada data keuangan</p>
        @endif
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</body>

</html>
