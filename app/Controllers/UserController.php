<?php

namespace App\Controllers;
use App\Models\UserModel;

class UserController extends BaseController
{

    public function IMCresult()
    {
        // get les valeurs du formulaire 
        $poids = $this->request->getPost('poids');
        $taille = $this->request->getPost('taille');
        $taille = $taille / 100; // convertir en mètres
        $imc = $poids / ($taille * $taille);

        $data['imc'] = $imc;
        return view('imc', $data);
    }

    public function userProfile()
    {
        $usermodel = new UserModel();

        $id = 1;
        // mila recuperena avy am session !!!!!!!!!!!!!!!!
        
        $user = $usermodel->getUserById($id);
        $data['user'] = $user;
        return view('profil-user', $data);
    }
}
