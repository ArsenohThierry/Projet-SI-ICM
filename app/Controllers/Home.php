<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('login');
    }
    public function calculIMC() {
        // get les valeurs du formulaire 
        $poids = $this->request->getPost('poids');
        $taille = $this->request->getPost('taille');
        $imc = $poids / ($taille * $taille);
        
        $data['imc'] = $imc;
        return view('imc', $data);
    }
    public function toimcform() {
        return view('form');
    }
}
