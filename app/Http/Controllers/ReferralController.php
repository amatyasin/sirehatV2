<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Display the index page for referrals.
     */
    public function index()
    {
        return view('referrals.index');
    }
}
