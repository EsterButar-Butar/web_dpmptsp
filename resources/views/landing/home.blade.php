@extends('layouts.landing')


@section('title', 'Beranda | DPMPTSP Provinsi Sumatera Utara')


@section('content')


{{-- NAVBAR --}}
@include('partials.landing.navbar')


{{-- HERO --}}
<section id="hero" class="hero">

    <div class="hero-content">

        <h5>Selamat Datang</h5>

        <h1>
            Dashboard Analisis <br>
            Potensi Investasi
        </h1>


        <p>
            Dashboard Analisis Potensi Investasi berbasis GIS,
            PDRB, Location Quotient, Shift Share,
            Tipologi Klassen, dan Tipologi Sektor
            untuk Provinsi Sumatera Utara.
        </p>


        <div class="hero-button">

            <a href="{{ route('login') }}" class="btn1">

                Mulai Analisis

            </a>

        </div>


        <div class="hero-stats">

            <div>

                <h3>33</h3>

                <span>
                    Kabupaten/Kota
                </span>

            </div>


            <div>

                <h3>4</h3>

                <span>
                    Metode Analisis
                </span>

            </div>


            <div>

                <h3>GIS</h3>

                <span>
                    Pemetaan Investasi
                </span>

            </div>

        </div>


    </div>


    <div class="hero-image">

        <img
            src="{{ asset('images/gedung-dpmptsp.jpg') }}"
            alt="Gedung DPMPTSP"
        >

    </div>


</section>



{{-- ABOUT --}}
@include('landing.about')



{{-- FOOTER --}}
{{-- @include('partials.landing.footer') --}}



@endsection