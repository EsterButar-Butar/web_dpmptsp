@if(!empty($dashboard['summary']))

<section class="summary-section">

    <div class="summary-grid">

        @foreach($dashboard['summary'] as $card)

            <div class="summary-card {{ $card['color'] }}">

                <div class="summary-top">

                    <div class="summary-icon">
                        <i class="{{ $card['icon'] }}"></i>
                    </div>

                    <div class="summary-value">
                        {{ $card['value'] }}
                    </div>

                </div>

                <div class="summary-bottom">

                    <h4>
                        {{ $card['title'] }}
                    </h4>

                    <p>
                        {{ $card['description'] }}
                    </p>

                </div>

            </div>

        @endforeach

    </div>

</section>

@endif