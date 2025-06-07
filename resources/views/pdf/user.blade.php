<!DOCTYPE html>
<html>

<head>
    <title>Daftar Pekerja</title>
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
    <h1>Daftar Pekerja</h1>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Nomor Telepon</th>
                <th>Peran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>{{ $user->getRoleNames()->first() ?? 'Tidak ada peran' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>