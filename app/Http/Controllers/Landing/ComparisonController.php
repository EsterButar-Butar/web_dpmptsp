<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;

class ComparisonController extends Controller
{
    public function index()
    {
        return view(
            'landing.comparison'
        );
    }
}