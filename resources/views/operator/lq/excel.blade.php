<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analisis Location Quotient (LQ)</title>
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
    <h2>Laporan Hasil Analisis Location Quotient (LQ)</h2>
    <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    @if($search)
        <p>Pencarian/Filter: "{{ $search }}"</p>
    @endif
    <br>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tingkat Wilayah</th>
                <th>Daerah Analisis</th>
                <th>Daerah Pembanding</th>
                <th>Sektor</th>
                <th>Tahun</th>
                <th>Nilai LQ</th>
                <th>Kategori</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lqData as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['tingkat_wilayah'] }}</td>
                    <td>{{ $item['kabupaten'] ?? $item['daerah_analisis'] }}</td>
                    <td>{{ $item['provinsi'] ?? $item['daerah_pembanding'] }}</td>
                    <td>{{ $item['sektor'] }}</td>
                    <td>{{ $item['tahun'] }}</td>
                    <td>{{ number_format($item['nilai_lq'], 4, ',', '.') }}</td>
                    <td>{{ $item['kategori'] }}</td>
                    <td>{{ $item['keterangan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Tidak ada data analisis LQ.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
