<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produksi Berhasil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3f4e4f;
        }

        .header h1 {
            font-size: 18px;
            color: #3f4e4f;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .info-section {
            margin-bottom: 15px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            width: 120px;
            font-weight: bold;
            color: #3f4e4f;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #3f4e4f;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Produksi Berhasil</h1>
        <p>Periode: {{ $dateRange }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Pekerja:</div>
            <div class="info-value">{{ $workerName }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Metode Produksi:</div>
            <div class="info-value">{{ $methodName }}</div>
        </div>
    </div>

    @if (count($productionData) > 0)
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Tanggal</th>
                    <th width="25%">Produk</th>
                    <th width="15%" class="text-center">Berhasil</th>
                    <th width="20%">Pekerja</th>
                    <th width="15%">Metode</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productionData as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['date'] }}</td>
                        <td>{{ $item['product'] }}</td>
                        <td class="text-center">{{ $item['success'] }}</td>
                        <td>{{ $item['workers'] }}</td>
                        <td>{{ $item['method'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-message">
            Tidak ada data produksi berhasil untuk periode dan filter yang dipilih.
        </div>
    @endif

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }} WIB
    </div>
</body>

</html>
