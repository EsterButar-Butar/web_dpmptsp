<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;

class InvestmentMapController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::orderBy('nama')->get();

        return view('landing.map', compact('lokasi'));
    }
}