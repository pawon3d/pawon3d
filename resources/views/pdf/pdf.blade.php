<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi</title>
    <script type="text/javascript" src="{{ asset('scripts/qr-code-styling.js') }}"></script>
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

        @media print {
            body * {
                visibility: hidden;
            }


            #printArea,
            #printArea * {
                visibility: visible;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            #printArea {
                size: 72mm 100vh;
                margin: 0;
                padding: 0;
                font-size: 10px;
            }
        }
    </style>
</head>

<body>
    <div id="printArea">
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
                    <td class="text-center">{!! htmlspecialchars($detail->product->name, ENT_QUOTES, 'UTF-8', true) !!}
                    </td>
                    <td class="text-right">{{ $detail->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 5px; text-align: center;">
            <p>Scan QR Code untuk ulasan</p>
            <div id="canvas"></div>
        </div>

        <div style="text-align: center; margin-top: 8px;">
            <p>Terima kasih telah berbelanja</p>
        </div>
    </div>


    <script type="text/javascript">
        const qrCode = new QRCodeStyling({
            width: 100,
            height: 100,
            type: "svg",
            data: "{{ route('ulasan', $transaction->id) }}",
            image: "",
            dotsOptions: {
                color: "#000",
                type: "rounded"
            },
            backgroundOptions: {
                color: "#fff",
            },
            imageOptions: {
                crossOrigin: "anonymous",
                margin: 20
            }
        });
    
        qrCode.append(document.getElementById("canvas"));
        // qrCode.download({ name: "qr", extension: "svg" });
    </script>
</body>

</html>