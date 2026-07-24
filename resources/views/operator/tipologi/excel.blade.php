<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analisis Tipologi Sektor</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #145239;
            color: #ffffff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Laporan Hasil Analisis Tipologi Sektor (Klassen Typology)</h2>
    <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    @if($search)
        <p>Pencarian/Filter: "{{ $search }}"</p>
    @endif
    <br>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Daerah Analisis</th>
                <th>Provinsi</th>
                <th>Sektor</th>
                <th>Tahun</th>
                <th>Nilai SS (Dij)</th>
                <th>Nilai LQ (Rasio Kontribusi)</th>
                <th>Tipologi (Kuadran)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tipologiData as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['daerah_analisis'] }}</td>
                    <td>{{ $item['provinsi'] }}</td>
                    <td>{{ $item['sektor'] }}</td>
                    <td>{{ $item['tahun'] }}</td>
                    <td>{{ number_format($item['nilai_ss'], 0, ',', '.') }}</td>
                    <td>{{ number_format($item['nilai_lq'], 4, ',', '.') }}</td>
                    <td>{{ $item['kuadran'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data analisis Tipologi Sektor.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
