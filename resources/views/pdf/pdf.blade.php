<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'DejaVu Sans', 'Courier New', monospace;
            font-size: 9pt;
            line-height: 1.3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        th {
            border-bottom: 1px dashed #000;
            padding: 2px 0;
        }

        td {
            padding: 1px 0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 5px;">
        <h3 style="margin: 2px 0;">Struk Transaksi</h3>
        <p style="margin: 2px 0;">{{ \Carbon\Carbon::now()->isoFormat('DD-MM-YYYY HH:mm') }}</p>
    </div>

    <div style="margin-bottom: 5px;">
        @php
        $fields = [
        'Tipe' => $transaction->type,
        'Total' => 'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
        ];
        @endphp

        @foreach($fields as $label => $value)
        <p style="margin: 1px 0;">
            <strong>{{ $label }}:</strong>
            {!! htmlspecialchars($value, ENT_QUOTES, 'UTF-8', true) !!}
        </p>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $detail)
            <tr>
                <td class="text-center">{!! htmlspecialchars($detail->product->name, ENT_QUOTES, 'UTF-8', true) !!}</td>
                <td class="text-right">{{ $detail->quantity }}</td>
                <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 8px;">
        <p>Terima kasih telah berbelanja</p>
    </div>
</body>
</html>
