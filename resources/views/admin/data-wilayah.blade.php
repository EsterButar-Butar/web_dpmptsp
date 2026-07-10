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

    .page-dots {
        min-width: 40px;
        height: 40px;
        padding: 0 12px;
        border-radius: 12px;
        color: var(--text-muted);
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
            name="kode_kabupaten"
            onchange="this.form.submit()"
        >
            <option value="">Pilih Kab/Kota</option>

            @foreach ($kabupatenOptions as $option)
                <option
                    value="{{ $option->kode_kabupaten }}"
                    {{ request('kode_kabupaten') === $option->kode_kabupaten ? 'selected' : '' }}
                >
                    {{ $option->nama_kabupaten }}
                </option>
            @endforeach
        </select>
        <i class="fa-solid fa-chevron-down"></i>
    </div>

    <div class="filter-control">
        <select
            name="kode_kecamatan"
            onchange="this.form.submit()"
        >
            <option value="">Pilih Kecamatan</option>

            @foreach ($kecamatanOptions as $option)
                <option
                    value="{{ $option->kode_kecamatan }}"
                    {{ request('kode_kecamatan') === $option->kode_kecamatan ? 'selected' : '' }}
                >
                    {{ $option->nama_kecamatan }}
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
                                <div class="region-name">{{ $item->nama_provinsi }}</div>
                                <div class="region-code">{{ $item->code_provinsi }}</div>
                            </td>

                            <td>
                                <div class="region-name">{{ $item->nama_kabupaten }}</div>
                                <div class="region-code">{{ $item->kode_kabupaten }}</div>
                            </td>

                            <td>
                                <div class="region-name">{{ $item->nama_kecamatan }}</div>
                                <div class="region-code">{{ $item->kode_kecamatan }}</div>
                            </td>

                            <td>
                                <div class="region-name">{{ $item->nama_desa }}</div>
                                <div class="region-code">{{ $item->kode_desa }}</div>
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

            @php
                $currentPage = $dataWilayah->currentPage();
                $lastPage = $dataWilayah->lastPage();

                $pages = collect([
                    1,
                    2,
                    $currentPage - 1,
                    $currentPage,
                    $currentPage + 1,
                    $lastPage - 1,
                    $lastPage,
                ])
                    ->filter(fn ($page) => $page >= 1 && $page <= $lastPage)
                    ->unique()
                    ->sort()
                    ->values();

                $previousPageNumber = null;
            @endphp

            @foreach ($pages as $page)
                @if ($previousPageNumber && $page - $previousPageNumber > 1)
                    <span class="page-dots">...</span>
                @endif

                <a
                    href="{{ $dataWilayah->url($page) }}"
                    class="page-link {{ $page === $currentPage ? 'active' : '' }}"
                >
                    {{ $page }}
                </a>

                @php
                    $previousPageNumber = $page;
                @endphp
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
                    name="nama_provinsi"
                    id="nama_provinsi"
                    value="{{ old('nama_provinsi', $isEdit ? $editData->nama_provinsi : 'Sumatera Utara') }}"
                >

                <input
                    type="hidden"
                    name="nama_kabupaten"
                    id="nama_kabupaten"
                    value="{{ old('nama_kabupaten', $isEdit ? $editData->nama_kabupaten : '') }}"
                >

                <input
                    type="hidden"
                    name="nama_kecamatan"
                    id="nama_kecamatan"
                    value="{{ old('nama_kecamatan', $isEdit ? $editData->nama_kecamatan : '') }}"
                >

                <input
                    type="hidden"
                    name="nama_desa"
                    id="nama_desa"
                    value="{{ old('nama_desa', $isEdit ? $editData->nama_desa : '') }}"
                >

                <div class="form-grid">
    <input
        type="hidden"
        name="nama_provinsi"
        id="nama_provinsi"
        value="{{ old('nama_provinsi', $isEdit ? $editData->nama_provinsi : 'SUMATERA UTARA') }}"
    >

    <input
        type="hidden"
        name="nama_kabupaten"
        id="nama_kabupaten"
        value="{{ old('nama_kabupaten', $isEdit ? $editData->nama_kabupaten : '') }}"
    >

    <input
        type="hidden"
        name="nama_kecamatan"
        id="nama_kecamatan"
        value="{{ old('nama_kecamatan', $isEdit ? $editData->nama_kecamatan : '') }}"
    >

    <div class="form-group">
        <label for="kode_provinsi">Provinsi</label>

        <input
            type="text"
            value="SUMATERA UTARA"
            readonly
        >

        <input
            type="hidden"
            id="kode_provinsi"
            name="kode_provinsi"
            value="{{ old('kode_provinsi', $isEdit ? $editData->kode_provinsi : '12') }}"
        >

        <div class="form-helper">
            Kode Provinsi: <strong>12</strong>
        </div>

        @error('kode_provinsi')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="kode_kabupaten">Kabupaten/Kota</label>

        <select
            id="kode_kabupaten"
            name="kode_kabupaten"
            required
        >
            <option value="">Pilih Kabupaten/Kota</option>
        </select>

        <div class="form-helper">
            Kode Kabupaten/Kota:
            <strong id="preview_kode_kabupaten">
                {{ old('kode_kabupaten', $isEdit ? $editData->kode_kabupaten : '-') }}
            </strong>
        </div>

        @error('kode_kabupaten')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="kode_kecamatan">Kecamatan</label>

        <select
            id="kode_kecamatan"
            name="kode_kecamatan"
            required
            disabled
        >
            <option value="">Pilih Kabupaten/Kota dulu</option>
        </select>

        <div class="form-helper">
            Kode Kecamatan:
            <strong id="preview_kode_kecamatan">
                {{ old('kode_kecamatan', $isEdit ? $editData->kode_kecamatan : '-') }}
            </strong>
        </div>

        @error('kode_kecamatan')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="nama_desa">Desa/Kelurahan</label>

        <input
            id="nama_desa"
            type="text"
            name="nama_desa"
            value="{{ old('nama_desa', $isEdit ? $editData->nama_desa : '') }}"
            placeholder="Contoh: Aek Loba"
            required
        >

        @error('nama_desa')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="kode_desa">Kode Desa/Kelurahan</label>

        <input
            id="kode_desa"
            type="text"
            name="kode_desa"
            value="{{ old('kode_desa', $isEdit ? $editData->kode_desa : '') }}"
            placeholder="Contoh: 12.09.18.2013"
            required
        >

        @error('kode_desa')
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
    const wilayahOptions = @json($wilayahOptions ?? []);

    const selectedWilayah = {
        kodeKabupaten: @json(old('kode_kabupaten', $isEdit ? $editData->kode_kabupaten : '')),
        kodeKecamatan: @json(old('kode_kecamatan', $isEdit ? $editData->kode_kecamatan : '')),
    };

    const kabupatenSelect = document.getElementById('kode_kabupaten');
    const kecamatanSelect = document.getElementById('kode_kecamatan');

    const namaKabupatenInput = document.getElementById('nama_kabupaten');
    const namaKecamatanInput = document.getElementById('nama_kecamatan');

    const previewKodeKabupaten = document.getElementById('preview_kode_kabupaten');
    const previewKodeKecamatan = document.getElementById('preview_kode_kecamatan');

    function uniqueBy(items, key) {
        const seen = new Set();

        return items.filter((item) => {
            if (! item[key] || seen.has(item[key])) {
                return false;
            }

            seen.add(item[key]);
            return true;
        });
    }

    function resetSelect(select, placeholder, disabled = false) {
        select.innerHTML = '';
        select.disabled = disabled;

        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;

        select.appendChild(option);
    }

    function fillKabupaten(selectedCode = '') {
        const kabupatenList = uniqueBy(wilayahOptions, 'kode_kabupaten')
            .sort((a, b) => a.nama_kabupaten.localeCompare(b.nama_kabupaten));

        resetSelect(kabupatenSelect, 'Pilih Kabupaten/Kota', false);

        kabupatenList.forEach((item) => {
            const option = document.createElement('option');

            option.value = item.kode_kabupaten;
            option.textContent = `${item.nama_kabupaten} — ${item.kode_kabupaten}`;
            option.dataset.nama = item.nama_kabupaten;

            if (selectedCode && selectedCode === item.kode_kabupaten) {
                option.selected = true;
            }

            kabupatenSelect.appendChild(option);
        });

        setKabupatenName();
    }

    function fillKecamatan(kodeKabupaten, selectedCode = '') {
        if (! kodeKabupaten) {
            resetSelect(kecamatanSelect, 'Pilih Kabupaten/Kota dulu', true);
            namaKecamatanInput.value = '';
            previewKodeKecamatan.textContent = '-';
            return;
        }

        const kecamatanList = uniqueBy(
            wilayahOptions.filter((item) => item.kode_kabupaten === kodeKabupaten),
            'kode_kecamatan'
        ).sort((a, b) => a.nama_kecamatan.localeCompare(b.nama_kecamatan));

        resetSelect(kecamatanSelect, 'Pilih Kecamatan', false);

        kecamatanList.forEach((item) => {
            const option = document.createElement('option');

            option.value = item.kode_kecamatan;
            option.textContent = `${item.nama_kecamatan} — ${item.kode_kecamatan}`;
            option.dataset.nama = item.nama_kecamatan;

            if (selectedCode && selectedCode === item.kode_kecamatan) {
                option.selected = true;
            }

            kecamatanSelect.appendChild(option);
        });

        setKecamatanName();
    }

    function setKabupatenName() {
        const selected = kabupatenSelect.options[kabupatenSelect.selectedIndex];

        if (! selected || ! selected.value) {
            namaKabupatenInput.value = '';
            previewKodeKabupaten.textContent = '-';
            return;
        }

        namaKabupatenInput.value = selected.dataset.nama;
        previewKodeKabupaten.textContent = selected.value;
    }

    function setKecamatanName() {
        const selected = kecamatanSelect.options[kecamatanSelect.selectedIndex];

        if (! selected || ! selected.value) {
            namaKecamatanInput.value = '';
            previewKodeKecamatan.textContent = '-';
            return;
        }

        namaKecamatanInput.value = selected.dataset.nama;
        previewKodeKecamatan.textContent = selected.value;
    }

    kabupatenSelect.addEventListener('change', function () {
        setKabupatenName();
        fillKecamatan(kabupatenSelect.value);
    });

    kecamatanSelect.addEventListener('change', function () {
        setKecamatanName();
    });

    document.addEventListener('DOMContentLoaded', function () {
        fillKabupaten(selectedWilayah.kodeKabupaten);

        if (selectedWilayah.kodeKabupaten) {
            fillKecamatan(
                selectedWilayah.kodeKabupaten,
                selectedWilayah.kodeKecamatan
            );
        }
    });
</script>
@endif
@endsection