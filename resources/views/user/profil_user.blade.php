<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Profil Saya | DPMPTSP Sumatera Utara</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8faf9;
            color: #1f2937;
        }


        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            width: 100%;
            min-height: 76px;
            background: #17633f;

            display: flex;
            align-items: center;
        }

        .navbar-container {
            width: min(1200px, calc(100% - 40px));
            margin: auto;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;

            color: white;
            text-decoration: none;
        }

        .brand-logo {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .brand-title {
            font-size: 22px;
            font-weight: bold;
        }

        .brand-subtitle {
            display: block;
            margin-top: 2px;

            font-size: 11px;
            font-weight: normal;

            opacity: 0.85;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 35px;
        }

        .nav-link {
            position: relative;

            padding: 28px 0;

            color: white;
            text-decoration: none;

            font-size: 14px;
            font-weight: 600;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #ffd447;
        }

        .nav-link.active::after {
            content: "";

            position: absolute;

            left: 0;
            right: 0;
            bottom: 17px;

            height: 3px;

            background: #ffd447;
            border-radius: 10px;
        }

        .user-button {
            min-width: 95px;

            padding: 12px 20px;

            border-radius: 25px;

            background: #ffd447;
            color: #145237;

            text-align: center;
            text-decoration: none;

            font-size: 14px;
            font-weight: 700;
        }


        /* =========================
           PAGE
        ========================= */

        .page-container {
            width: min(1180px, calc(100% - 40px));

            margin: 50px auto;
        }

        .profile-card {
            display: grid;
            grid-template-columns: 310px 1fr;

            background: white;

            border: 1px solid #edf0f2;
            border-radius: 14px;

            overflow: hidden;

            box-shadow:
                0 8px 30px rgba(0, 0, 0, 0.06);
        }


        /* =========================
           LEFT PROFILE
        ========================= */

        .profile-left {
            padding: 45px 32px;

            text-align: center;

            border-right: 1px solid #e5e7eb;
        }

        .avatar {
            width: 150px;
            height: 150px;

            margin: 0 auto 25px;

            border-radius: 50%;

            background: #e7ebf0;

            display: flex;
            align-items: center;
            justify-content: center;

            overflow: hidden;
        }

        .avatar img {
            width: 100%;
            height: 100%;

            object-fit: cover;
        }

        .avatar-default {
            font-size: 75px;
            color: #9ca3af;
        }

        .profile-name {
            margin-bottom: 10px;

            font-size: 25px;
            font-weight: 700;
        }

        .profile-email {
            color: #718096;

            font-size: 15px;

            word-break: break-word;
        }

        .email-information {
            margin-top: 35px;

            padding: 17px;

            display: flex;
            align-items: flex-start;
            gap: 10px;

            border: 1px solid #dce4ec;
            border-radius: 10px;

            background: #fbfcfd;

            text-align: left;

            color: #64748b;

            font-size: 14px;
            line-height: 1.5;
        }

        .info-icon {
            color: #2563eb;
            font-size: 18px;
        }


        /* =========================
           RIGHT FORM
        ========================= */

        .profile-right {
            padding: 45px 55px;
        }

        .page-title {
            margin-bottom: 8px;

            font-size: 25px;
            color: #183b2b;
        }

        .page-description {
            margin-bottom: 32px;

            color: #64748b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;

            margin-bottom: 10px;

            font-size: 15px;
            font-weight: 600;

            color: #273142;
        }

        .form-control {
            width: 100%;

            padding: 15px 17px;

            border: 1px solid #d7dee7;
            border-radius: 8px;

            background: white;

            font-size: 16px;
            color: #475569;

            outline: none;

            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #17633f;

            box-shadow:
                0 0 0 3px rgba(23, 99, 63, 0.10);
        }

        textarea.form-control {
            min-height: 130px;

            resize: vertical;

            line-height: 1.5;
        }

        .form-footer {
            margin-top: 10px;
            padding-top: 25px;

            border-top: 1px solid #e5e7eb;

            display: flex;
            justify-content: flex-end;
        }

        .save-button {
            padding: 14px 25px;

            border: none;
            border-radius: 8px;

            background: #17633f;
            color: white;

            font-size: 15px;
            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }

        .save-button:hover {
            background: #104b30;
        }


        /* =========================
           ALERT
        ========================= */

        .alert-success {
            margin-bottom: 25px;

            padding: 14px 18px;

            background: #ecfdf3;

            border: 1px solid #a7f3d0;
            border-radius: 8px;

            color: #166534;

            font-size: 14px;
        }

        .error-message {
            display: block;

            margin-top: 7px;

            color: #dc2626;

            font-size: 13px;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {

            .nav-menu {
                display: none;
            }

            .profile-card {
                grid-template-columns: 1fr;
            }

            .profile-left {
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            .profile-right {
                padding: 35px 25px;
            }
        }

        @media (max-width: 500px) {

            .navbar-container {
                width: calc(100% - 24px);
            }

            .page-container {
                width: calc(100% - 24px);

                margin: 25px auto;
            }

            .brand-title {
                font-size: 18px;
            }

            .user-button {
                min-width: auto;

                padding: 10px 15px;
            }
        }
    </style>
</head>


<body>


    {{-- ==============================
         NAVBAR
    =============================== --}}

    <nav class="navbar">

        <div class="navbar-container">


            {{-- LOGO --}}

            <a
                href="{{ route('home') }}"
                class="brand"
            >

                <div>

                    <div class="brand-title">
                        DPMPTSP
                    </div>

                    <span class="brand-subtitle">
                        Provinsi Sumatera Utara
                    </span>

                </div>

            </a>


            {{-- MENU --}}

            <div class="nav-menu">

                <a
                    href="{{ route('home') }}"
                    class="nav-link"
                >
                    Beranda
                </a>


                <a
                    href="{{ route('about') }}"
                    class="nav-link"
                >
                    Tentang
                </a>


                <a
                    href="{{ route('investment.map') }}"
                    class="nav-link"
                >
                    Peta Investasi
                </a>

            </div>


            {{-- USER BUTTON --}}

            <a
                href="{{ route('user.profile.edit') }}"
                class="user-button"
            >
                {{ \Illuminate\Support\Str::before(
                    auth()->user()->name,
                    ' '
                ) }}
            </a>


        </div>

    </nav>



    {{-- ==============================
         PROFILE PAGE
    =============================== --}}

    <main class="page-container">

        <section class="profile-card">


            {{-- ======================
                 LEFT PROFILE
            ======================= --}}

            <aside class="profile-left">


                {{-- AVATAR --}}

                <div class="avatar">

                    @if ($user->avatar)

                        <img
                            src="{{ $user->avatar }}"
                            alt="Foto profil {{ $user->name }}"
                        >

                    @else

                        <div class="avatar-default">
                            👤
                        </div>

                    @endif

                </div>


                {{-- USER NAME --}}

                <h1 class="profile-name">
                    {{ $user->name }}
                </h1>


                {{-- USER EMAIL --}}

                <p class="profile-email">
                    {{ $user->email }}
                </p>


                {{-- INFORMATION --}}

                <div class="email-information">

                    <span class="info-icon">
                        ⓘ
                    </span>

                    <span>
                        Informasi email tidak dapat diubah.
                    </span>

                </div>


            </aside>



            {{-- ======================
                 RIGHT PROFILE FORM
            ======================= --}}

            <section class="profile-right">


                <h2 class="page-title">
                    Profil Saya
                </h2>


                <p class="page-description">
                    Perbarui informasi profil Anda.
                </p>


                {{-- SUCCESS MESSAGE --}}

                @if (session('success'))

                    <div class="alert-success">
                        {{ session('success') }}
                    </div>

                @endif


                {{-- FORM --}}

                <form
                    method="POST"
                    action="{{ route('user.profile.update') }}"
                >

                    @csrf

                    @method('PATCH')


                    {{-- NAME --}}

                    <div class="form-group">

                        <label
                            for="name"
                            class="form-label"
                        >
                            Nama Lengkap
                        </label>


                        <input
                            type="text"
                            id="name"
                            name="name"

                            class="form-control"

                            value="{{ old('name', $user->name) }}"

                            required
                            autocomplete="name"
                        >


                        @error('name')

                            <span class="error-message">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>



                    {{-- PHONE --}}

                    <div class="form-group">

                        <label
                            for="phone"
                            class="form-label"
                        >
                            Nomor Telepon
                        </label>


                        <input
                            type="text"
                            id="phone"
                            name="phone"

                            class="form-control"

                            value="{{ old('phone', $user->phone) }}"

                            placeholder="Contoh: 0812 3456 7890"
                        >


                        @error('phone')

                            <span class="error-message">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>



                    {{-- ADDRESS --}}

                    <div class="form-group">

                        <label
                            for="address"
                            class="form-label"
                        >
                            Alamat
                        </label>


                        <textarea
                            id="address"
                            name="address"

                            class="form-control"

                            placeholder="Masukkan alamat lengkap"
                        >{{ old('address', $user->address) }}</textarea>


                        @error('address')

                            <span class="error-message">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>



                    {{-- SAVE BUTTON --}}

                    <div class="form-footer">

                        <button
                            type="submit"
                            class="save-button"
                        >
                            💾 Simpan Perubahan
                        </button>

                    </div>


                </form>


            </section>


        </section>

    </main>


</body>

</html>