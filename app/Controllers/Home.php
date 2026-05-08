<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('login');
    }

    public function IMCresult() {
        // get les valeurs du formulaire 
        $poids = $this->request->getPost('poids');
        $taille = $this->request->getPost('taille');
        $taille = $taille / 100; // convertir en mètres
        $imc = $poids / ($taille * $taille);
        
        $data['imc'] = $imc;
        return view('imc', $data);
    }
}
