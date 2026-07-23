@if(!empty($dashboard['ranking']['top']))

<section class="ranking-section">

    <div class="ranking-card">

        <div class="ranking-header">

            <h3>
                Top 5 Sektor Berdasarkan Nilai LQ
            </h3>

        </div>

        <div class="ranking-body">

            @foreach($dashboard['ranking']['top'] as $index => $item)

                <div class="ranking-item">

                    <div class="ranking-number">

                        {{ $index + 1 }}

                    </div>

                    <div class="ranking-content">

                        <div class="ranking-title">

                            {{ Str::title(Str::lower($item['nama'] ?? $item['nama_sektor'])) }}

                        </div>

                        <div class="ranking-subtitle">

                            <div>

                                <strong>Nilai LQ :</strong>

                                {{ number_format($item['nilai'], 4) }}

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

</section>

@endif