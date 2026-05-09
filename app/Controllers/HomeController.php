<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            if (session()->get('role_user') === 'admin') {
                return redirect()->to('/admin/regimes');
            }
            return redirect()->to('/imc');
        }

        return view('login');
    }

    public function unauthorized()
    {
        return view('errors/unauthorized');
    }
}
