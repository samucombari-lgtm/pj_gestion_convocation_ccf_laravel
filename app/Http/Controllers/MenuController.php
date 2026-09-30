<?php

namespace App\Http\Controllers;

class MenuController extends Controller
{
    // C'est la méthode qu'on a reliée à la route GET /menu.
    // Elle ne fait qu'une chose : renvoyer la vue "menu".
    public function index()
    {
        return view('menu');
    }
}