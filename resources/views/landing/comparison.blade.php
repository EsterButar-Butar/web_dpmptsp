@extends('layouts.landing')


@section('title','Perbandingan Sektor')


@section('content')


<section class="comparison-page">


<h1>
    Analisis Perbandingan Sektor
</h1>


<form 
    method="GET"
    action="{{ route('comparison.index') }}"
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
{{ $filter['tahun_awal']==$i?'selected':'' }}
>
{{ $i }}
</option>

@endfor

</select>


<select name="tahun_akhir">

@for($i=2019;$i<=2026;$i++)

<option 
value="{{ $i }}"
{{ $filter['tahun_akhir']==$i?'selected':'' }}
>
{{ $i }}
</option>

@endfor

</select>


<button>
Analisis
</button>


</form>



<div class="summary-grid">


<div>
<h2>{{ $summary['growth'] }}%</h2>
<p>Pertumbuhan rata-rata</p>
</div>


<div>
<h2>{{ $summary['contribution'] }}%</h2>
<p>Kontribusi rata-rata</p>
</div>


<div>
<h2>{{ $summary['lq'] }}</h2>
<p>Nilai LQ terakhir</p>
</div>


<div>
<h2>{{ $summary['status'] }}</h2>
<p>{{ $summary['kategori'] }}</p>
</div>


</div>



<div class="chart-grid">


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


@endsection



@push('scripts')


<script>


let data = @json($trend);


new Chart(
document.getElementById('growthChart'),
{
type:'line',

data:{

labels:data.map(x=>x.tahun),

datasets:[{

label:'Pertumbuhan',

data:data.map(x=>x.growth)

}]

}

}
);



new Chart(
document.getElementById('indicatorChart'),
{

type:'line',

data:{

labels:data.map(x=>x.tahun),

datasets:[


{
label:'LQ',
data:data.map(x=>x.lq)
},


{
label:'SSA',
data:data.map(x=>x.ssa)
},


{
label:'Kontribusi',
data:data.map(x=>x.contribution)
}


]

}


}

);


</script>


@endpush