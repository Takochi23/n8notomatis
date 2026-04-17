<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth', ['mode' => 'login']);
    }

    public function showRegister()
    {
        return view('auth', ['mode' => 'register']);
    }
}
