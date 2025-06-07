<!DOCTYPE html>
<html>

<head>
    <title>Daftar Peran</title>
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
    <h1>Daftar Peran</h1>
    <table>
        <thead>
            <tr>
                <th>Peran</th>
                <th>Akses</th>
                <th>Jumlah Pekerja</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td>{{ $role->permissions->count() > 0 ? $role->permissions->pluck('name')->implode(', ') : 'Tidak ada
                    akses' }}</td>
                <td>{{ $role->users->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>