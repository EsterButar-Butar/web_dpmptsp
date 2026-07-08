@extends('layouts.landing')


@section('title', 'Beranda - Sumatera Investment')


@section('content')


{{-- =====================================================
    HERO SECTION
===================================================== --}}

<section class="hero-section">

    <div class="hero-overlay"></div>

    <div class="container hero-container">

        <div class="hero-content">

            <span class="hero-badge">
                Potensi Investasi Sumatera Utara
            </span>


            <h1>
                Temukan Peluang Investasi
                <span>Terbaik di Sumatera Utara</span>
            </h1>


            <p>
                Jelajahi data, potensi daerah, sektor unggulan,
                dan peluang investasi strategis di seluruh
                kabupaten dan kota Provinsi Sumatera Utara.
            </p>


            <div class="hero-actions">

                <a
                    href="{{ url('/peta-investasi') }}"
                    class="btn btn-primary"
                >
                    <i class="fa-solid fa-map-location-dot"></i>

                    Jelajahi Peta
                </a>


                <a
                    href="{{ route('analysis') }}"
                    class="btn btn-outline"
                >
                    <i class="fa-solid fa-chart-column"></i>

                    Analisis Sekarang
                </a>

            </div>

        </div>

    </div>

</section>



{{-- =====================================================
    STATISTICS SECTION
===================================================== --}}

<section class="statistics-section">

    <div class="container">

        <div class="statistics-header">

            <span class="section-label">
                DATA INVESTASI
            </span>

            <h2>
                Investasi Sumatera Utara dalam Angka
            </h2>

            <p>
                Ringkasan data investasi, proyek, wilayah,
                dan penyerapan tenaga kerja di Sumatera Utara.
            </p>

        </div>


        <div class="statistics-grid">


            {{-- =================================================
                CARD 1 - TOTAL INVESTASI
            ================================================== --}}

            <article class="stat-card">

                <div class="stat-icon">

                    <i class="fa-solid fa-chart-line"></i>

                </div>


                <div class="stat-content">

                    <span class="stat-label">
                        Total Investasi
                    </span>


                    <strong class="stat-value">
                        Rp 48,7 T
                    </strong>


                    <small class="stat-description">

                        <i class="fa-solid fa-arrow-trend-up"></i>

                        Pertumbuhan investasi daerah

                    </small>

                </div>

            </article>



            {{-- =================================================
                CARD 2 - TOTAL PROYEK
            ================================================== --}}

            <article class="stat-card">

                <div class="stat-icon">

                    <i class="fa-solid fa-building"></i>

                </div>


                <div class="stat-content">

                    <span class="stat-label">
                        Total Proyek
                    </span>


                    <strong class="stat-value">
                        1.245
                    </strong>


                    <small class="stat-description">

                        <i class="fa-solid fa-folder-open"></i>

                        Proyek investasi terdaftar

                    </small>

                </div>

            </article>



            {{-- =================================================
                CARD 3 - KABUPATEN / KOTA
            ================================================== --}}

            <article class="stat-card">

                <div class="stat-icon">

                    <i class="fa-solid fa-map-location-dot"></i>

                </div>


                <div class="stat-content">

                    <span class="stat-label">
                        Kabupaten / Kota
                    </span>


                    <strong class="stat-value">
                        33
                    </strong>


                    <small class="stat-description">

                        <i class="fa-solid fa-location-dot"></i>

                        Wilayah Sumatera Utara

                    </small>

                </div>

            </article>



            {{-- =================================================
                CARD 4 - TENAGA KERJA
            ================================================== --}}

            <article class="stat-card">

                <div class="stat-icon">

                    <i class="fa-solid fa-briefcase"></i>

                </div>


                <div class="stat-content">

                    <span class="stat-label">
                        Tenaga Kerja
                    </span>


                    <strong class="stat-value">
                        125K+
                    </strong>


                    <small class="stat-description">

                        <i class="fa-solid fa-users"></i>

                        Penyerapan tenaga kerja

                    </small>

                </div>

            </article>


        </div>

    </div>

</section>



{{-- =====================================================
    MAP INVESTASI
===================================================== --}}

