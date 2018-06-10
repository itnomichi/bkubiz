<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        return view('home');
    }

    public function login(Request $request)
    {
        return view('login');
    }

}
