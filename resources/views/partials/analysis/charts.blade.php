@if(!empty($dashboard['charts']))

<section class="chart-section">

        {{-- ==========================================================
            LAYOUT KHUSUS LQ
        ========================================================== --}}
        @if(isset($dashboard['charts']['doughnut']) && !empty($dashboard['ranking']))

        <div class="lq-layout">
            <div class="chart-card lq-chart-card">
                <div class="chart-header">
                    <h3>{{ $dashboard['charts']['doughnut']['title'] }}</h3>
                </div>

                <div class="chart-body lq-doughnut-body">

                    <div class="doughnut-wrapper">
                        <canvas id="doughnutChart"></canvas>
                    </div>

                    <div class="doughnut-legend">

                        @foreach($dashboard['charts']['doughnut']['legend'] as $item)

                        <div class="legend-item">

                            <div class="legend-title">

                                <span
                                    class="legend-dot"
                                    style="background:{{ $item['color'] }}"
                                ></span>

                                {{ $item['label'] }}

                            </div>

                            <div
                                class="legend-value"
                                style="color:{{ $item['color'] }}"
                            >
                                {{ $item['value'] }} Sektor
                                ({{ number_format($item['percent'],2,',','.') }}%)
                            </div>

                        </div>

                        @endforeach

                    </div>

                </div>

            </div>

            <div class="ranking-card lq-ranking-card">
                <div class="ranking-header">
                    <h3>Top 5 Sektor Berdasarkan Nilai LQ</h3>
                </div>

                <div class="ranking-body">

                    @foreach($dashboard['ranking']['top'] as $index => $item)

                    <div class="ranking-item">

                        <div class="ranking-number">
                            {{ $index + 1 }}
                        </div>

                        <div class="ranking-content">

                            <div class="ranking-title">
                                {{ \Illuminate\Support\Str::title(\Illuminate\Support\Str::lower($item['nama'] ?? $item['nama_sektor'])) }}
                            </div>

                            <div class="ranking-subtitle">

                                <div>
                                    <strong>Nilai LQ :</strong>
                                    {{ number_format($item['nilai'],4) }}
                                </div>

                                <div>
                                    <strong>Kategori :</strong>
                                    {{ $item['kategori'] }}
                                </div>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

            </div>

        </div>

        {{-- ================= Horizontal Bar ================= --}}
        @if(isset($dashboard['charts']['bar']))

        <div class="chart-card chart-full">

            <div class="chart-header">
                <h3>{{ $dashboard['charts']['bar']['title'] }}</h3>
            </div>

            <div class="chart-body lq-bar-body">
                <canvas id="barChart"></canvas>
            </div>

        </div>

        @endif

        @else

        {{-- =========================
             Layout standar
             SSA, Tipologi, Klassen
        ========================== --}}

        <div class="chart-grid">

            {{-- Doughnut --}}
            @if(isset($dashboard['charts']['doughnut']))
            <div class="chart-card">

                <div class="chart-header">
                    <h3>{{ $dashboard['charts']['doughnut']['title'] }}</h3>
                </div>

                <div class="chart-body lq-pie-body">

                    <div class="doughnut-wrapper">
                        <canvas id="doughnutChart"></canvas>
                    </div>

                    <div class="doughnut-legend">

                        @foreach($dashboard['charts']['doughnut']['legend'] as $item)

                            <div class="legend-item">

                                <div class="legend-title">
                                    <span class="legend-dot"
                                        style="background:{{ $item['color'] }}"></span>

                                    {{ $item['label'] }}
                                </div>

                                <div class="legend-value">
                                    {{ $item['value'] }} sektor
                                    ({{ number_format($item['percent'],1,',','.') }}%)
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>
            @endif

            {{-- Bar --}}
            @if(isset($dashboard['charts']['bar']))
                <div class="chart-card">

                    <div class="chart-header">
                        <h3>{{ $dashboard['charts']['bar']['title'] }}</h3>
                    </div>

                    <div class="chart-body">
                        <canvas id="barChart"></canvas>
                    </div>

                </div>
            @endif
        </div>

    @endif

</section>

@endif