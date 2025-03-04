@extends('layouts.home')
@section('title_page', 'Import Absensi')
@section('content')

    <h2>Import Absensi</h2>
    <form action="{{ route('absensi.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Pilih File Excel</label>
            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror">
            <span class="text-small text-danger font-italic">File extension only: xlsx,xls,csv | Max Upload Image is 2048
                Kb</span>
            @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary mt-3">Import</button>
    </form>

@endsection
