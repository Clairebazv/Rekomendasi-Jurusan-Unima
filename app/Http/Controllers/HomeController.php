<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\Alternatif;

class HomeController extends Controller
{
    public function index(){
        $mhs = User::where('role','mhs')->count();
        $kriteria = Kriteria::count();
        $subkriteria = SubKriteria::count();
        $alternatif = Alternatif::count();

        return view('home', compact('mhs','kriteria','subkriteria','alternatif'));
    }
}
