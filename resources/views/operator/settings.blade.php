@extends('layouts.operator')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Pengaturan Akun</h1>
        <p class="text-slate-500 mt-1">Kelola preferensi dan keamanan akun operator Anda.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Settings -->
        <div class="col-span-1 space-y-2">
            <a href="#" class="block px-4 py-3 bg-white border-l-4 border-emerald-600 rounded-r-lg shadow-sm font-medium text-emerald-700">
                Keamanan & Password
            </a>
            <a href="#" class="block px-4 py-3 hover:bg-white border-l-4 border-transparent hover:border-slate-300 rounded-r-lg transition-colors font-medium text-slate-600">
                Log Aktivitas
            </a>
        </div>

        <!-- Main Settings Form -->
        <div class="col-span-1 lg:col-span-2 space-y-6">
            <!-- Ubah Password -->
            <form action="{{ route('operator.settings.password') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                @csrf
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-semibold text-slate-800">Ubah Password</h2>
                </div>
                <div class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">Password Saat Ini</label>
                        <input type="password" name="current_password" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" placeholder="••••••••" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Password Baru</label>
                            <input type="password" name="new_password" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" placeholder="Minimal 8 karakter" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all outline-none" placeholder="Ketik ulang password baru" required>
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm shadow-emerald-200 transition-all">Perbarui Password</button>
                    </div>
                </div>
            </form>

            <!-- Autentikasi Dua Faktor (2FA) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-semibold text-slate-800">Autentikasi Dua Faktor (2FA)</h2>
                </div>
                <div class="p-6 flex flex-col md:flex-row gap-6 items-start md:items-center justify-between">
                    <div>
                        <h3 class="font-medium text-slate-800">Tingkatkan Keamanan Akun</h3>
                        <p class="text-sm text-slate-500 mt-1 max-w-md">Aktifkan Autentikasi Dua Langkah untuk lapisan keamanan ekstra saat login ke sistem operator.</p>
                    </div>
                    <form action="{{ route('operator.settings.2fa') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors whitespace-nowrap">
                            Aktifkan 2FA
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
