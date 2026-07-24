<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analisis Klassen Typology</title>
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
    <h2>Laporan Hasil Analisis Klassen Typology</h2>
    <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    @if($search)
        <p>Pencarian/Filter: "{{ $search }}"</p>
    @endif
    <br>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Sektor</th>
                <th>Kab/Kota</th>
                <th>Provinsi</th>
                <th>Laju Pertumbuhan Sektor Analisis (%)</th>
                <th>Laju Pertumbuhan Sektor Pembanding (%)</th>
                <th>Kontribusi Sektor Analisis (%)</th>
                <th>Kontribusi Sektor Pembanding (%)</th>
                <th>Kuadran</th>
                <th>Klasifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($klassenData as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['sektor'] }}</td>
                    <td>{{ $item['kabupaten'] }}</td>
                    <td>{{ $item['provinsi'] }}</td>
                    <td>{{ number_format($item['g_sektor_analisis'], 4, ',', '.') }}</td>
                    <td>{{ number_format($item['g_sektor_pembanding'], 4, ',', '.') }}</td>
                    <td>{{ number_format($item['s_sektor_analisis'], 4, ',', '.') }}</td>
                    <td>{{ number_format($item['s_sektor_pembanding'], 4, ',', '.') }}</td>
                    <td>{{ $item['kuadran'] }}</td>
                    <td>{{ $item['klasifikasi'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Tidak ada data analisis Klassen Typology.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
