<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssetManagementController extends Controller
{
    public function index()
    {
        return view('asset-management.index');
    }
}
