<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Transaksi - {{ $transaction->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000000;
            background-color: #ffffff;
            padding: 10px 15px;
            width: 280px;
            margin: 0 auto;
        }

        .container {
            background-color: #fafafa;
            border: 1px solid #666666;
            border-radius: 15px;
            padding: 20px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            color: #000000;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .address {
            text-align: center;
            font-size: 9px;
            color: #000000;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .divider {
            border-top: 1px dashed #000000;
            margin: 10px 0;
        }

        .section {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            font-size: 9px;
        }

        .info-table .label {
            width: 40%;
            font-weight: normal;
        }

        .info-table .value {
            width: 60%;
            font-weight: bold;
            text-align: right;
        }

        .product-item {
            margin-bottom: 10px;
        }

        .product-table td {
            padding: 2px 0;
        }

        .product-name {
            font-weight: normal;
            width: 65%;
        }

        .product-price {
            font-weight: bold;
            text-align: right;
            width: 35%;
        }

        .product-qty {
            font-size: 9px;
            font-weight: normal;
        }

        .total-table td {
            padding: 4px 0;
            font-size: 10px;
        }

        .total-label {
            width: 60%;
            font-weight: normal;
        }

        .total-value {
            width: 40%;
            font-weight: bold;
            text-align: right;
        }

        .payment-table td {
            padding: 4px 0;
            font-size: 9px;
        }

        .payment-label {
            width: 60%;
            font-weight: normal;
        }

        .payment-value {
            width: 40%;
            font-weight: normal;
            text-align: right;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            font-weight: normal;
            color: #000000;
            margin-top: 10px;
            line-height: 1.4;
        }

        .status-lunas {
            color: #009900;
        }

        .status-belum {
            color: #ff9900;
        }

        .status-refund {
            color: #cc0000;
        }

        .refund-text {
            color: #cc0000;
        }

        .discount-text {
            color: #009900;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- Logo --}}
        <div class="logo">
            {{ config('app.name', 'Pawon3D') }}
        </div>

        {{-- Alamat --}}
        <div class="address">
            <div>
                {{ $storeProfile->address != '' ? $storeProfile->address : 'Jl. Jenderal Sudirman Km.3 RT.25 RW.07 Kel. Muara Bulian, Kec.Muara Bulian, Kab.Batang Hari, Jambi, 36613' }}
            </div>
            <div>Telp: {{ $storeProfile->contact != '' ? $storeProfile->contact : '081234567890' }}</div>
        </div>

        <div class="divider"></div>

        {{-- Info Transaksi --}}
        <div class="section">
            <table class="info-table">
                <tr>
                    <td class="label">ID Transaksi</td>
                    <td class="value">{{ $transaction->invoice_number }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="value">
                        {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->translatedFormat('d F Y H:i') : \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Status Bayar</td>
                    <td
                        class="value {{ $transaction->payment_status == 'Lunas' ? 'status-lunas' : ($transaction->payment_status == 'Refund' ? 'status-refund' : 'status-belum') }}">
                        {{ $transaction->payment_status ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Kasir</td>
                    <td class="value">{{ $transaction->user->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        {{-- Daftar Produk --}}
        <div class="section">
            @foreach ($transaction->details as $detail)
                <div class="product-item">
                    <table class="product-table">
                        <tr>
                            <td class="product-name">{{ $detail->product->name }}</td>
                            <td class="product-price">
                                Rp{{ number_format($detail->product->price * $detail->quantity, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="product-qty" colspan="2">
                                {{ $detail->quantity }} x Rp{{ number_format($detail->product->price, 0, ',', '.') }}
                                @if ($detail->refund_quantity > 0)
                                    <span class="refund-text">(Refund {{ $detail->refund_quantity }} =
                                        -Rp{{ number_format($detail->product->price * $detail->refund_quantity, 0, ',', '.') }})</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        {{-- Total --}}
        @php
            $subtotal = $transaction->total_amount;
            $totalItems = $transaction->details->sum('quantity');
        @endphp
        <div class="section">
            <table class="total-table">
                <tr>
                    <td class="total-label">Subtotal {{ $totalItems }} Produk</td>
                    <td class="total-value">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                @if ($transaction->points_used > 0)
                    <tr class="discount-text">
                        <td class="total-label">Tukar {{ number_format($transaction->points_used, 0, ',', '.') }} Poin
                        </td>
                        <td class="total-value">-Rp{{ number_format($transaction->points_discount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr class="bold">
                    <td class="total-label">Total Tagihan</td>
                    <td class="total-value">
                        Rp{{ number_format($subtotal - $transaction->points_discount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        {{-- Pembayaran --}}
        @php
            $allPayments = $transaction->payments ?? collect();
            $totalPaid = $allPayments->sum('paid_amount');
            $transactionRefund = $transaction->refund;
            $remainingAmount =
                $subtotal -
                $transaction->points_discount -
                $totalPaid +
                ($transactionRefund ? $transactionRefund->total_amount : 0);
        @endphp
        <div class="section">
            <table class="total-table">
                <tr class="bold">
                    <td class="total-label">Total Bayar</td>
                    <td class="total-value">Rp{{ number_format($totalPaid, 0, ',', '.') }}</td>
                </tr>
            </table>

            {{-- Refund Payment Display --}}
            @if ($transactionRefund)
                <table class="payment-table">
                    <tr class="refund-text">
                        <td class="payment-label">
                            (Refund)
                            {{ ucfirst($transactionRefund->refund_method) }}{{ $transactionRefund->channel ? ' - ' . $transactionRefund->channel->bank_name : '' }}
                        </td>
                        <td class="payment-value">Rp{{ number_format($transactionRefund->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @endif

            @if ($allPayments && $allPayments->count())
                <table class="payment-table">
                    @foreach ($allPayments as $payment)
                        @php
                            $method = $payment->payment_method ? ucfirst($payment->payment_method) : '-';
                            $bank = $payment->channel->bank_name ?? null;
                            $paymentCount = $allPayments->count();
                            $paymentIndex = $allPayments->search(fn($p) => $p->id === $payment->id);
                            $tipe =
                                $paymentCount == 1
                                    ? 'Lunas'
                                    : ($paymentIndex == $paymentCount - 1
                                        ? 'Lunas'
                                        : 'Uang Muka');
                            $label = "($tipe) $method" . ($bank ? " - $bank" : '');
                        @endphp
                        <tr>
                            <td class="payment-label">{{ $label }}</td>
                            <td class="payment-value">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            <table class="total-table">
                <tr class="bold {{ $remainingAmount > 0 ? 'refund-text' : '' }}">
                    <td class="total-label">Sisa Tagihan</td>
                    <td class="total-value">Rp{{ number_format($remainingAmount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        {{-- Footer Info --}}
        <div class="section">
            <table class="info-table">
                <tr>
                    <td class="label">Tanggal Cetak</td>
                    <td class="value">{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        {{-- Footer Message --}}
        <div class="footer">
            Mohon Cek Kembali Uang Kembalian<br>Sebelum Meninggalkan Kasir
        </div>
    </div>
</body>

</html>
