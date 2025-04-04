<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index(){
        return Inertia::render('Homepage');
    }
    public function TestPage(){
        return Inertia::render('Testpage');
    }
}

