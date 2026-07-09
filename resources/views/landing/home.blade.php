@extends('layouts.landing')

@section('title', 'Beranda | DPMPTSP Provinsi Sumatera Utara')

@section('content')


<section class="hero-section">

<<<<<<< HEAD
    <div class="container hero-container">
=======
        'resources/js/navbar.js',
        'resources/js/home.js',
        'resources/js/about.js',
    ])

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

    {{-- NAVBAR --}}
    @include('partials.landing.navbar')

    {{-- HERO --}}
    <section id="hero" class="hero">
>>>>>>> 55f46de (Update tabel database)

        <div class="hero-content">

            <div class="hero-badge">
                Potensi Investasi Sumatera Utara
            </div>


            <h1>
                Temukan Peluang
                <br>
                Investasi
                <span>
                    Terbaik di Sumatera Utara
                </span>
            </h1>


            <p>
                Jelajahi data, potensi daerah,
                sektor unggulan, dan peluang investasi
                strategis di seluruh kabupaten dan kota
                Provinsi Sumatera Utara.
            </p>


            <div class="hero-actions">

                <a 
                    href="{{ route('analysis.index') }}"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-chart-column"></i>

                    Analisis Sekarang

                </a>


                <a
                    href="{{ route('investment.map') }}"
                    class="btn btn-outline"
                >

                    <i class="fa-solid fa-map-location-dot"></i>

                    Jelajahi Peta

                </a>

            </div>


        </div>

    </div>


</section>




<section class="statistics-section">

    <div class="container">

        <div class="statistics-grid">


            <div class="stat-card">

                <div class="stat-icon">

                    <i class="fa-solid fa-city"></i>

                </div>


                <div class="stat-content">

                    <strong>33</strong>

                    <span>Kabupaten/Kota</span>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon">

                    <i class="fa-solid fa-chart-line"></i>

                </div>


                <div class="stat-content">

                    <strong>4</strong>

                    <span>Metode Analisis</span>

                </div>

            </div>




            <div class="stat-card">

                <div class="stat-icon">

                    <i class="fa-solid fa-map"></i>

                </div>


                <div class="stat-content">

                    <strong>GIS</strong>

                    <span>Pemetaan Investasi</span>

                </div>

            </div>



            <div class="stat-card">

                <div class="stat-icon">

                    <i class="fa-solid fa-database"></i>

                </div>


                <div class="stat-content">

                    <strong>PDRB</strong>

                    <span>Data Ekonomi</span>

                </div>

            </div>


        </div>

    </div>


</section>

@endsection