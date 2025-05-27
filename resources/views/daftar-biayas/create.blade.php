@extends('layouts.home')
@section('title_page', isset($edit) ? 'Edit Daftar Biaya' : 'Tambah Daftar Biaya')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Tambah Daftar Biaya</h2>
                <form action="{{ route('daftar-biayas.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="status">Status Kategori</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Pilih Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kategori_biaya_id">Kategori Biaya</label>
                        <select name="kategori_biaya_id" id="kategori_biaya_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id_kategori_biaya }}" data-status="{{ $kategori->status }}">
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input type="number" name="nominal" id="nominal" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('daftar-biayas.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Filter categories based on selected status
            $('#status').change(function() {
                var selectedStatus = $(this).val();
                if (selectedStatus) {
                    $('#kategori_biaya_id option').each(function() {
                        var $option = $(this);
                        // Show only options with matching status or the default option
                        if ($option.data('status') === selectedStatus || $option.val() === '') {
                            $option.show();
                        } else {
                            $option.hide();
                        }
                    });
                    // Reset selection
                    $('#kategori_biaya_id').val('');
                } else {
                    // Show all options if no status is selected
                    $('#kategori_biaya_id option').show();
                }
            });
        });
    </script>
@endsection
