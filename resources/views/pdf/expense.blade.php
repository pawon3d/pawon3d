<!DOCTYPE html>
<html>

<head>
    <title>Daftar Belanja Persediaan</title>
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
    <h1>Daftar Belanja Persediaan</h1>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Status</th>
                <th>Jumlah Produk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}</td>
                <td>{{ $category->products_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>