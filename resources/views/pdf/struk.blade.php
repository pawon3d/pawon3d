<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 4px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        hr {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="text-center">
        <h3>Struk Transaksi</h3>
        <p>Tanggal: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
    </div>

    <hr>

    <p><strong>Total:</strong> Rp {{ number_format($transaction->total_amount) }}</p>
    <p><strong>Status Pembayaran:</strong> {{ $transaction->payment_status }}</p>
    <p><strong>Tipe:</strong>
        @if($transaction->method == 'pesanan-reguler')
        Pesanan Reguler
        @elseif($transaction->method == 'pesanan-kotak')
        Pesanan Kotak
        @else
        Siap Saji
        @endif
    </p>

    <hr>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $detail)
            <tr>
                <td>{{ $detail->product->name }}</td>
                <td class="text-right">{{ $detail->quantity }}</td>
                <td class="text-right">Rp {{ number_format($detail->price) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>
    <div class="text-center">
        <p>Terima kasih telah berbelanja</p>
    </div>
</body>

</html>