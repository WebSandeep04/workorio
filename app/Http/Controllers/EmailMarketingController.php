<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailMarketingController extends Controller
{
    public function index()
    {
        return view('email-marketing.index');
    }
}
