<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'DPMPTSP Provinsi Sumatera Utara')
    </title>


    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    {{-- Font Awesome --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    {{-- VITE --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])


    @stack('styles')

</head>


<body>


    {{-- NAVBAR --}}

    @include('partials.landing.navbar')



    {{-- PAGE CONTENT --}}

    <main>

        @yield('content')

    </main>



    {{-- FOOTER --}}

    @include('partials.landing.footer')



    {{-- BACK TO TOP --}}

    <button
        type="button"
        class="back-to-top"
        id="backToTop"
        aria-label="Kembali ke atas"
    >

        <i class="fa-solid fa-arrow-up"></i>

    </button>


    @stack('scripts')


</body>

</html>