@extends('layouts.admin')

@section('title', 'Pengguna')

@section('content')
@php
    $isModalOpen = in_array($mode, ['create', 'edit']);
    $isEdit = $mode === 'edit' && $editData;

    $formAction = $isEdit
        ? route('admin.pengguna.update', $editData->id)
        : route('admin.pengguna.store');
@endphp

<style>
    :root {
        --green-dark: #255d3e;
        --green-main: #2f6b48;
        --green-soft: #eaf7ef;
        --green-pale: #f8fcf9;
        --yellow-soft: #fff8e6;
        --blue-soft: #eef7ff;
        --red-soft: #fff3f3;
        --navy: #14213d;
        --text-dark: #243042;
        --text-muted: #667085;
        --border: #e1e7ef;
        --white: #ffffff;
        --danger: #ef4444;
    }

    .pengguna-page {
        min-height: 100vh;
        padding: 28px 30px;
        background: #f8faf8;
        font-family: 'Poppins', sans-serif;
        color: var(--text-dark);
    }

    .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 22px;
    }

    .page-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--green-soft);
        color: var(--green-dark);
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .page-heading h1 {
        margin: 0;
        color: var(--green-dark);
        font-size: 30px;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .page-heading p {
        margin: 8px 0 0;
        color: var(--text-muted);
        font-size: 14px;
        font-weight: 400;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: var(--green-dark);
        color: #ffffff;
        border: none;
        border-radius: 16px;
        padding: 14px 20px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 12px 28px rgba(37, 93, 62, 0.16);
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: #1f4f35;
        color: #ffffff;
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
        background: #ffffff;
        color: var(--text-muted);
        border-radius: 14px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 14px;
        margin-bottom: 18px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-success {
        color: #166534;
        background: #dcfce7;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        color: #991b1b;
        background: #fee2e2;
        border: 1px solid #fecaca;
    }

    .user-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .user-stat-card {
        min-height: 162px;
        padding: 22px;
        border-radius: 22px;
        border: 1px solid rgba(255, 255, 255, 0.75);
        box-shadow: 0 14px 30px rgba(31, 41, 55, 0.05);
        overflow: hidden;
    }

    .user-stat-mint {
        background: linear-gradient(135deg, #ecfbf4 0%, #f8fdfa 100%);
    }

    .user-stat-cream {
        background: linear-gradient(135deg, #fff8e6 0%, #fffdf5 100%);
    }

    .user-stat-blue {
        background: linear-gradient(135deg, #eef7ff 0%, #f8fbff 100%);
    }

    .user-stat-red {
        background: linear-gradient(135deg, #fff2f2 0%, #fff9f9 100%);
    }

    .user-stat-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .user-stat-label {
        color: #344054;
        font-size: 15px;
        line-height: 1.5;
        font-weight: 500;
        max-width: 130px;
    }

    .user-stat-icon {
        width: 58px;
        height: 58px;
        border-radius: 20px;
        background: rgba(20, 33, 61, 0.10);
        color: var(--navy);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
        flex-shrink: 0;
    }

    .user-stat-value {
        color: var(--text-dark);
        font-size: 34px;
        line-height: 1;
        font-weight: 700;
        margin-bottom: 14px;
    }

    .user-stat-footer {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-top: 13px;
        border-top: 1px solid rgba(20, 33, 61, 0.07);
        color: #38a78f;
        font-size: 14px;
        font-weight: 500;
    }

    .toolbar {
        display: grid;
        grid-template-columns: 230px minmax(280px, 1fr) 150px;
        gap: 14px;
        align-items: center;
        margin-bottom: 20px;
    }

    .filter-control,
    .search-control {
        position: relative;
        height: 52px;
        background: #ffffff;
        border: 1px solid #d9dee8;
        border-radius: 18px;
        box-shadow: 0 8px 20px rgba(31, 41, 55, 0.035);
        display: flex;
        align-items: center;
    }

    .filter-control select,
    .search-control input {
        width: 100%;
        height: 100%;
        border: none;
        outline: none;
        background: transparent;
        padding: 0 48px 0 18px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 400;
        color: var(--text-dark);
        appearance: none;
    }

    .filter-control i {
        position: absolute;
        right: 18px;
        color: var(--green-dark);
        pointer-events: none;
    }

    .search-control button {
        position: absolute;
        right: 9px;
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 12px;
        background: var(--green-soft);
        color: var(--green-dark);
        cursor: pointer;
        font-size: 15px;
    }

    .btn-reset {
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 1px solid #d9dee8;
        border-radius: 18px;
        text-decoration: none;
        color: var(--green-dark);
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 8px 20px rgba(31, 41, 55, 0.035);
    }

    .table-card {
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #dce3ec;
        border-radius: 24px;
        box-shadow: 0 16px 30px rgba(31, 41, 55, 0.05);
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .data-table thead {
        background: #fbfcfd;
    }

    .data-table th {
        text-align: left;
        padding: 20px 24px;
        color: #344054;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        border-bottom: 1px solid #e8edf3;
    }

    .data-table td {
        padding: 24px 24px;
        border-bottom: 1px solid #edf2f7;
        color: var(--text-dark);
        font-size: 14px;
        font-weight: 400;
        vertical-align: middle;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .avatar {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--green-dark), #f4cf63);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .user-name {
        font-size: 15px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .user-email {
        font-size: 13px;
        font-weight: 400;
        color: var(--text-muted);
    }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        line-height: 1;
        white-space: nowrap;
    }

    .badge-role {
        background: var(--yellow-soft);
        color: #916a06;
    }

    .badge-user {
        background: var(--green-soft);
        color: var(--green-dark);
    }

    .badge-operator {
        background: var(--blue-soft);
        color: #2563eb;
    }

    .badge-active {
        background: #d7f7c8;
        color: #2b8f3d;
    }

    .badge-suspend {
        background: #ffe1e1;
        color: #dc2626;
    }

    .action-group {
        display: inline-flex;
        border: 1px solid #dde4ed;
        border-radius: 14px;
        overflow: hidden;
        background: #ffffff;
    }

    .action-btn {
        width: 46px;
        height: 40px;
        border: none;
        background: #ffffff;
        color: #667085;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        border-right: 1px solid #dde4ed;
        font-size: 15px;
    }

    .action-btn:last-child {
        border-right: none;
    }

    .action-btn:hover {
        background: var(--green-soft);
        color: var(--green-dark);
    }

    .action-btn.delete {
        color: var(--danger);
    }

    .empty-state {
        text-align: center;
        padding: 42px 20px;
        color: var(--text-muted);
        font-size: 14px;
    }

    .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 6px 0;
    }

    .pagination-info {
        color: var(--text-muted);
        font-size: 14px;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .page-link {
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: var(--navy);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 500;
    }

    .page-link.active {
        background: var(--green-dark);
        color: #ffffff;
        border-color: var(--green-dark);
    }

    .page-link.disabled {
        pointer-events: none;
        opacity: 0.45;
        background: #f3f4f6;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.38);
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .modal-card {
        width: min(760px, 100%);
        max-height: 92vh;
        overflow-y: auto;
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid var(--border);
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.22);
        padding: 24px;
    }

    .modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
    }

    .modal-header h2 {
        margin: 0;
        color: var(--green-dark);
        font-size: 22px;
        font-weight: 600;
    }

    .modal-header p {
        margin: 6px 0 0;
        color: var(--text-muted);
        font-size: 14px;
    }

    .btn-close {
        width: 40px;
        height: 40px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: #ffffff;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--navy);
        font-size: 14px;
        font-weight: 500;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        height: 48px;
        border: 1px solid #d9dee8;
        border-radius: 14px;
        padding: 0 14px;
        outline: none;
        background: #ffffff;
        color: var(--text-dark);
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 22px;
    }

    .error-text {
        margin-top: 7px;
        color: #dc2626;
        font-size: 13px;
    }

    @media (max-width: 1200px) {
        .user-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .toolbar {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .pengguna-page {
            padding: 18px;
        }

        .page-heading {
            flex-direction: column;
            align-items: stretch;
        }

        .user-stats-grid,
        .toolbar,
        .form-grid {
            grid-template-columns: 1fr;
        }

        .pagination-wrap {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="pengguna-page">
    <div class="page-heading">
        <div>
            <div class="page-kicker">
                <i class="fa-solid fa-user-shield"></i>
                Profil Admin
            </div>

            <h1>Manajemen Pengguna</h1>

            <p>Kelola pengguna yang terdaftar melalui sistem register.</p>
        </div>

        <a
            href="{{ route('admin.pengguna.index', array_merge(request()->query(), ['mode' => 'create'])) }}"
            class="btn-primary"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Pengguna
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="user-stats-grid">
        @foreach ($stats as $stat)
            <div class="user-stat-card user-stat-{{ $stat['color'] }}">
                <div class="user-stat-header">
                    <div class="user-stat-label">
                        {{ $stat['label'] }}
                    </div>

                    <div class="user-stat-icon">
                        <i class="fa-solid {{ $stat['icon'] }}"></i>
                    </div>
                </div>

                <div class="user-stat-value">
                    {{ $stat['value'] }}
                </div>

                <div class="user-stat-footer">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>Total Data</span>
                </div>
            </div>
        @endforeach
    </div>

    <form
        action="{{ route('admin.pengguna.index') }}"
        method="GET"
        class="toolbar"
    >
        @if ($hasRoleColumn)
            <div class="filter-control">
                <select
                    name="role"
                    onchange="this.form.submit()"
                >
                    <option value="">Pilih Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="operator" {{ request('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                </select>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
        @endif

        <div class="search-control">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama, email, role..."
            >

            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <a
            href="{{ route('admin.pengguna.index') }}"
            class="btn-reset"
        >
            Reset
        </a>
    </form>

    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NOMOR</th>
                        <th>PENGGUNA</th>
                        <th>ROLE</th>
                        <th>STATUS</th>
                        <th>TERDAFTAR</th>
                        <th>AKSI</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengguna as $item)
                        @php
                            $nomor = ($pengguna->currentPage() - 1) * $pengguna->perPage() + $loop->iteration;

                            $role = $hasRoleColumn
                                ? strtolower(trim($item->role ?? 'user'))
                                : 'user';

                            $status = $hasStatusColumn
                                ? ($item->status ?? 'Aktif')
                                : 'Aktif';

                            $statusLower = strtolower(trim($status));

                            $initials = collect(explode(' ', $item->name))
                                ->filter()
                                ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                                ->take(2)
                                ->implode('');
                        @endphp

                        <tr>
                            <td>{{ str_pad($nomor, 5, '0', STR_PAD_LEFT) }}</td>

                            <td>
                                <div class="user-cell">
                                    <div class="avatar">
                                        {{ $initials ?: 'U' }}
                                    </div>

                                    <div>
                                        <div class="user-name">
                                            {{ $item->name }}
                                        </div>
                                        <div class="user-email">
                                            {{ $item->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge {{ $role === 'admin' ? 'badge-role' : ($role === 'operator' ? 'badge-operator' : 'badge-user') }}">
                                    {{ ucfirst($role) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge {{ $statusLower === 'suspend' ? 'badge-suspend' : 'badge-active' }}">
                                    {{ $status }}
                                </span>
                            </td>

                            <td>
                                {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                            </td>

                            <td>
                                <div class="action-group">
                                    <a
                                        href="{{ route('admin.pengguna.index', array_merge(request()->query(), ['edit' => $item->id])) }}"
                                        class="action-btn"
                                        title="Edit"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>

                                    <form
                                        action="{{ route('admin.pengguna.destroy', $item->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="action-btn delete"
                                            title="Hapus"
                                        >
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($pengguna->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-info">
                Menampilkan {{ $pengguna->firstItem() }} - {{ $pengguna->lastItem() }}
                dari {{ $pengguna->total() }} pengguna
            </div>

            <div class="pagination">
                <a
                    href="{{ $pengguna->previousPageUrl() ?? '#' }}"
                    class="page-link {{ $pengguna->onFirstPage() ? 'disabled' : '' }}"
                >
                    <i class="fa-solid fa-chevron-left"></i>&nbsp; PREV
                </a>

                @foreach ($pengguna->getUrlRange(1, $pengguna->lastPage()) as $page => $url)
                    <a
                        href="{{ $url }}"
                        class="page-link {{ $page === $pengguna->currentPage() ? 'active' : '' }}"
                    >
                        {{ $page }}
                    </a>
                @endforeach

                <a
                    href="{{ $pengguna->nextPageUrl() ?? '#' }}"
                    class="page-link {{ $pengguna->hasMorePages() ? '' : 'disabled' }}"
                >
                    NEXT &nbsp;<i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>
        </div>
    @endif
</div>

@if ($isModalOpen)
    <div class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h2>{{ $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h2>
                    <p>
                        {{ $isEdit ? 'Perbarui data pengguna yang sudah terdaftar.' : 'Tambahkan pengguna baru jika diperlukan oleh admin.' }}
                    </p>
                </div>

                <a
                    href="{{ route('admin.pengguna.index', request()->except(['mode', 'edit'])) }}"
                    class="btn-close"
                >
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>

            <form action="{{ $formAction }}" method="POST">
                @csrf

                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Nama Pengguna</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $isEdit ? $editData->name : '') }}"
                            required
                        >
                        @error('name')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $isEdit ? $editData->email : '') }}"
                            required
                        >
                        @error('email')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($hasRoleColumn)
                        <div class="form-group">
                            <label for="role">Role</label>

                            @php
                                $selectedRole = old('role', $isEdit ? $editData->role : 'user');
                            @endphp

                            <select id="role" name="role" required>
                                <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="operator" {{ $selectedRole === 'operator' ? 'selected' : '' }}>Operator</option>
                                <option value="user" {{ $selectedRole === 'user' ? 'selected' : '' }}>User</option>
                            </select>

                            @error('role')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    @if ($hasStatusColumn)
                        <div class="form-group">
                            <label for="status">Status</label>

                            @php
                                $selectedStatus = old('status', $isEdit ? $editData->status : 'Aktif');
                            @endphp

                            <select id="status" name="status" required>
                                <option value="Aktif" {{ $selectedStatus === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Suspend" {{ $selectedStatus === 'Suspend' ? 'selected' : '' }}>Suspend</option>
                            </select>

                            @error('status')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="password">
                            {{ $isEdit ? 'Password Baru' : 'Password' }}
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="{{ $isEdit ? 'Kosongkan jika tidak diganti' : 'Minimal 8 karakter' }}"
                            {{ $isEdit ? '' : 'required' }}
                        >
                        @error('password')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            {{ $isEdit ? '' : 'required' }}
                        >
                    </div>
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route('admin.pengguna.index', request()->except(['mode', 'edit'])) }}"
                        class="btn-secondary"
                    >
                        Batal
                    </a>

                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection