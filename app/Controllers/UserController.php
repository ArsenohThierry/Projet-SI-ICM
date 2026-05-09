<?php

namespace App\Controllers;

use App\Models\ObjectifModel;
use App\Models\UserModel;
use App\Models\OptionModel;
use App\Models\UserOptionModel;
use App\Models\UserObjectifModel;
use App\Services\RegimeService;

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

    public function objectifUser()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $objectifModel = new ObjectifModel();
        $session = session();

        $data['user'] = $user;
        $data['objectifs'] = $objectifModel->getObjectifs();
        $data['disableUnavailable'] = (bool) $session->getFlashdata('disable_unavailable');
        $data['availableObjectifIds'] = $session->getFlashdata('available_objectif_ids') ?? [];
        $data['errorMessage'] = $session->getFlashdata('objectif_validation_error');

        return view('objectifUser', $data);
    }

    public function setObjectif()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $id = (int) $this->request->getPost('objectif_id');
        if ($id <= 0) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un objectif.');
        }

        $objectifModel = new ObjectifModel();
        $objectif = $objectifModel->find($id);
        if (!$objectif) {
            return redirect()->back()->with('error', 'Objectif invalide.');
        }

        $regimeService = new RegimeService();
        try {
            $imc = $regimeService->calculIMC((float) ($user['poids_initial'] ?? 0), (float) ($user['taille'] ?? 0));
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/imc')->with('error', 'Veuillez d\'abord renseigner votre poids et votre taille pour choisir un objectif.');
        }

        try {
            $regimeService->validerObjectif((int) $objectif['id'], $imc);
        } catch (\InvalidArgumentException $e) {
            $objectifsDisponibles = $regimeService->getObjectifsDisponible($imc);
            $availableObjectifIds = array_values(array_filter(array_map(
                static fn($item) => (int) ($item['id'] ?? 0),
                $objectifsDisponibles
            )));

            return redirect()->to('/objectif')
                ->with('disable_unavailable', true)
                ->with('available_objectif_ids', $availableObjectifIds)
                ->with('objectif_validation_error', "Cet objectif ne correspond pas avec votre IMC. Merci de choisir parmi les objectifs disponibles.");
        }

        $userId = (int) session()->get('user_id');
        $userObjectifModel = new UserObjectifModel();
        $saved = $userObjectifModel->assignObjectifToUser($userId, (int) $objectif['id']);

        if (!$saved) {
            return redirect()->back()->with('error', 'Impossible d’enregistrer l’objectif.');
        }

        session()->set('user_objectif', $objectif['id']);
        session()->setFlashdata('success', 'Objectif enregistré.');

        return redirect()->to('/profile');
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
