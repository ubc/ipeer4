<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class VersionController extends Controller
{
    public function index()
    {
        return config('ipeer.version');
    }
}
