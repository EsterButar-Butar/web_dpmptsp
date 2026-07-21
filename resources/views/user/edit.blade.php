@extends('partials.layouts.user')

@section('title','Ganti Password')

@section('content')

<div class="card">

    <div class="header">

        <div>

            <span class="badge">
                <i class="fa-solid fa-key"></i>
                Pengaturan
            </span>

            <h1>
                Ganti <span>Password</span>
            </h1>

        </div>

    </div>

    <div class="table-profile">

        @if (session('status') === 'password-updated')
            <div class="alert-success">
                Password berhasil diperbarui.
            </div>
        @endif

        <form method="POST"
              action="{{ route('user.password.update') }}">

            @csrf
            @method('PUT')

            <div class="form-group">

                <label>Password Lama</label>

                <input
                    type="password"
                    name="current_password"
                    class="form-control">

                @error('current_password', 'updatePassword')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            <div class="form-group">

                <label>Password Baru</label>

                <input
                    type="password"
                    name="password"
                    class="form-control">

                @error('password', 'updatePassword')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            <div class="form-group">

                <label>Konfirmasi Password Baru</label>

                <input
                    type="password"
                    name="password_confirmation"
                    class="form-control">

            </div>

            <button class="btn-save">

                Simpan Password

            </button>

        </form>

    </div>

</div>

@endsection