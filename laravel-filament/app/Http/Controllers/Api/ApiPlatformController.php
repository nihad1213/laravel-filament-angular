<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;

class ApiPlatformController extends Controller
{
    public function index()
    {
        return response()->json(Platform::all());
    }
}
