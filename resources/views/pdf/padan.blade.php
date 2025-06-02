<!DOCTYPE html>
<html>

<head>
    <title>{{ $status == 'history' ? 'Riwayat' : 'Daftar' }} Hitung dan Padan Persediaan</title>
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
    <h1>{{ $status == 'history' ? 'Riwayat' : 'Daftar' }} Hitung dan Padan Persediaan</h1>
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
            @foreach($padans as $padan)
            <tr>
                <td>{{ $padan->padan_number }}</td>
                <td>{{ \Carbon\Carbon::parse($padan->padan_date)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($padan->padan_finish_date)->format('d-m-Y') }}</td>
                <td>{{ $padan->action }}</td>
                <td>{{ $padan->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>