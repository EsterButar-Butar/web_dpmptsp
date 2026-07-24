<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Klassen Typology</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5px;
            color: #333;
            line-height: 1.35;
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
            padding: 5px 7px;
            text-align: left;
        }
        th {
            background-color: #145239;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
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
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-k1 {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        .badge-k2 {
            background-color: #cff4fc;
            color: #055160;
            border: 1px solid #b6effb;
        }
        .badge-k3 {
            background-color: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
        }
        .badge-k4 {
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
        <h3>Laporan Hasil Analisis Klassen Typology</h3>
        <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
        @if($search)
            <p>Pencarian/Filter: "{{ $search }}"</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 3%;">No</th>
                <th style="width: 10%;">Daerah Analisis</th>
                <th style="width: 10%;">Daerah Pembanding</th>
                <th style="width: 18%;">Sektor</th>
                <th class="text-center" style="width: 8%;">Tahun</th>
                <th class="text-right" style="width: 6%;">ri</th>
                <th class="text-right" style="width: 6%;">r</th>
                <th class="text-right" style="width: 6%;">yi</th>
                <th class="text-right" style="width: 6%;">y</th>
                <th class="text-center" style="width: 10%;">Kuadran</th>
                <th style="width: 17%;">Klasifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($klassenData as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['daerah_analisis'] }}</td>
                    <td>{{ $item['daerah_pembanding'] }}</td>
                    <td>{{ $item['sektor'] }}</td>
                    <td class="text-center">{{ $item['tahun_awal'] }} - {{ $item['tahun_akhir'] }}</td>
                    <td class="text-right">{{ $item['ri'] }}</td>
                    <td class="text-right">{{ $item['r'] }}</td>
                    <td class="text-right">{{ $item['yi'] }}</td>
                    <td class="text-right">{{ $item['y'] }}</td>
                    <td class="text-center">
                        @php
                            $badgeClass = 'badge-k4';
                            if ($item['kuadran'] === 'Kuadran I') $badgeClass = 'badge-k1';
                            elseif ($item['kuadran'] === 'Kuadran II') $badgeClass = 'badge-k2';
                            elseif ($item['kuadran'] === 'Kuadran III') $badgeClass = 'badge-k3';
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ $item['kuadran'] }}
                        </span>
                    </td>
                    <td>{{ $item['klasifikasi'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data analisis Klassen Typology.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
