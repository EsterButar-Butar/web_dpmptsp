<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

                <a href="{{ route('analysis.index') }}" class="btn1">
                    Mulai Analisis
                </a>

                <a href="{{ route('comparison.index') }}" class="btn1">
                    Analisis Perbandingan
                </a>

            </div>

            <div class="hero-stats">

                <div>
                    <h3>33</h3>
                    <span>Kabupaten/Kota</span>
                </div>

                <div>
                    <h3>4</h3>
                    <span>Metode Analisis</span>
                </div>

                <div>
                    <h3>GIS</h3>
                    <span>Pemetaan Investasi</span>
                </div>

            </div>

        </div>

        <div class="hero-image">

            <img
                src="{{ asset('images/gedung-dpmptsp.jpg') }}"
                alt="Gedung DPMPTSP">

        </div>

    </section>

    {{-- ABOUT --}}
    @include('landing.about')

    {{-- FOOTER --}}
    {{-- @include('partials.landing.footer') --}}

</body>

</html>