<<<<<<< HEAD
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Investasi</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite([
        'resources/css/navbar.css',
        'resources/css/home.css',
        'resources/css/about.css',
        'resources/css/analysis.css',
        'resources/js/navbar.js',
        'resources/js/home.js',
        'resources/js/about.js',
        'resources/js/analysis.js',
    ])
</head>

<body>

    {{-- Navbar --}}
    @include('partials.landing.navbar')

    <section class="analysis-page">

        <h1 class="analysis-title">
            Dashboard Executive of Sumatera Investment
        </h1>

        <form
            method="GET"
            action="{{ route('analysis') }}"
            class="filter-box"
        >

            {{-- =======================
                PROVINSI
            ======================== --}}
            <select
                name="provinsi"
                id="provinsi"
                onchange="this.form.submit()"
            >

                @foreach($provinsiList as $prov)

                    <option
                        value="{{ $prov->provinsi_id }}"
                        {{ ($provinsi ?? '') == $prov->provinsi_id ? 'selected' : '' }}
                    >
                        {{ $prov->nama_provinsi }}
                    </option>

                @endforeach

            </select>
=======
@php
    use Illuminate\Support\Str;
@endphp

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
/>

@vite([
    'resources/css/navbar.css',
    'resources/css/home.css',
    'resources/css/about.css',
    'resources/css/analysis.css',
    'resources/js/navbar.js',
    'resources/js/home.js',
    'resources/js/about.js',
    'resources/js/analysis.js',
])

@include('partials.landing.navbar')

<section class="analysis-page">

    <h1 class="analysis-title">
        Dashboard Analisis Sektoral
    </h1>

    @include('partials.analysis.filters')

    @if(!empty($dashboard))

        @if(!empty($dashboard['header']))
            <div class="analysis-header">

                <h2>

                    {{ $dashboard['header']['title'] }}

                    pada

                    <strong>

                        {{ Str::title(Str::lower($dashboard['header']['kabupaten'])) }}
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29

                    </strong>

<<<<<<< HEAD
            {{-- =======================
                KABUPATEN
            ======================== --}}
            <select
                name="kabupaten"
                id="kabupaten"
                onchange="this.form.submit()"
            >

                @foreach($kabupatenList as $kab)

                    <option
                        value="{{ $kab->kab_id }}"
                        {{ ($kabupaten ?? '') == $kab->kab_id ? 'selected' : '' }}
                    >
                        {{ $kab->nama_kabupaten }}
                    </option>

                @endforeach

            </select>
=======
                    Tahun
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29

                    {{ $dashboard['header']['tahun'] }}

<<<<<<< HEAD
            {{-- =======================
                METODE
            ======================== --}}
            <select name="metode">

                <option
                    value="lq"
                    {{ ($metode ?? '') == 'lq' ? 'selected' : '' }}
                >
                    LQ
                </option>

                <option
                    value="ssa"
                    {{ ($metode ?? '') == 'ssa' ? 'selected' : '' }}
                >
                    SSA
                </option>

                <option
                    value="tipologi"
                    {{ ($metode ?? '') == 'tipologi' ? 'selected' : '' }}
                >
                    Tipologi Sektor
                </option>

                <option
                    value="klassen"
                    {{ ($metode ?? '') == 'klassen' ? 'selected' : '' }}
                >
                    Tipologi Klassen
                </option>

            </select>
=======
                </h2>
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29

                <p>

<<<<<<< HEAD
            {{-- =======================
                TAHUN
            ======================== --}}
            <select name="tahun">

                @for($i = 2021; $i <= 2025; $i++)

                    <option
                        value="{{ $i }}"
                        {{ ($tahun ?? '') == $i ? 'selected' : '' }}
                    >
                        {{ $i }}
                    </option>

                @endfor

            </select>
=======
                    {{ $dashboard['header']['description'] }}
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29

                </p>

<<<<<<< HEAD
            <button type="submit">
                Analisis
            </button>

        </form>
=======
            </div>
        @endif
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29

        @include('partials.analysis.summary')
        
        @if(
            isset($dashboard['charts']['doughnut']) ||
            isset($dashboard['charts']['bar']) ||
            isset($dashboard['charts']['scatter'])
        )
            @include('partials.analysis.charts')
        @endif

<<<<<<< HEAD
        {{-- =======================
            SUMMARY
        ======================== --}}
        <div class="summary-grid">

            @foreach($summary as $item)

                <div class="summary-card">

                    <h3>
                        {{ $item['value'] }}
                    </h3>

                    <strong>
                        {{ $item['title'] }}
                    </strong>

                    <p>
                        {{ $item['text'] }}
                    </p>

                </div>

            @endforeach
=======
        @if(!empty($dashboard['tabs']))
            @include('partials.analysis.tabs')
        @endif

        @if(!empty($dashboard['table']))
            @include('partials.analysis.table')
        @endif

    @else
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29

        <div class="empty-analysis">
            Silakan pilih kabupaten dan metode analisis.
        </div>

<<<<<<< HEAD

        {{-- =======================
            CHART
        ======================== --}}
        <div class="chart-box">

            <canvas id="analysisChart"></canvas>

        </div>

    </section>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        const labels = @json($sektor);
        const values = @json($nilai);

        new Chart(
            document.getElementById('analysisChart'),
            {
                type: 'bar',

                data: {

                    labels: labels,

                    datasets: [
=======
    @endif
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29

                        {

<<<<<<< HEAD
                            label: 'Nilai {{ strtoupper($metode) }} Tahun {{ $tahun }}',

                            data: values

                        }

                    ]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false

                }

            }
        );

    </script>

</body>
</html>
=======
@if(!empty($dashboard))
<script>
    window.dashboardCharts = @json($dashboard['charts'] ?? []);
</script>
@endif
>>>>>>> 256d8aec886297726809100f5a3cc0916fce2a29
