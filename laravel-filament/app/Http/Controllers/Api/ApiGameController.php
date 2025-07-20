<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class ApiGameController extends Controller
{
    public function index()
    {
        return response()->json(Game::all());
    }

    public function show(Game $game)
    {
        return response()->json($game);
    }
}
