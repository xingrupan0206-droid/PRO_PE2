<?php

namespace App\Http\Controllers;

use App\Providers\Afspraak\Read;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AfspraakOverzichtController extends Controller
{
    public function index(Request $request, Read $read): View
    {
        return view('afspraken.overzicht', $read->overzicht($request));
    }
}