<section class="section map-section">

    <div class="container">


        {{-- SECTION HEADER --}}

        <div class="section-header">

            <div class="section-heading-content">

                <span class="section-label">
                    WEBGIS INVESTASI
                </span>

                <h2>
                    Peta Potensi Investasi
                </h2>

                <p>
                    Temukan persebaran proyek dan potensi investasi
                    di seluruh wilayah Sumatera Utara.
                </p>

            </div>


            <a
                href="{{ route('investment.map') }}"
                class="text-link"
            >
                Buka Peta Lengkap

                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>



        {{-- MAP CONTAINER --}}

        <div class="map-wrapper">


            {{-- GIS MAP --}}

            <div
                id="investmentMap"
                class="investment-map"
            ></div>



            {{-- FILTER PANEL --}}

            <aside class="map-filter">


                <div class="map-filter-header">

                    <div class="filter-header-icon">

                        <i class="fa-solid fa-filter"></i>

                    </div>


                    <div>

                        <h3>
                            Filter Investasi
                        </h3>

                        <span>
                            Sesuaikan data pada peta
                        </span>

                    </div>

                </div>



                <div class="filter-group">

                    <label for="regionFilter">

                        <i class="fa-solid fa-location-dot"></i>

                        Kabupaten / Kota

                    </label>


                    <select id="regionFilter">

                        <option value="all">
                            Semua Wilayah
                        </option>

                        <option value="medan">
                            Kota Medan
                        </option>

                        <option value="deli-serdang">
                            Deli Serdang
                        </option>

                        <option value="simalungun">
                            Simalungun
                        </option>

                    </select>

                </div>



                <div class="filter-group">

                    <label for="sectorFilter">

                        <i class="fa-solid fa-layer-group"></i>

                        Sektor

                    </label>


                    <select id="sectorFilter">

                        <option value="all">
                            Semua Sektor
                        </option>

                        <option value="industri">
                            Industri
                        </option>

                        <option value="pertanian">
                            Pertanian
                        </option>

                        <option value="pariwisata">
                            Pariwisata
                        </option>

                        <option value="energi">
                            Energi
                        </option>

                    </select>

                </div>



                <button
                    type="button"
                    class="filter-button"
                    id="applyMapFilter"
                >

                    <i class="fa-solid fa-magnifying-glass-location"></i>

                    Terapkan Filter

                </button>


                <button
                    type="button"
                    class="reset-filter-button"
                    id="resetMapFilter"
                >

                    <i class="fa-solid fa-rotate-left"></i>

                    Reset Filter

                </button>


            </aside>


        </div>


    </div>

</section>



{{-- =====================================================
    INVESTMENT SECTORS
===================================================== --}}

<section class="section sector-section">

    <div class="container">


        <div class="section-title-center">

            <span class="section-label">
                SEKTOR UNGGULAN
            </span>

            <h2>
                Potensi Investasi Sumatera Utara
            </h2>

            <p>
                Jelajahi berbagai sektor unggulan dengan potensi
                investasi yang menjanjikan.
            </p>

        </div>


        <div class="sector-grid">


            <article class="sector-card">

                <div class="sector-icon">
                    <i class="fa-solid fa-industry"></i>
                </div>

                <h3>
                    Industri
                </h3>

                <p>
                    Peluang pengembangan kawasan industri
                    dan manufaktur.
                </p>

                <a href="#">
                    Lihat Potensi
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </article>



            <article class="sector-card">

                <div class="sector-icon">
                    <i class="fa-solid fa-seedling"></i>
                </div>

                <h3>
                    Pertanian
                </h3>

                <p>
                    Potensi agribisnis dan komoditas unggulan
                    Sumatera Utara.
                </p>

                <a href="#">
                    Lihat Potensi
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </article>



            <article class="sector-card">

                <div class="sector-icon">
                    <i class="fa-solid fa-umbrella-beach"></i>
                </div>

                <h3>
                    Pariwisata
                </h3>

                <p>
                    Destinasi wisata dan pengembangan ekonomi
                    kreatif daerah.
                </p>

                <a href="#">
                    Lihat Potensi
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </article>



            <article class="sector-card">

                <div class="sector-icon">
                    <i class="fa-solid fa-bolt"></i>
                </div>

                <h3>
                    Energi
                </h3>

                <p>
                    Peluang energi terbarukan dan sumber daya
                    energi strategis.
                </p>

                <a href="#">
                    Lihat Potensi
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </article>


        </div>

    </div>

</section>



{{-- =====================================================
    CTA
===================================================== --}}

<section class="cta-section">

    <div class="container">

        <div class="cta-box">

            <div>

                <span>
                    MULAI BERINVESTASI
                </span>

                <h2>
                    Temukan Peluang Investasi Anda
                    di Sumatera Utara
                </h2>

                <p>
                    Dapatkan informasi potensi daerah dan
                    proyek investasi strategis.
                </p>

            </div>


            <a
                href="{{ url('/kontak') }}"
                class="btn btn-gold"
            >
                Hubungi Kami

                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </div>

</section>


@endsection