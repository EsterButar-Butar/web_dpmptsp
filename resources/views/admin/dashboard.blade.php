@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<style>
    :root {
        --green-dark: #255d3e;
        --green-main: #2f6b48;
        --green-soft: #eaf7ef;
        --green-pale: #f8fcf9;
        --blue-soft: #eef5ff;
        --purple-soft: #f4efff;
        --orange-soft: #fff3e8;
        --text-dark: #243042;
        --text-muted: #667085;
        --border: #e1e7ef;
        --white: #ffffff;
        --navy: #14213d;
    }

    .dashboard-page {
        min-height: 100vh;
        background: #f8faf8;
        font-family: 'Poppins', sans-serif;
        color: var(--text-dark);
        padding: 0;
    }

    .dashboard-topbar {
        min-height: 82px;
        padding: 22px 30px;
        background: #ffffff;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .sidebar-toggle {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: #ffffff;
        color: var(--navy);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .welcome-text {
        font-size: 14px;
        font-weight: 400;
        color: var(--text-muted);
    }

    .admin-dropdown {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        height: 48px;
        padding: 0 16px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #ffffff;
        color: var(--text-dark);
        font-size: 14px;
        font-weight: 500;
    }

    .dashboard-content {
        padding: 30px;
    }

    .dashboard-heading {
        margin-bottom: 24px;
    }

    .dashboard-heading h1 {
        margin: 0;
        color: var(--navy);
        font-size: 30px;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .dashboard-heading p {
        margin: 8px 0 0;
        color: var(--text-muted);
        font-size: 14px;
        font-weight: 400;
        line-height: 1.7;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 22px;
        min-height: 210px;
        box-shadow: 0 14px 30px rgba(31, 41, 55, 0.045);
    }

    .summary-main {
        display: flex;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 26px;
    }

    .summary-icon {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        flex-shrink: 0;
    }

    .summary-card.green .summary-icon {
        background: var(--green-soft);
        color: var(--green-dark);
    }

    .summary-card.blue .summary-icon {
        background: var(--blue-soft);
        color: #2563eb;
    }

    .summary-card.purple .summary-icon {
        background: var(--purple-soft);
        color: #7c3aed;
    }

    .summary-card.orange .summary-icon {
        background: var(--orange-soft);
        color: #d45b1f;
    }

    .summary-label {
        margin-top: 8px;
        color: var(--text-dark);
        font-size: 15px;
        font-weight: 600;
        line-height: 1.4;
    }

    .summary-value {
        margin-top: 8px;
        color: var(--navy);
        font-size: 30px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.02em;
    }

    .summary-subtitle {
        margin-top: 8px;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 400;
    }

    .summary-footer {
        border-top: 1px solid #edf2f7;
        padding-top: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .summary-footer-label {
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 400;
    }

    .summary-footer-value {
        color: var(--navy);
        font-size: 14px;
        font-weight: 700;
    }

    .dashboard-lower-grid {
        display: grid;
        grid-template-columns: 0.95fr 1.45fr;
        gap: 22px;
    }

    .panel-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 14px 30px rgba(31, 41, 55, 0.045);
    }

    .panel-title {
        margin: 0 0 22px;
        color: var(--navy);
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .quick-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .quick-card {
        min-height: 132px;
        border-radius: 16px;
        text-decoration: none;
        padding: 22px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-align: center;
        transition: 0.2s ease;
    }

    .quick-card:hover {
        transform: translateY(-2px);
    }

    .quick-card.disabled {
        opacity: 0.55;
        pointer-events: none;
    }

    .quick-card.green {
        background: linear-gradient(135deg, #ecfbf4 0%, #f8fdfa 100%);
        color: var(--green-dark);
    }

    .quick-card.blue {
        background: linear-gradient(135deg, #eef5ff 0%, #f8fbff 100%);
        color: #2563eb;
    }

    .quick-card.purple {
        background: linear-gradient(135deg, #f4efff 0%, #fbf9ff 100%);
        color: #7c3aed;
    }

    .quick-card.orange {
        background: linear-gradient(135deg, #fff3e8 0%, #fffaf6 100%);
        color: #d45b1f;
    }

    .quick-icon {
        font-size: 26px;
        line-height: 1;
    }

    .quick-label {
        font-size: 14px;
        font-weight: 600;
        line-height: 1.5;
    }

    .activity-table {
        width: 100%;
        border-collapse: collapse;
    }

    .activity-table th {
        padding: 0 0 14px;
        text-align: left;
        color: var(--text-dark);
        font-size: 13px;
        font-weight: 600;
        border-bottom: 1px solid #edf2f7;
    }

    .activity-table td {
        padding: 16px 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 400;
        line-height: 1.6;
        border-bottom: 1px solid #edf2f7;
        vertical-align: top;
    }

    .activity-table td:first-child {
        width: 165px;
        color: var(--text-dark);
        font-weight: 500;
        white-space: nowrap;
    }

    .empty-activity {
        padding: 30px 0;
        color: var(--text-muted);
        font-size: 14px;
        text-align: center;
    }

    .activity-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        color: #2563eb;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .dashboard-footer {
        margin-top: 36px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
        text-align: center;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 400;
    }

    @media (max-width: 1280px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-lower-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .dashboard-topbar {
            flex-direction: column;
            align-items: flex-start;
        }

        .dashboard-content {
            padding: 20px;
        }

        .summary-grid,
        .quick-grid {
            grid-template-columns: 1fr;
        }

        .topbar-right {
            width: 100%;
            justify-content: space-between;
        }

        .activity-table td:first-child {
            width: auto;
            white-space: normal;
        }
    }
</style>

<div class="dashboard-page">
    <div class="dashboard-topbar">
        <button
            type="button"
            class="sidebar-toggle"
        >
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="topbar-right">
            <div class="welcome-text">
                Selamat datang, {{ auth()->user()->name ?? 'Admin' }}
            </div>

            <div class="admin-dropdown">
                <i class="fa-regular fa-user"></i>
                <span>{{ ucfirst(auth()->user()->role ?? 'Admin') }}</span>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="dashboard-heading">
            <h1>Dashboard Admin</h1>
            <p>
                Monitoring dan pengelolaan data utama sistem Sumatera Investment.
            </p>
        </div>

        <div class="summary-grid">
            @foreach ($stats as $stat)
                <div class="summary-card {{ $stat['color'] }}">
                    <div class="summary-main">
                        <div class="summary-icon">
                            <i class="fa-solid {{ $stat['icon'] }}"></i>
                        </div>

                        <div>
                            <div class="summary-label">
                                {{ $stat['label'] }}
                            </div>

                            <div class="summary-value">
                                {{ number_format($stat['value'], 0, ',', '.') }}
                            </div>

                            <div class="summary-subtitle">
                                Total Data
                            </div>
                        </div>
                    </div>

                    <div class="summary-footer">
                        <div class="summary-footer-label">
                            {{ $stat['caption_label'] }}
                        </div>

                        <div class="summary-footer-value">
                            {{ number_format($stat['caption_value'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="dashboard-lower-grid">
            <div class="panel-card">
                <h2 class="panel-title">Akses Cepat</h2>

                <div class="quick-grid">
                    @foreach ($quickActions as $action)
                        <a
                            href="{{ $action['url'] ?? '#' }}"
                            class="quick-card {{ $action['color'] }} {{ $action['url'] ? '' : 'disabled' }}"
                            title="{{ $action['url'] ? $action['label'] : 'Halaman belum tersedia' }}"
                        >
                            <div class="quick-icon">
                                <i class="fa-solid {{ $action['icon'] }}"></i>
                            </div>

                            <div class="quick-label">
                                {{ $action['label'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="panel-card">
                <h2 class="panel-title">Aktivitas Terbaru</h2>

                @if ($activities->count())
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Aktivitas</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($activities as $activity)
                                <tr>
                                    <td>{{ $activity['waktu'] }}</td>
                                    <td>{{ $activity['aktivitas'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-activity">
                        Belum ada aktivitas terbaru yang bisa ditampilkan.
                    </div>
                @endif

                <a
                    href="#"
                    class="activity-link"
                >
                    Lihat semua aktivitas
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="dashboard-footer">
            Copyright © DPMPTSP Provinsi Sumatera Utara {{ date('Y') }}
        </div>
    </div>
</div>
@endsection