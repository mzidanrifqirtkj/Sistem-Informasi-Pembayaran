@extends('layouts.home')
@section('title_page','Edit Item ')
@section('content')

    <form action="{{ route('admin.tambahan_bulanan.update', $item) }}" method="post">
        @csrf
        @method('PUT')
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nama_item">Nama Item</label>
                        <input id="nama_item" type="text" class="form-control @error('nama_item') is-invalid @enderror" name="nama_item" value="{{ old('nama_item', $item->nama_item)}}" required>
                        @error('nama_item')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input id="nominal" type="number" class="form-control @error('nominal') is-invalid @enderror" name="nominal" value="{{ old('nominal', $item->nominal) }}" required autocomplete="nominal" min=0>

                        @error('nominal')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <button class="btn btn-primary">Edit</button>
                        <a href="{{ route('admin.tambahan_bulanan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
