@extends('layouts.home')
@section('title_page', 'Dashboard')
@section('content')
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <h5>Selamat datang di Dashboard</h5>
        </div>
    </div>
@endsection
