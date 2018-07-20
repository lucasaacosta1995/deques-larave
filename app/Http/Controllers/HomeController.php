<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\jo_proyecto;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      //  $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proyectos = jo_proyecto::with(['autor', 'congreso.provincia','tramite','tipo'])->where('status',1)->get()->toArray();

        foreach ($proyectos as $proyecto){
            $data = $proyecto;
        }
        return view('home');
    }
}
