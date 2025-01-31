@extends('santris.layouts.home')
@section('title_page', 'Dashboard')
@section('content')
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <h1>SANTRI</h1>
@endsection
