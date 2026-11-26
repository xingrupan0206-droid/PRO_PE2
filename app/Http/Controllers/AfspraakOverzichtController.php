<?php

namespace App\Http\Controllers;

use App\Providers\Afspraak\Read;
use Illuminate\Contracts\View\View;

class AfspraakOverzichtController extends Controller
{
    public function index(Read $read): View
    {
        return view('afspraken.overzicht', $read->overzicht());
    }
}
