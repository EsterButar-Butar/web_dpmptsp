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

                    </strong>

                    Tahun

                    {{ $dashboard['header']['tahun'] }}

                </h2>

                <p>

                    {{ $dashboard['header']['description'] }}

                </p>

            </div>
        @endif

        @include('partials.analysis.summary')
        
        @if(
            isset($dashboard['charts']['doughnut']) ||
            isset($dashboard['charts']['bar']) ||
            isset($dashboard['charts']['scatter'])
        )
            @include('partials.analysis.charts')
        @endif

        @if(!empty($dashboard['tabs']))
            @include('partials.analysis.tabs')
        @endif

        @if(!empty($dashboard['table']))
            @include('partials.analysis.table')
        @endif

    @else

        <div class="empty-analysis">
            Silakan pilih kabupaten dan metode analisis.
        </div>

    @endif

</section>

@if(!empty($dashboard))
<script>
    window.dashboardCharts = @json($dashboard['charts'] ?? []);
</script>
@endif
