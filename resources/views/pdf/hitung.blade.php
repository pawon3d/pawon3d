<!DOCTYPE html>
<html>

<head>
    <title>{{ $status == 'history' ? 'Riwayat' : 'Daftar' }} Hitung dan Catat Persediaan</title>
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
    <h1>{{ $status == 'history' ? 'Riwayat' : 'Daftar' }} Hitung dan Catat Persediaan</h1>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tanggal Dibuat</th>
                <th>Tanggal Selesai</th>
                <th>Aksi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hitungs as $hitung)
            <tr>
                <td>{{ $hitung->hitung_number }}</td>
                <td>{{ \Carbon\Carbon::parse($hitung->hitung_date)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($hitung->hitung_finish_date)->format('d-m-Y') }}</td>
                <td>{{ $hitung->action }}</td>
                <td>{{ $hitung->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>