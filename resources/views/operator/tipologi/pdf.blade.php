<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Tipologi Sektor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px double #333;
            padding-bottom: 5px;
        }
        .header h1 {
            font-size: 13px;
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header h2 {
            font-size: 11px;
            margin: 3px 0 0 0;
            font-weight: normal;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 8px;
            color: #666;
        }
        .title-block {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-block h3 {
            font-size: 13px;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            color: #145239;
        }
        .title-block p {
            margin: 0;
            font-size: 10px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #145239;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 3px 5px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-prima {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        .badge-potensial {
            background-color: #cff4fc;
            color: #055160;
            border: 1px solid #b6effb;
        }
        .badge-berkembang {
            background-color: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
        }
        .badge-terbelakang {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
        .signature-container {
            width: 100%;
            margin-top: 20px;
        }
        .signature-table {
            width: 100%;
            border: none;
        }
        .signature-table td {
            border: none;
            width: 50%;
            vertical-align: top;
        }
        .signature-block {
            text-align: center;
            float: right;
            width: 250px;
        }
        .signature-block p {
            margin: 0;
        }
        .signature-space {
            height: 40px;
        }
    </style>
</head>
<body>

    <div class="title-block">
        <h3>Laporan Hasil Analisis Tipologi Sektor</h3>
        <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
        @if($search)
            <p>Pencarian/Filter: "{{ $search }}"</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">No</th>
                <th style="width: 11%;">Daerah Analisis</th>
                <th style="width: 11%;">Daerah Pembanding</th>
                <th style="width: 25%;">Sektor</th>
                <th class="text-center" style="width: 8%;">Tahun</th>
                <th class="text-right" style="width: 12%;">Nilai SS (Dij)</th>
                <th class="text-right" style="width: 12%;">Nilai LQ</th>
                <th style="width: 17%;">Klasifikasi Tipologi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tipologiData as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['daerah_analisis'] }}</td>
                    <td>{{ $item['daerah_pembanding'] }}</td>
                    <td>{{ $item['sektor'] }}</td>
                    <td class="text-center">{{ $item['tahun'] }}</td>
                    <td class="text-right">{{ number_format($item['nilai_ss'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item['nilai_lq'], 4) }}</td>
                    <td>
                        @php
                            $badgeClass = 'badge-terbelakang';
                            if ($item['tipologi'] === 'Sektor Prima') $badgeClass = 'badge-prima';
                            elseif ($item['tipologi'] === 'Sektor Potensial') $badgeClass = 'badge-potensial';
                            elseif ($item['tipologi'] === 'Sektor Berkembang') $badgeClass = 'badge-berkembang';
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ $item['tipologi'] }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data analisis Tipologi Sektor.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
