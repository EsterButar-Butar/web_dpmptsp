@extends('partials.layouts.landing')

@section('title', 'Tentang')

@push('styles')
    @vite([
        'resources/css/home.css',
        'resources/css/about.css'
    ])
@endpush

@section('content')

@include('landing.about-section')

@endsection

@push('scripts')
    @vite(['resources/js/about.js'])
@endpush