<!DOCTYPE html>
<html>

<head>
    <title>Daftar Toko Persediaan</title>
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
    <h1>Daftar Toko Persediaan</h1>
    <table>
        <thead>
            <tr>
                <th>Nama Toko</th>
                <th>Nama Kontak</th>
                <th>Nomor Telepon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->contact_name }}</td>
                <td>{{ $supplier->phone }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>