<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataLatih;

class RiwayatPrediksiController extends Controller
{
    public function index()
    {
        $riwayat = DataLatih::orderBy('created_at', 'desc')->get();

        return view('pengukuran.data_latih.riwayat', compact('riwayat'));
    }
}
