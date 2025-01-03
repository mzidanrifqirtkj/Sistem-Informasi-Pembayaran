<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Santri List</h1>
<a href="{{ route('santri.create') }}" class="btn btn-primary">Add Santri</a>
<a href="{{ route('santri.importForm') }}" class="btn btn-primary">Import Santri</a>
<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>NIS</th>
            <th>Foto</th>
            <th>Action</th>
            <th>Detail</th>
        </tr>
    </thead>
    <tbody>
        @foreach($santris as $santri)
        <tr>
            <td>{{ $santri->nama_santri }}</td>
            <td>{{ $santri->nis }}</td>
            <td><img src="{{ asset('storage/' . $santri->foto) }}" alt="Foto Santri" style="max-width: 150px; height: auto;"></td>
            <td>
                <a href="{{ route('santri.edit', $santri->id_santri) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('santri.destroy', $santri->id_santri) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
            <td>
                <a href="{{ route('santri.show', $santri->id_santri) }}" class="btn btn-info">Detail</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
