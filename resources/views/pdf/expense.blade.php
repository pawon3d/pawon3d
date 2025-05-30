<!DOCTYPE html>
<html>

<head>
    <title>{{ $status == 'history' ? 'Riwayat' : 'Daftar' }} Belanja Persediaan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>{{ $status == 'history' ? 'Riwayat' : 'Daftar' }} Belanja Persediaan</h1>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Status</th>
                <th>Barang Diharapkan</th>
                <th>Barang Didapatkan</th>
                <th>Total Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->expense_number }}</td>
                <td>{{ $expense->status }}</td>
                <td>{{ $expense->expenseDetails->sum('quantity_expect') }}</td>
                <td>{{ $expense->expenseDetails->sum('quantity_get') }}</td>
                <td>{{ $expense->grand_total_actual }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>