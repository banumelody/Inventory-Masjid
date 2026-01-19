<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HelpController extends Controller
{
    public function faq(): View
    {
        return view('help.faq');
    }

    public function guide(): View
    {
        return view('help.guide');
    }
}
