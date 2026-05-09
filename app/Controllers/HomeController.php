<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/imc');
        }

        return view('login');
    }
}
