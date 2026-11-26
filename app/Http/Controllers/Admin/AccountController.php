<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Show the forms for the admin's details and password.
     */
    public function edit(Request $request): View
    {
        return view('admin.account.edit', ['user' => $request->user()]);
    }

    /**
     * Show the two-factor authentication settings. Requires a recently confirmed password.
     */
    public function twoFactor(Request $request): View
    {
        return view('admin.account.two-factor', ['user' => $request->user()]);
    }
}
