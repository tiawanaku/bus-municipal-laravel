<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aviso;


class AvisoController extends Controller
{
    public function index()
{
    
    $avisos = Aviso::all();
    
    return view('layouts.app', ['avisos' => $avisos]);
}
}
