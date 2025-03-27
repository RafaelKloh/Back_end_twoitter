<?php

namespace App\Controllers;
use App\Http\Request;
use App\Http\Response;

Class Not_found_controller
{
    public function index(Request $request, Response $reponse)
    {
        $reponse::json([
            'error' => true,
            'success' => false,
            'message' => 'Sorry, route not foud'
        ], 404);
        return;
    }
}