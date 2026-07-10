@extends('layouts.profile')

@section(
    'title',
    'Profil Saya | DPMPTSP Provinsi Sumatera Utara'
)


@section('content')

<section class="user-profile-page">


    {{-- PAGE HEADER --}}
    <header class="user-profile-header">

        <h1>
            Profil User
        </h1>

        <p>
            Informasi
        </p>

    </header>


    <div class="user-profile-divider"></div>


    {{-- INFORMATION GRID --}}
    <div class="user-information-grid">


        {{-- NAME --}}
        <article class="user-info-card">

            <span class="user-info-label">
                Nama Lengkap
            </span>

            <strong class="user-info-value">
                {{ $user->name ?? '-' }}
            </strong>

        </article>


        {{-- EMAIL --}}
        <article class="user-info-card">

            <span class="user-info-label">
                Alamat Email
            </span>

            <strong class="user-info-value">
                {{ $user->email ?? '-' }}
            </strong>

        </article>


        {{-- ROLE --}}
        <article class="user-info-card">

            <span class="user-info-label">
                Role
            </span>

            <strong class="user-info-value">
                {{ ucfirst($user->role ?? 'User') }}
            </strong>

        </article>


        {{-- EMAIL STATUS --}}
        <article class="user-info-card">

            <span class="user-info-label">
                Status Email
            </span>


            @if ($user->email_verified_at)

                <span class="email-status verified">

                    <i class="fa-solid fa-circle-check"></i>

                    Terverifikasi

                </span>

            @else

                <span class="email-status unverified">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    Belum Terverifikasi

                </span>

            @endif

        </article>


        {{-- CREATED DATE --}}
        <article class="user-info-card">

            <span class="user-info-label">
                Bergabung Sejak
            </span>

            <strong class="user-info-value">

                {{ $user->created_at
                    ? $user->created_at->format('d F Y')
                    : '-'
                }}

            </strong>

        </article>


    </div>

</section>

@endsection