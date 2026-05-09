<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function login(): string
    {
        return view('login');
    }
}
