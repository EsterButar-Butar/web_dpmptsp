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

{{-- NAVBAR --}}
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

        <select name="provinsi">
            <option>
                Sumatera Utara
            </option>
        </select>


        <select name="kabupaten">
            <option>
                Deli Serdang
            </option>
        </select>


        <select name="metode">

            <option 
                value="lq"
                {{ $metode=='lq' ? 'selected' : '' }}
            >
                LQ
            </option>


            <option 
                value="ssa"
                {{ $metode=='ssa' ? 'selected' : '' }}
            >
                SSA
            </option>


            <option value="tipologi">
                Tipologi Sektor
            </option>


            <option value="klassen">
                Tipologi Klassen
            </option>


        </select>


        <select name="tahun">

            @for($i=2021;$i<=2025;$i++)

            <option 
                value="{{ $i }}"
                {{ $tahun==$i ? 'selected' : '' }}
            >
                {{ $i }}
            </option>

            @endfor

        </select>


        <button type="submit">
            Analisis
        </button>


    </form>




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

    </div>





    <div class="chart-box">

        <canvas 
            id="analysisChart">
        </canvas>

    </div>


</section>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>

const labels =
@json($sektor);


const values =
@json($nilai);



new Chart(

    document.getElementById('analysisChart'),

    {

        type:'bar',


        data:{


            labels:labels,


            datasets:[

                {

                    label:'Nilai {{ strtoupper($metode) }} Tahun {{ $tahun }}',

                    data:values

                }

            ]

        },


        options:{


            responsive:true,


            maintainAspectRatio:false


        }

    }

);

</script>

</body>
</html>