<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PrivacyController extends Controller
{
    /**
     * Show the privacy statement.
     */
    public function __invoke(): View
    {
        return view('site.privacy');
    }
}
