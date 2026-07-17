<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbandingan Sektor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    @vite([
        'resources/css/navbar.css',
        'resources/css/home.css',
        'resources/css/about.css',
        'resources/css/analysis.css',
        'resources/css/comparison.css',
        'resources/js/navbar.js',
        'resources/js/home.js',
        'resources/js/about.js',
        'resources/js/analysis.js',
        'resources/js/comparison.js',
    ])
</head>
<body>

{{-- NAVBAR --}}
@include('partials.landing.navbar')



<section class="comparison-page">


    {{-- TITLE --}}
    <h1 class="comparison-title">
        Analisis Perbandingan Sektor
    </h1>



    {{-- FILTER --}}
    <form 
        method="GET"
        action="{{ route('comparison') }}"
        class="comparison-filter"
    >


        <select name="provinsi">

            <option>
                {{ $filter['provinsi'] }}
            </option>

        </select>



        <select name="kabupaten">

            <option>
                {{ $filter['kabupaten'] }}
            </option>

        </select>



        <select name="sektor">

            <option>
                {{ $filter['sektor'] }}
            </option>

        </select>



        <select name="tahun_awal">


            @for($i=2019;$i<=2026;$i++)

            <option 
                value="{{ $i }}"
                {{ $filter['tahun_awal']==$i ? 'selected' : '' }}
            >

                {{ $i }}

            </option>

            @endfor


        </select>




        <select name="tahun_akhir">


            @for($i=2019;$i<=2026;$i++)

            <option 
                value="{{ $i }}"
                {{ $filter['tahun_akhir']==$i ? 'selected' : '' }}
            >

                {{ $i }}

            </option>

            @endfor


        </select>




        <button type="submit">

            Analisis

        </button>



    </form>





    {{-- SUMMARY --}}

    <div class="comparison-summary">



        <div class="summary-card">

            <h2>
                {{ $summary['growth'] }}%
            </h2>

            <p>
                Pertumbuhan rata-rata
            </p>

        </div>





        <div class="summary-card">

            <h2>
                {{ $summary['contribution'] }}%
            </h2>

            <p>
                Kontribusi rata-rata
            </p>

        </div>






        <div class="summary-card">

            <h2>
                {{ $summary['lq'] }}
            </h2>

            <p>
                Nilai LQ terakhir
            </p>

        </div>





        <div class="summary-card">

            <h2>
                {{ $summary['status'] }}
            </h2>

            <p>
                {{ $summary['kategori'] }}
            </p>

        </div>



    </div>








    {{-- CHART --}}

    <div class="comparison-chart-grid">


        <div class="chart-card">


            <h3>
                Trend Pertumbuhan Sektor (%)
            </h3>


            <canvas id="growthChart"></canvas>


        </div>






        <div class="chart-card">


            <h3>
                Perbandingan Indikator Tahunan
            </h3>


            <canvas id="indicatorChart"></canvas>


        </div>



    </div>








    {{-- TABLE --}}

    <div class="table-card">


        <h3>
            Daftar Sektor Berdasarkan Tipologi
        </h3>



        <table>


            <thead>

                <tr>

                    <th>Tahun</th>
                    <th>Pertumbuhan</th>
                    <th>Kontribusi</th>
                    <th>LQ</th>
                    <th>SSA</th>
                    <th>Kuadran</th>
                    <th>Status</th>

                </tr>

            </thead>





            <tbody>


            @foreach($trend as $row)


                <tr>

                    <td>{{ $row['tahun'] }}</td>

                    <td>{{ $row['growth'] }}</td>

                    <td>{{ $row['contribution'] }}</td>

                    <td>{{ $row['lq'] }}</td>

                    <td>{{ $row['ssa'] }}</td>

                    <td>{{ $row['kuadran'] }}</td>

                    <td>{{ $row['status'] }}</td>


                </tr>


            @endforeach


            </tbody>



        </table>


    </div>




</section>





<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



<script>


const comparisonData =
@json($trend);





new Chart(

    document.getElementById('growthChart'),

    {

        type:'line',


        data:{


            labels:
                comparisonData.map(
                    item => item.tahun
                ),



            datasets:[

                {

                    label:'Pertumbuhan',

                    data:
                        comparisonData.map(
                            item => item.growth
                        )

                }

            ]


        }


    }

);







new Chart(

    document.getElementById('indicatorChart'),


    {

        type:'line',



        data:{


            labels:

                comparisonData.map(

                    item => item.tahun

                ),




            datasets:[


                {

                    label:'LQ',

                    data:

                        comparisonData.map(

                            item => item.lq

                        )

                },




                {

                    label:'SSA',

                    data:

                        comparisonData.map(

                            item => item.ssa

                        )

                },





                {

                    label:'Kontribusi',

                    data:

                        comparisonData.map(

                            item => item.contribution

                        )

                }



            ]


        }


    }


);



</script>

</body>
</html>