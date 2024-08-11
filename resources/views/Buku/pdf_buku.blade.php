<!DOCTYPE html>
<html>
<head>
    <title>Daftar Buku</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Daftar Buku</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah</th>
                <th>File</th>
                <th>Cover</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($buku as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->kategoris_id }}</td>
                <td>{{ $item->deskripsi }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ $item->file }}</td>
                <td>{{ $item->cover }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
