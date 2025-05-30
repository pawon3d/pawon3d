<!DOCTYPE html>
<html>

<head>
    <title>Belanja Persediaan {{ $expense->expense_number }}</title>
    <style>
        /* Container Utama */
        .expense-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }

        .expense-header {
            font-size: 1.875rem;
            font-weight: 700;
        }

        .expense-status {
            font-size: 1.125rem;
            color: #6b7280;
        }

        /* Header Section */
        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-direction: row;
        }

        .supplier-section {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            flex-direction: column;
        }

        .info-section {
            display: flex;
            align-items: center;
            gap: 4rem;
            flex-direction: row;
        }

        .info-group {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            flex-direction: column;
        }

        .section-heading {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .info-text {
            font-size: 0.875rem;
            text-align: right;
        }

        /* Progress Bar */
        .progress-container {
            display: flex;
            align-items: center;
            flex-direction: column;
            margin: 1rem 0;
            gap: 1rem;
        }

        .progress-bar {
            width: 100%;
            height: 0.25rem;
            background-color: #e5e7eb;
            border-radius: 9999px;
            margin-bottom: 1rem;
        }

        .progress-fill {
            height: 100%;
            background-color: #3b82f6;
            border-radius: 9999px;
        }

        .progress-text {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* Dark Mode Variants */
        .dark .progress-bar {
            background-color: #374151;
        }

        .dark .progress-fill {
            background-color: #3b82f6;
        }

        /* Notes Section */
        .notes-section {
            display: flex;
            align-items: flex-start;
            text-align: left;
            gap: 0.75rem;
            flex-direction: column;
            margin-top: 1rem;
        }

        .notes-textarea {
            background-color: #d1d5db;
            width: 100%;
            border-radius: 0.375rem;
            padding: 0.5rem;
            font-size: 1rem;
            resize: none;
            border: none;
        }

        /* Shopping List */
        .shopping-container {
            width: 100%;
            margin-top: 2rem;
            display: flex;
            align-items: center;
            flex-direction: column;
            gap: 1rem;
        }

        .shopping-header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 1rem;
            flex-direction: row;
        }

        .table-container {
            position: relative;
            overflow-x: auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
            width: 100%;
        }

        .data-table {
            width: 100%;
            font-size: 0.875rem;
            text-align: left;
            border-collapse: collapse;
        }

        .data-table thead {
            background-color: #e5e7eb;
            text-transform: uppercase;
        }

        .data-table th {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            color: #374151;
        }

        .data-table td {
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table tfoot {
            background-color: #e5e7eb;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .data-table tfoot td {
            color: #374151;
            font-weight: 600;
        }

        /* Text Styles */
        .text-sm {
            font-size: 0.875rem;
        }

        /* Dark Mode Table */
        .dark .data-table thead {
            background-color: #374151;
            color: #d1d5db;
        }

        .dark .data-table tfoot {
            background-color: #374151;
            color: #d1d5db;
        }

        .dark .data-table th,
        .dark .data-table td {
            color: #d1d5db;
        }

        .dark .data-table td {
            border-bottom-color: #4b5563;
        }
    </style>
</head>

<body>


    <div class="expense-container">
        <h1 class="expense-header">{{ $expense->expense_number }}</h1>
        <p class="expense-status">{{ $status }}</p>

        <div class="header-container">
            <div class="supplier-section">
                <div class="section-heading">Toko Persediaan</div>
                <p class="info-text">{{ $expense->supplier->name }}</p>
            </div>

            <div class="info-section">
                <div class="info-group">
                    <div class="section-heading">Tanggal</div>
                    <p class="info-text">{{ $expense->expense_date ?
                        \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y') : '-' }}</p>
                </div>

                <div class="info-group">
                    <div class="section-heading">Dibelanja Oleh</div>
                    <p class="info-text">{{ $logName }}</p>
                </div>
            </div>
        </div>

        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ number_format($percentage, 0) }}%"></div>
            </div>
            <span class="progress-text">
                {{ number_format($total_quantity_get, 0) }} dari {{ number_format($total_quantity_expect, 0) }}
            </span>
        </div>

        <div class="notes-section">
            <div class="section-heading">Catatan Belanja</div>
            <textarea class="notes-textarea" rows="4" disabled>{{ $expense->note }}</textarea>
        </div>
    </div>

    <div class="shopping-container">
        <div class="shopping-header">
            <label>Daftar Belanja Persediaan</label>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Barang Persediaan</th>
                        <th>Jumlah Diharapkan</th>
                        <th>Jumlah Didapatkan</th>
                        <th>Satuan Ukur Belanja</th>
                        <th>Harga / Satuan</th>
                        <th>Total Harga (Perkiraan)</th>
                        <th>Total Harga (Sebenarnya)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenseDetails as $detail)
                    <tr>
                        <td>
                            <span class="text-sm">
                                {{ $detail->material->name ?? 'Barang Tidak Ditemukan' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-sm">
                                {{ $detail->quantity_expect }}
                            </span>
                        </td>
                        <td>
                            <span class="text-sm">
                                {{ $detail->quantity_get }}
                            </span>
                        </td>
                        <td>
                            <span class="text-sm">
                                {{ $detail->unit->name ?? '' }} ({{ $detail->unit->alias ?? '' }})
                            </span>
                        </td>
                        <td>
                            <span class="text-sm">
                                Rp{{ number_format($detail->price_expect, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <span class="text-sm">
                                Rp{{ number_format($detail->total_expect, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <span class="text-sm">
                                Rp{{ number_format($detail->total_actual, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <span>Total Harga Keseluruhan (Sebenarnya)</span>
                        </td>
                        <td>
                            <span>
                                Rp{{ number_format($expense->grand_total_actual, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <span>Total Harga Keseluruhan (Perkiraan)</span>
                        </td>
                        <td>
                            <span>
                                Rp{{ number_format($expense->grand_total_expect, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>

</html>