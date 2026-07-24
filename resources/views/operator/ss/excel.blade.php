<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analisis Shift Share (SS)</title>
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
    <h2>Laporan Hasil Analisis Shift Share (SS)</h2>
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
                <th>Daerah Pembanding</th>
                <th>Sektor</th>
                <th>Tahun</th>
                <th>Rij</th>
                <th>Rin</th>
                <th>Rn</th>
                <th>Nij</th>
                <th>Mij</th>
                <th>Cij</th>
                <th>Dij</th>
                <th>Status Pertumbuhan</th>
                <th>Status Daya Saing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ssData as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['daerah_analisis'] }}</td>
                    <td>{{ $item['daerah_pembanding'] }}</td>
                    <td>{{ $item['sektor'] }}</td>
                    <td>{{ $item['tahun'] }}</td>
                    <td>{{ number_format($item['rij'], 4, ',', '.') }}</td>
                    <td>{{ number_format($item['rin'], 4, ',', '.') }}</td>
                    <td>{{ number_format($item['rn'], 4, ',', '.') }}</td>
                    <td>{{ number_format($item['nij'], 0, ',', '.') }}</td>
                    <td>{{ number_format($item['mij'], 0, ',', '.') }}</td>
                    <td>{{ number_format($item['cij'], 0, ',', '.') }}</td>
                    <td>{{ number_format($item['dij'], 0, ',', '.') }}</td>
                    <td>{{ $item['status_pertumbuhan'] }}</td>
                    <td>{{ $item['status_daya_saing'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14">Tidak ada data analisis SS.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
