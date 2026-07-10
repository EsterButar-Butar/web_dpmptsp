@extends('layouts.admin')

@section('title', 'Data Wilayah')

@section('content')
@php
    $isModalOpen = in_array($mode, ['create', 'edit']);
    $isEdit = $mode === 'edit' && $editData;

    $formAction = $isEdit
        ? route('admin.data-wilayah.update', $editData->id)
        : route('admin.data-wilayah.store');
@endphp

<style>
    :root {
        --green-dark: #255d3e;
        --green-main: #2f6b48;
        --green-soft: #eaf7ef;
        --yellow-soft: #fff8e6;
        --blue-soft: #eef7ff;
        --red-soft: #fff3f3;
        --navy: #14213d;
        --text-dark: #243042;
        --text-muted: #667085;
        --border: #e1e7ef;
        --danger: #ef4444;
    }

    .wilayah-page {
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

    .wilayah-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .wilayah-stat-card {
        min-height: 162px;
        padding: 22px;
        border-radius: 22px;
        border: 1px solid rgba(255, 255, 255, 0.75);
        box-shadow: 0 14px 30px rgba(31, 41, 55, 0.05);
        overflow: hidden;
    }

    .wilayah-stat-mint {
        background: linear-gradient(135deg, #ecfbf4 0%, #f8fdfa 100%);
    }

    .wilayah-stat-cream {
        background: linear-gradient(135deg, #fff8e6 0%, #fffdf5 100%);
    }

    .wilayah-stat-blue {
        background: linear-gradient(135deg, #eef7ff 0%, #f8fbff 100%);
    }

    .wilayah-stat-red {
        background: linear-gradient(135deg, #fff2f2 0%, #fff9f9 100%);
    }

    .wilayah-stat-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .wilayah-stat-label {
        color: #344054;
        font-size: 15px;
        line-height: 1.5;
        font-weight: 500;
        max-width: 150px;
    }

    .wilayah-stat-icon {
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

    .wilayah-stat-value {
        color: var(--text-dark);
        font-size: 34px;
        line-height: 1;
        font-weight: 700;
        margin-bottom: 14px;
    }

    .wilayah-stat-footer {
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
        grid-template-columns: 210px 210px 170px minmax(260px, 1fr) 130px;
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
        min-width: 1180px;
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
        padding: 22px 24px;
        border-bottom: 1px solid #edf2f7;
        color: var(--text-dark);
        font-size: 14px;
        font-weight: 400;
        vertical-align: top;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .region-name {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .region-code {
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

    .badge-active {
        background: #d7f7c8;
        color: #2b8f3d;
    }

    .badge-inactive {
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
        width: min(860px, 100%);
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

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--navy);
        font-size: 14px;
        font-weight: 500;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        border: 1px solid #d9dee8;
        border-radius: 14px;
        padding: 0 14px;
        outline: none;
        background: #ffffff;
        color: var(--text-dark);
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
    }

    .form-group input,
    .form-group select {
        height: 48px;
    }

    .form-group textarea {
        min-height: 90px;
        padding-top: 12px;
        resize: vertical;
    }

    .form-helper {
        margin-top: 7px;
        color: var(--text-muted);
        font-size: 12px;
        line-height: 1.5;
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

    @media (max-width: 1280px) {
        .wilayah-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .toolbar {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .wilayah-page {
            padding: 18px;
        }

        .page-heading {
            flex-direction: column;
            align-items: stretch;
        }

        .wilayah-stats-grid,
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

<div class="wilayah-page">
    <div class="page-heading">
        <div>
            <div class="page-kicker">
                <i class="fa-solid fa-map-location-dot"></i>
                Profil Admin
            </div>

            <h1>Manajemen Data Wilayah</h1>

            <p>
                Kelola data provinsi, kabupaten/kota, kecamatan, dan desa/kelurahan untuk wilayah Sumatera Utara.
            </p>
        </div>

        <a
            href="{{ route('admin.data-wilayah.index', array_merge(request()->query(), ['mode' => 'create'])) }}"
            class="btn-primary"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Wilayah
        </a>
    </div>

    @if (! $tableExists)
        <div class="alert alert-error">
            Tabel <strong>data_wilayah</strong> belum tersedia di Supabase. Besok setelah tabel dibuat dan CSV di-import, halaman ini akan otomatis menampilkan data.
        </div>
    @endif

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

    <div class="wilayah-stats-grid">
        @foreach ($stats as $stat)
            <div class="wilayah-stat-card wilayah-stat-{{ $stat['color'] }}">
                <div class="wilayah-stat-header">
                    <div class="wilayah-stat-label">
                        {{ $stat['label'] }}
                    </div>

                    <div class="wilayah-stat-icon">
                        <i class="fa-solid {{ $stat['icon'] }}"></i>
                    </div>
                </div>

                <div class="wilayah-stat-value">
                    {{ $stat['value'] }}
                </div>

                <div class="wilayah-stat-footer">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>Total Data</span>
                </div>
            </div>
        @endforeach
    </div>

    <form
        action="{{ route('admin.data-wilayah.index') }}"
        method="GET"
        class="toolbar"
    >
        <div class="filter-control">
            <select
                name="regency_code"
                onchange="this.form.submit()"
            >
                <option value="">Pilih Kab/Kota</option>
                @foreach ($regencyOptions as $option)
                    <option
                        value="{{ $option->regency_code }}"
                        {{ request('regency_code') === $option->regency_code ? 'selected' : '' }}
                    >
                        {{ $option->regency_name }}
                    </option>
                @endforeach
            </select>
            <i class="fa-solid fa-chevron-down"></i>
        </div>

        <div class="filter-control">
            <select
                name="district_code"
                onchange="this.form.submit()"
            >
                <option value="">Pilih Kecamatan</option>
                @foreach ($districtOptions as $option)
                    <option
                        value="{{ $option->district_code }}"
                        {{ request('district_code') === $option->district_code ? 'selected' : '' }}
                    >
                        {{ $option->district_name }}
                    </option>
                @endforeach
            </select>
            <i class="fa-solid fa-chevron-down"></i>
        </div>

        <div class="filter-control">
            <select
                name="status"
                onchange="this.form.submit()"
            >
                <option value="">Pilih Status</option>
                <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>
                    Aktif
                </option>
                <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>
                    Nonaktif
                </option>
            </select>
            <i class="fa-solid fa-chevron-down"></i>
        </div>

        <div class="search-control">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari provinsi, kab/kota, kecamatan, desa..."
            >

            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <a
            href="{{ route('admin.data-wilayah.index') }}"
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
                        <th>PROVINSI</th>
                        <th>KAB/KOTA</th>
                        <th>KECAMATAN</th>
                        <th>DESA/KELURAHAN</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($dataWilayah as $item)
                        @php
                            $nomor = ($dataWilayah->currentPage() - 1) * $dataWilayah->perPage() + $loop->iteration;
                            $status = $item->status ?? 'Aktif';
                            $statusLower = strtolower(trim($status));
                        @endphp

                        <tr>
                            <td>
                                {{ str_pad($nomor, 5, '0', STR_PAD_LEFT) }}
                            </td>

                            <td>
                                <div class="region-name">{{ $item->province_name }}</div>
                                <div class="region-code">{{ $item->province_code }}</div>
                            </td>

                            <td>
                                <div class="region-name">{{ $item->regency_name }}</div>
                                <div class="region-code">{{ $item->regency_code }}</div>
                            </td>

                            <td>
                                <div class="region-name">{{ $item->district_name }}</div>
                                <div class="region-code">{{ $item->district_code }}</div>
                            </td>

                            <td>
                                <div class="region-name">{{ $item->village_name }}</div>
                                <div class="region-code">{{ $item->village_code }}</div>
                            </td>

                            <td>
                                <span class="badge {{ $statusLower === 'nonaktif' ? 'badge-inactive' : 'badge-active' }}">
                                    {{ $status }}
                                </span>
                            </td>

                            <td>
                                <div class="action-group">
                                    <a
                                        href="{{ route('admin.data-wilayah.index', array_merge(request()->query(), ['edit' => $item->id])) }}"
                                        class="action-btn"
                                        title="Edit"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>

                                    <form
                                        action="{{ route('admin.data-wilayah.destroy', $item->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data wilayah ini?')"
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
                            <td colspan="7" class="empty-state">
                                Belum ada data wilayah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($dataWilayah->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-info">
                Menampilkan {{ $dataWilayah->firstItem() }} - {{ $dataWilayah->lastItem() }}
                dari {{ $dataWilayah->total() }} data wilayah
            </div>

            <div class="pagination">
                <a
                    href="{{ $dataWilayah->previousPageUrl() ?? '#' }}"
                    class="page-link {{ $dataWilayah->onFirstPage() ? 'disabled' : '' }}"
                >
                    <i class="fa-solid fa-chevron-left"></i>&nbsp; PREV
                </a>

                @foreach ($dataWilayah->getUrlRange(1, $dataWilayah->lastPage()) as $page => $url)
                    <a
                        href="{{ $url }}"
                        class="page-link {{ $page === $dataWilayah->currentPage() ? 'active' : '' }}"
                    >
                        {{ $page }}
                    </a>
                @endforeach

                <a
                    href="{{ $dataWilayah->nextPageUrl() ?? '#' }}"
                    class="page-link {{ $dataWilayah->hasMorePages() ? '' : 'disabled' }}"
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
                    <h2>{{ $isEdit ? 'Edit Data Wilayah' : 'Tambah Data Wilayah' }}</h2>
                    <p>
                        Pilih wilayah secara berurutan dari Provinsi, Kabupaten/Kota, Kecamatan, lalu Desa/Kelurahan.
                    </p>
                </div>

                <a
                    href="{{ route('admin.data-wilayah.index', request()->except(['mode', 'edit'])) }}"
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

                <input
                    type="hidden"
                    name="province_name"
                    id="province_name"
                    value="{{ old('province_name', $isEdit ? $editData->province_name : 'Sumatera Utara') }}"
                >

                <input
                    type="hidden"
                    name="regency_name"
                    id="regency_name"
                    value="{{ old('regency_name', $isEdit ? $editData->regency_name : '') }}"
                >

                <input
                    type="hidden"
                    name="district_name"
                    id="district_name"
                    value="{{ old('district_name', $isEdit ? $editData->district_name : '') }}"
                >

                <input
                    type="hidden"
                    name="village_name"
                    id="village_name"
                    value="{{ old('village_name', $isEdit ? $editData->village_name : '') }}"
                >

                <div class="form-grid">
                    <div class="form-group">
                        <label for="province_code">Provinsi</label>
                        <select
                            id="province_code"
                            name="province_code"
                            required
                        >
                            <option value="12" selected>Sumatera Utara</option>
                        </select>

                        @error('province_code')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="regency_code">Kabupaten/Kota</label>
                        <select
                            id="regency_code"
                            name="regency_code"
                            required
                        >
                            <option value="">Memuat Kabupaten/Kota...</option>
                        </select>

                        @error('regency_code')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="district_code">Kecamatan</label>
                        <select
                            id="district_code"
                            name="district_code"
                            required
                            disabled
                        >
                            <option value="">Pilih Kabupaten/Kota dulu</option>
                        </select>

                        @error('district_code')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="village_code">Desa/Kelurahan</label>
                        <select
                            id="village_code"
                            name="village_code"
                            required
                            disabled
                        >
                            <option value="">Pilih Kecamatan dulu</option>
                        </select>

                        @error('village_code')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        @php
                            $selectedStatus = old('status', $isEdit ? $editData->status : 'Aktif');
                        @endphp

                        <select
                            id="status"
                            name="status"
                            required
                        >
                            <option value="Aktif" {{ $selectedStatus === 'Aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="Nonaktif" {{ $selectedStatus === 'Nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>
                        </select>

                        @error('status')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label for="keterangan">Keterangan</label>
                        <textarea
                            id="keterangan"
                            name="keterangan"
                            placeholder="Opsional"
                        >{{ old('keterangan', $isEdit ? $editData->keterangan : '') }}</textarea>

                        @error('keterangan')
                            <div class="error-text">{{ $message }}</div>
                        @enderror

                        <div class="form-helper">
                            Keterangan boleh dikosongkan.
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route('admin.data-wilayah.index', request()->except(['mode', 'edit'])) }}"
                        class="btn-secondary"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Wilayah' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const selectedWilayah = {
        provinceCode: @json(old('province_code', $isEdit ? $editData->province_code : '12')),
        regencyCode: @json(old('regency_code', $isEdit ? $editData->regency_code : '')),
        districtCode: @json(old('district_code', $isEdit ? $editData->district_code : '')),
        villageCode: @json(old('village_code', $isEdit ? $editData->village_code : '')),
    };

    const apiBase = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    const provinceSelect = document.getElementById('province_code');
    const regencySelect = document.getElementById('regency_code');
    const districtSelect = document.getElementById('district_code');
    const villageSelect = document.getElementById('village_code');

    const provinceNameInput = document.getElementById('province_name');
    const regencyNameInput = document.getElementById('regency_name');
    const districtNameInput = document.getElementById('district_name');
    const villageNameInput = document.getElementById('village_name');

    function resetSelect(select, placeholder, disabled = false) {
        select.innerHTML = '';
        select.disabled = disabled;

        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;

        select.appendChild(option);
    }

    async function fetchWilayah(url) {
        const response = await fetch(url);

        if (! response.ok) {
            throw new Error('Gagal mengambil data wilayah.');
        }

        const json = await response.json();

        if (Array.isArray(json)) {
            return json;
        }

        if (Array.isArray(json.data)) {
            return json.data;
        }

        return [];
    }

    function getCode(item) {
        return item.id || item.code || '';
    }

    function getName(item) {
        return item.name || item.nama || '';
    }

    function fillSelect(select, items, placeholder, selectedCode = '') {
        resetSelect(select, placeholder, false);

        items.forEach((item) => {
            const code = getCode(item);
            const name = getName(item);

            const option = document.createElement('option');
            option.value = code;
            option.textContent = name;
            option.dataset.name = name;

            if (selectedCode && selectedCode === code) {
                option.selected = true;
            }

            select.appendChild(option);
        });
    }

    function setNameFromSelect(select, input) {
        const selected = select.options[select.selectedIndex];

        if (! selected || ! selected.value) {
            input.value = '';
            return;
        }

        input.value = selected.dataset.name || selected.textContent;
    }

    async function loadRegencies(selectedCode = '') {
        resetSelect(regencySelect, 'Memuat Kabupaten/Kota...', true);
        resetSelect(districtSelect, 'Pilih Kabupaten/Kota dulu', true);
        resetSelect(villageSelect, 'Pilih Kecamatan dulu', true);

        regencyNameInput.value = '';
        districtNameInput.value = '';
        villageNameInput.value = '';

        try {
            const items = await fetchWilayah(`${apiBase}/regencies/12.json`);

            fillSelect(
                regencySelect,
                items,
                'Pilih Kabupaten/Kota',
                selectedCode
            );

            setNameFromSelect(regencySelect, regencyNameInput);
        } catch (error) {
            resetSelect(regencySelect, 'Gagal memuat Kabupaten/Kota', true);
            console.error(error);
        }
    }

    async function loadDistricts(regencyCode, selectedCode = '') {
        resetSelect(districtSelect, 'Memuat Kecamatan...', true);
        resetSelect(villageSelect, 'Pilih Kecamatan dulu', true);

        districtNameInput.value = '';
        villageNameInput.value = '';

        if (! regencyCode) {
            resetSelect(districtSelect, 'Pilih Kabupaten/Kota dulu', true);
            return;
        }

        try {
            const items = await fetchWilayah(`${apiBase}/districts/${regencyCode}.json`);

            fillSelect(
                districtSelect,
                items,
                'Pilih Kecamatan',
                selectedCode
            );

            setNameFromSelect(districtSelect, districtNameInput);
        } catch (error) {
            resetSelect(districtSelect, 'Gagal memuat Kecamatan', true);
            console.error(error);
        }
    }

    async function loadVillages(districtCode, selectedCode = '') {
        resetSelect(villageSelect, 'Memuat Desa/Kelurahan...', true);

        villageNameInput.value = '';

        if (! districtCode) {
            resetSelect(villageSelect, 'Pilih Kecamatan dulu', true);
            return;
        }

        try {
            const items = await fetchWilayah(`${apiBase}/villages/${districtCode}.json`);

            fillSelect(
                villageSelect,
                items,
                'Pilih Desa/Kelurahan',
                selectedCode
            );

            setNameFromSelect(villageSelect, villageNameInput);
        } catch (error) {
            resetSelect(villageSelect, 'Gagal memuat Desa/Kelurahan', true);
            console.error(error);
        }
    }

    provinceSelect.addEventListener('change', async function () {
        provinceNameInput.value = 'Sumatera Utara';

        await loadRegencies();
    });

    regencySelect.addEventListener('change', async function () {
        setNameFromSelect(regencySelect, regencyNameInput);

        await loadDistricts(regencySelect.value);
    });

    districtSelect.addEventListener('change', async function () {
        setNameFromSelect(districtSelect, districtNameInput);

        await loadVillages(districtSelect.value);
    });

    villageSelect.addEventListener('change', function () {
        setNameFromSelect(villageSelect, villageNameInput);
    });

    document.addEventListener('DOMContentLoaded', async function () {
        provinceNameInput.value = 'Sumatera Utara';
        provinceSelect.value = '12';

        await loadRegencies(selectedWilayah.regencyCode);

        if (selectedWilayah.regencyCode) {
            await loadDistricts(
                selectedWilayah.regencyCode,
                selectedWilayah.districtCode
            );
        }

        if (selectedWilayah.districtCode) {
            await loadVillages(
                selectedWilayah.districtCode,
                selectedWilayah.villageCode
            );
        }
    });
</script>
@endif
@endsection