<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OptionModel;
use App\Models\UserOptionModel;

class UserController extends BaseController
{
    public function imcForm(): string
    {
        return $this->getIMC();
    }

    public function getIMC()
    {
        $user = $this->getSessionUser();
        $data['user'] = $user;

        if (!$user) {
            return redirect()->to('/login');
        }

        $poids = (float) ($user['poids_initial'] ?? 0);
        $taille = (float) ($user['taille'] ?? 0);

        if ($poids > 0 && $taille > 0) {
            $taille = $taille / 100; // convertir en metres
            $data['imc'] = $poids / ($taille * $taille);
        }

        return view('imc', $data);
    }

    public function IMCresult()
    {
        // get les valeurs du formulaire
        $poids = (float) $this->request->getPost('poids');
        $taille = (float) $this->request->getPost('taille');
        if ($poids <= 0 || $taille <= 0) {
            return redirect()->to('/imc')->with('error', 'Veuillez saisir des valeurs valides.');
        }
        $taille = $taille / 100; // convertir en metres
        $imc = $poids / ($taille * $taille);

        $data['imc'] = $imc;
        $data['user'] = $this->getSessionUser();
        return view('imc', $data);
    }

    public function userProfile()
    {
        $usermodel = new UserModel();
        $id = (int) session()->get('user_id');

        $user = $usermodel->getUserById($id);
        if (!$user) {
            session()->destroy();
            return redirect()->to('/login');
        }

        $data['user'] = $user;
        return view('profil-user', $data);
    }

    public function upgradeToGold()
    {
        $userId = (int) session()->get('user_id');
        $optionModel = new OptionModel();
        $userOptionModel = new UserOptionModel();

        $gold = $optionModel->where('libelle', 'gold')->first();
        if (!$gold) {
            $goldId = (int) $optionModel->insert([
                'libelle' => 'gold',
                'montant' => 0
            ], true);
        } else {
            $goldId = (int) $gold['id'];
        }

        $userOptionModel->insert([
            'user_id' => $userId,
            'option_id' => $goldId,
            'date_save' => date('Y-m-d H:i:s')
        ]);

        session()->set('user_option', 'gold');

        return redirect()->to('/profile');
    }

    private function getSessionUser(): ?array
    {
        $id = (int) session()->get('user_id');
        if ($id <= 0) {
            return null;
        }

        $userModel = new UserModel();
        return $userModel->getUserById($id);
    }
}
