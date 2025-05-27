<!DOCTYPE html>
<html>

<head>
    <title>Daftar Barang Persediaan</title>
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
    <h1>Daftar Barang Persediaan</h1>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jumlah Tersedia</th>
                <th>Tanggal Expired</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materials as $material)
            <tr>
                <td>{{ $material->name }}</td>
                <td>{{ $material->supply_quantity }} {{ $material->unit_alias }}</td>
                <td>{{ $material->products_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>