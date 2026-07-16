<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tipologi;

$data = Tipologi::limit(5)->get();
foreach ($data as $d) {
    echo "ID: " . $d->id . "\n";
    echo "Sektor ID: " . $d->sektor_id . "\n";
    echo "Tingkat: " . $d->tingkat_wilayah . "\n";
    echo "Daerah Analisis: " . $d->daerah_analisis . "\n";
    echo "Daerah Pembanding: " . $d->daerah_pembanding . "\n";
    echo "PDRB Sektor Analisis Awal: " . $d->pdrb_sektor_analisis_awal . "\n";
    echo "Total PDRB Analisis Awal: " . $d->total_pdrb_analisis_awal . "\n";
    echo "PDRB Sektor Pembanding Awal: " . $d->pdrb_sektor_pembanding_awal . "\n";
    echo "Total PDRB Pembanding Awal: " . $d->total_pdrb_pembanding_awal . "\n";
    echo "---------------------------\n";
}
