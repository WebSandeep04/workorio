<?php

namespace App\Http\Controllers;

class CriticalPathController extends Controller
{
    /**
     * Display the Workflow Critical Path page.
     */
    public function index()
    {
        return view('critical-path.index');
    }
}


