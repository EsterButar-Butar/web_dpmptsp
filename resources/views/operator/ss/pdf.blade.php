<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Shift Share (SS)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
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
            padding: 5px 6px;
            text-align: left;
        }
        th {
            background-color: #145239;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5px;
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
        .badge-positive {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        .badge-negative {
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
        <h3>Laporan Hasil Analisis Shift Share (SS)</h3>
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
                <th style="width: 15%;">Sektor</th>
                <th class="text-center" style="width: 8%;">Tahun</th>
                <th class="text-right" style="width: 5%;">Rij</th>
                <th class="text-right" style="width: 5%;">Rin</th>
                <th class="text-right" style="width: 5%;">Rn</th>
                <th class="text-right" style="width: 7%;">Nij</th>
                <th class="text-right" style="width: 7%;">Mij</th>
                <th class="text-right" style="width: 7%;">Cij</th>
                <th class="text-right" style="width: 7%;">Dij</th>
                <th style="width: 8%;">Pertumbuhan</th>
                <th style="width: 8%;">Daya Saing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ssData as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['daerah_analisis'] }}</td>
                    <td>{{ $item['daerah_pembanding'] }}</td>
                    <td>{{ $item['sektor'] }}</td>
                    <td class="text-center">{{ $item['tahun_awal'] }} - {{ $item['tahun_akhir'] }}</td>
                    <td class="text-right">{{ $item['rij'] }}</td>
                    <td class="text-right">{{ $item['rin'] }}</td>
                    <td class="text-right">{{ $item['rn'] }}</td>
                    <td class="text-right">{{ number_format($item['nij'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item['mij'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item['cij'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item['dij'], 2, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $item['mij'] > 0 ? 'badge-positive' : 'badge-negative' }}">
                            {{ $item['status_pertumbuhan'] }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $item['cij'] > 0 ? 'badge-positive' : 'badge-negative' }}">
                            {{ $item['status_daya_saing'] }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data analisis Shift Share.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
