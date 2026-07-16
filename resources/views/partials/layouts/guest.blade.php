<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', config('app.name', 'DPMPTSP WebGIS'))
    </title>

    {{-- Fonts --}}
    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap"
        rel="stylesheet"
    >

    {{-- Vite --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- CSS tambahan halaman --}}
    @stack('styles')
</head>


<body class="font-sans text-gray-900 antialiased">

    <main
        class="
            min-h-screen
            flex
            flex-col
            items-center
            justify-center
            px-4
            py-8
            bg-gray-100
        "
    >

        {{-- Logo --}}
        <div class="mb-6">

            <a
                href="{{ route('home') }}"
                aria-label="Kembali ke halaman utama"
            >

                <x-application-logo
                    class="
                        w-20
                        h-20
                        fill-current
                        text-green-700
                    "
                />

            </a>

        </div>


        {{-- Authentication Card --}}
        <div
            class="
                w-full
                max-w-md
                px-6
                py-6
                bg-white
                shadow-md
                overflow-hidden
                rounded-xl
            "
        >

            {{ $slot }}

        </div>


        {{-- Back to Home --}}
        <div class="mt-6">

            <a
                href="{{ route('home') }}"
                class="
                    text-sm
                    font-medium
                    text-green-700
                    hover:text-green-900
                    transition
                "
            >
                ← Kembali ke Beranda
            </a>

        </div>

    </main>


    {{-- JavaScript tambahan halaman --}}
    @stack('scripts')

</body>

</html>