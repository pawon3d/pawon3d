<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi {{ $dateRange }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Transaksi</h2>
        <strong>{{ $dateRange }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Penanggung Jawab</th>
                <th>Total</th>
                <th>Status Pembayaran</th>
                <th>Tipe</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transaction->user->name }}</td>
                <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                <td>{{ ucfirst($transaction->payment_status) }}</td>
                <td>{{ $transaction->type }}</td>
                <td>{{ $transaction->created_at->translatedFormat('d F Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada transaksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>