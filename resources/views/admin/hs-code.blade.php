@extends('layouts.admin')

@section('title', 'Data HS Code')

@section('content')
@php
    $isModalOpen = in_array($mode, ['create', 'edit']);
    $isEdit = $mode === 'edit' && $editData;

    $formAction = $isEdit
        ? route('admin.hs-code.update', $editData->id)
        : route('admin.hs-code.store');
@endphp

<style>
    :root {
        --green-dark: #255d3e;
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

    .hs-page {
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

    .hs-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .hs-stat-card {
        min-height: 162px;
        padding: 22px;
        border-radius: 22px;
        border: 1px solid rgba(255, 255, 255, 0.75);
        box-shadow: 0 14px 30px rgba(31, 41, 55, 0.05);
        overflow: hidden;
    }

    .hs-stat-mint {
        background: linear-gradient(135deg, #ecfbf4 0%, #f8fdfa 100%);
    }

    .hs-stat-cream {
        background: linear-gradient(135deg, #fff8e6 0%, #fffdf5 100%);
    }

    .hs-stat-blue {
        background: linear-gradient(135deg, #eef7ff 0%, #f8fbff 100%);
    }

    .hs-stat-red {
        background: linear-gradient(135deg, #fff2f2 0%, #fff9f9 100%);
    }

    .hs-stat-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .hs-stat-label {
        color: #344054;
        font-size: 15px;
        line-height: 1.5;
        font-weight: 500;
        max-width: 150px;
    }

    .hs-stat-icon {
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

    .hs-stat-value {
        color: var(--text-dark);
        font-size: 34px;
        line-height: 1;
        font-weight: 700;
        margin-bottom: 14px;
    }

    .hs-stat-footer {
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
        grid-template-columns: 210px minmax(300px, 1fr) 130px;
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
        min-width: 1250px;
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

    .code-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 82px;
        padding: 8px 13px;
        border-radius: 12px;
        background: var(--green-soft);
        color: var(--green-dark);
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        letter-spacing: 0.02em;
        font-variant-numeric: tabular-nums;
    }

    .code-pill.hs {
        min-width: 110px;
        background: #eef7ff;
        color: #2563eb;
    }

    .sub-title {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
        line-height: 1.5;
        max-width: 280px;
    }

    .sub-code {
        margin-top: 5px;
        font-size: 13px;
        color: var(--text-muted);
    }

    .text-preview {
        color: var(--text-muted);
        font-size: 13px;
        line-height: 1.6;
        max-width: 360px;
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

    .page-link,
    .page-dots {
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
        border: none;
        background: transparent;
        color: var(--text-muted);
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
        width: min(920px, 100%);
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
        min-height: 120px;
        padding-top: 12px;
        resize: vertical;
        line-height: 1.6;
    }

    .error-text {
        margin-top: 7px;
        color: #dc2626;
        font-size: 13px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 22px;
    }

    @media (max-width: 1280px) {
        .hs-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .toolbar {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 768px) {
        .hs-page {
            padding: 18px;
        }

        .page-heading {
            flex-direction: column;
            align-items: stretch;
        }

        .hs-stats-grid,
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

<div class="hs-page">
    <div class="page-heading">
        <div>
            <div class="page-kicker">
                <i class="fa-solid fa-tags"></i>
                Profil Admin
            </div>

            <h1>Manajemen Data HS Code</h1>

            <p>
                Kelola kategori, kelompok, subkelompok, HS Code, dan uraian barang.
            </p>
        </div>

        <a
            href="{{ route('admin.hs-code.index', array_merge(request()->query(), ['mode' => 'create'])) }}"
            class="btn-primary"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah HS Code
        </a>
    </div>

    @if (! $tableExists)
        <div class="alert alert-error">
            Tabel <strong>hs_codes</strong> belum tersedia di Supabase.
        </div>
    @elseif (! $columnsReady)
        <div class="alert alert-error">
            Kolom tabel <strong>hs_codes</strong> belum sesuai. Minimal harus ada kolom <strong>hs_code</strong> dan <strong>uraian_barang</strong>.
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

    <div class="hs-stats-grid">
        @foreach ($stats as $stat)
            <div class="hs-stat-card hs-stat-{{ $stat['color'] }}">
                <div class="hs-stat-header">
                    <div class="hs-stat-label">
                        {{ $stat['label'] }}
                    </div>

                    <div class="hs-stat-icon">
                        <i class="fa-solid {{ $stat['icon'] }}"></i>
                    </div>
                </div>

                <div class="hs-stat-value">
                    {{ number_format($stat['value'], 0, ',', '.') }}
                </div>

                <div class="hs-stat-footer">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>Total Data</span>
                </div>
            </div>
        @endforeach
    </div>

    <form
        action="{{ route('admin.hs-code.index') }}"
        method="GET"
        class="toolbar"
    >
        @if ($hasStatusColumn)
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
        @endif

        <div class="search-control">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari HS Code, kategori, kelompok, uraian..."
            >

            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <a
            href="{{ route('admin.hs-code.index') }}"
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
                        <th>KATEGORI</th>
                        <th>KELOMPOK</th>
                        <th>SUBKELOMPOK</th>
                        <th>HS CODE</th>
                        <th>URAIAN BARANG</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($dataHsCode as $item)
                        @php
                            $nomor = ($dataHsCode->currentPage() - 1) * $dataHsCode->perPage() + $loop->iteration;
                            $status = $item->status ?? 'Aktif';
                            $statusLower = strtolower(trim($status));
                        @endphp

                        <tr>
                            <td>{{ str_pad($nomor, 5, '0', STR_PAD_LEFT) }}</td>

                            <td>
                                <span class="code-pill">
                                    {{ $item->kode_kategori ?: '-' }}
                                </span>
                            </td>

                            <td>
                                <div class="sub-title">
                                    {{ \Illuminate\Support\Str::limit($item->uraian_kelompok ?: '-', 70) }}
                                </div>
                                <div class="sub-code">
                                    {{ $item->kode_kelompok ?: '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="sub-title">
                                    {{ \Illuminate\Support\Str::limit($item->uraian_subkelompok ?: '-', 70) }}
                                </div>
                                <div class="sub-code">
                                    {{ $item->kode_subkelompok ?: '-' }}
                                </div>
                            </td>

                            <td>
                                <span class="code-pill hs">
                                    {{ $item->hs_code ?: '-' }}
                                </span>
                            </td>

                            <td>
                                <div
                                    class="text-preview"
                                    title="{{ $item->uraian_barang }}"
                                >
                                    {{ \Illuminate\Support\Str::limit($item->uraian_barang ?: '-', 110) }}
                                </div>
                            </td>

                            <td>
                                <span class="badge {{ $statusLower === 'nonaktif' ? 'badge-inactive' : 'badge-active' }}">
                                    {{ $status }}
                                </span>
                            </td>

                            <td>
                                @if ($hasIdColumn && $item->id)
                                    <div class="action-group">
                                        <a
                                            href="{{ route('admin.hs-code.index', array_merge(request()->query(), ['edit' => $item->id])) }}"
                                            class="action-btn"
                                            title="Edit"
                                        >
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>

                                        <form
                                            action="{{ route('admin.hs-code.destroy', $item->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data HS Code ini?')"
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
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                Belum ada data HS Code.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($dataHsCode->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-info">
                Menampilkan {{ $dataHsCode->firstItem() }} - {{ $dataHsCode->lastItem() }}
                dari {{ $dataHsCode->total() }} data HS Code
            </div>

            <div class="pagination">
                <a
                    href="{{ $dataHsCode->previousPageUrl() ?? '#' }}"
                    class="page-link {{ $dataHsCode->onFirstPage() ? 'disabled' : '' }}"
                >
                    <i class="fa-solid fa-chevron-left"></i>&nbsp; PREV
                </a>

                @php
                    $currentPage = $dataHsCode->currentPage();
                    $lastPage = $dataHsCode->lastPage();

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
                        href="{{ $dataHsCode->url($page) }}"
                        class="page-link {{ $page === $currentPage ? 'active' : '' }}"
                    >
                        {{ $page }}
                    </a>

                    @php
                        $previousPageNumber = $page;
                    @endphp
                @endforeach

                <a
                    href="{{ $dataHsCode->nextPageUrl() ?? '#' }}"
                    class="page-link {{ $dataHsCode->hasMorePages() ? '' : 'disabled' }}"
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
                    <h2>{{ $isEdit ? 'Edit Data HS Code' : 'Tambah Data HS Code' }}</h2>
                    <p>
                        Isi data kategori, kelompok, subkelompok, HS Code, dan uraian barang.
                    </p>
                </div>

                <a
                    href="{{ route('admin.hs-code.index', request()->except(['mode', 'edit'])) }}"
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
                        <label for="excel_id">ID Excel</label>
                        <input
                            id="excel_id"
                            type="number"
                            name="excel_id"
                            value="{{ old('excel_id', $isEdit ? $editData->excel_id : '') }}"
                            placeholder="Opsional"
                        >
                        @error('excel_id')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kode_kategori">Kode Kategori</label>
                        <input
                            id="kode_kategori"
                            type="text"
                            name="kode_kategori"
                            value="{{ old('kode_kategori', $isEdit ? $editData->kode_kategori : '') }}"
                            placeholder="Contoh: 01"
                        >
                        @error('kode_kategori')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kode_kelompok">Kode Kelompok</label>
                        <input
                            id="kode_kelompok"
                            type="text"
                            name="kode_kelompok"
                            value="{{ old('kode_kelompok', $isEdit ? $editData->kode_kelompok : '') }}"
                            placeholder="Contoh: 01.01"
                        >
                        @error('kode_kelompok')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kode_subkelompok">Kode Subkelompok</label>
                        <input
                            id="kode_subkelompok"
                            type="text"
                            name="kode_subkelompok"
                            value="{{ old('kode_subkelompok', $isEdit ? $editData->kode_subkelompok : '') }}"
                            placeholder="Contoh: 0101.30"
                        >
                        @error('kode_subkelompok')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label for="uraian_kelompok">Uraian Kelompok</label>
                        <input
                            id="uraian_kelompok"
                            type="text"
                            name="uraian_kelompok"
                            value="{{ old('uraian_kelompok', $isEdit ? $editData->uraian_kelompok : '') }}"
                            placeholder="Contoh: Kuda, keledai, bagal, hinnie, hidup"
                        >
                        @error('uraian_kelompok')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full">
                        <label for="uraian_subkelompok">Uraian Subkelompok</label>
                        <input
                            id="uraian_subkelompok"
                            type="text"
                            name="uraian_subkelompok"
                            value="{{ old('uraian_subkelompok', $isEdit ? $editData->uraian_subkelompok : '') }}"
                            placeholder="Contoh: Keledai"
                        >
                        @error('uraian_subkelompok')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="hs_code">HS Code</label>
                        <input
                            id="hs_code"
                            type="text"
                            name="hs_code"
                            value="{{ old('hs_code', $isEdit ? $editData->hs_code : '') }}"
                            placeholder="Contoh: 0101.21.00"
                            required
                        >
                        @error('hs_code')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($hasStatusColumn)
                        <div class="form-group">
                            <label for="status">Status</label>

                            @php
                                $selectedStatus = old('status', $isEdit ? ($editData->status ?? 'Aktif') : 'Aktif');
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
                    @endif

                    <div class="form-group full">
                        <label for="uraian_barang">Uraian Barang</label>
                        <textarea
                            id="uraian_barang"
                            name="uraian_barang"
                            placeholder="Contoh: Bibit"
                            required
                        >{{ old('uraian_barang', $isEdit ? $editData->uraian_barang : '') }}</textarea>

                        @error('uraian_barang')
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
                    </div>
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route('admin.hs-code.index', request()->except(['mode', 'edit'])) }}"
                        class="btn-secondary"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah HS Code' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection