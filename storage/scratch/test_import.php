<?php
require __DIR__."/../../vendor/autoload.php";
$app = require_once __DIR__."/../../bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $sektor = App\Models\Sektor::firstOrCreate(["nama_sektor" => "Test Sektor"]);
    echo "Sektor OK. ";
    App\Models\Tipologi::create([
        "user_id" => 1,
        "sektor_id" => $sektor->id,
        "tingkat_wilayah" => "Kabupaten/Kota",
        "daerah_analisis" => "Medan",
        "daerah_pembanding" => "Sumatera Utara",
        "tahun_awal" => 2021,
        "tahun_akhir" => 2022,
        "pdrb_sektor_analisis_awal" => 15000,
        "pdrb_sektor_analisis_akhir" => 16000,
        "total_pdrb_analisis_awal" => 100000,
        "total_pdrb_analisis_akhir" => 110000,
        "pdrb_sektor_pembanding_awal" => 50000,
        "pdrb_sektor_pembanding_akhir" => 52000,
        "total_pdrb_pembanding_awal" => 200000,
        "total_pdrb_pembanding_akhir" => 210000,
        "nilai_ss" => 0.5,
        "nilai_lq" => 1.2,
        "tipologi" => "Maju"
    ]);
    echo "Tipologi OK.";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}

