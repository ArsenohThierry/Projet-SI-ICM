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
                ->with('objectif_validation_error', "Cet objectif ne correspond pas avec votre IMC. Pour votre bien-être, merci de choisir parmi les objectifs disponibles.");
        }

        $userId = (int) session()->get('user_id');
        $userObjectifModel = new UserObjectifModel();
        $saved = $userObjectifModel->assignObjectifToUser($userId, (int) $objectif['id']);

        if (!$saved) {
            return redirect()->back()->with('error', 'Impossible d’enregistrer l’objectif.');
        }

        session()->set('user_objectif', $objectif['id']);
        session()->setFlashdata('success', 'Objectif enregistré.');

        return redirect()->to('/regime');
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

    public function regimeSelection()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $objectifId = (int) session()->get('user_objectif');
        if ($objectifId <= 0) {
            return redirect()->to('/objectif')->with('error', 'Veuillez d\'abord choisir un objectif.');
        }

        $regimeService = new RegimeService();
        try {
            $imc = $regimeService->calculIMC((float) ($user['poids_initial'] ?? 0), (float) ($user['taille'] ?? 0));
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/imc')->with('error', 'Veuillez renseigner votre poids et votre taille.');
        }

        $regimes = $regimeService->getSuggestionsRegime($imc, $objectifId);

        $data['user'] = $user;
        $data['regimes'] = is_string($regimes) ? [] : $regimes;
        $data['errorMessage'] = is_string($regimes) ? $regimes : null;

        return view('suggestion_regime', $data);
    }

    public function setRegime()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $regimeId = (int) $this->request->getPost('regime_id');
        if ($regimeId <= 0) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un régime.');
        }

        $dateDebutStr = trim((string) $this->request->getPost('date_debut'));
        if (!$dateDebutStr) {
            return redirect()->back()->with('error', 'Veuillez indiquer une date de début.');
        }

        try {
            $dateDebut = new \DateTime($dateDebutStr);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Date de début invalide.');
        }

        $objectifId = (int) session()->get('user_objectif');
        if ($objectifId <= 0) {
            return redirect()->to('/objectif')->with('error', 'Session expirée, veuillez rechoisir votre objectif.');
        }

        $regimeService = new RegimeService();
        $regime = (new \App\Models\RegimeModel())->find($regimeId);
        
        if (!$regime || (int) ($regime['objectif_id'] ?? 0) !== $objectifId) {
            return redirect()->back()->with('error', 'Régime invalide pour cet objectif.');
        }

        $userId = (int) session()->get('user_id');
        $poidsIdeal = $regimeService->calculPoidsIdeal((float) ($user['taille'] ?? 0));
        $dureeRegime = (int) ceil($regimeService->calculDureeRegime(
            (float) ($user['poids_initial'] ?? 0),
            $poidsIdeal,
            (float) ($regime['variation_poids'] ?? 0)
        ));

        // Si durée est 0 (IMC déjà idéal ou variation = 0), utiliser 30 jours par défaut
        if ($dureeRegime <= 0) {
            $dureeRegime = 30;
        }

        $regimeService->enregistrerChoixRegime(
            $userId,
            $regimeId,
            $dateDebut,
            $dureeRegime
        );

        session()->set('user_regime', $regimeId);
        session()->set('date_debut_regime', $dateDebut->format('Y-m-d'));
        session()->setFlashdata('success', 'Régime enregistré avec succès !');

        return redirect()->to('/sport');
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

    public function sportSelection()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $objectifId = (int) session()->get('user_objectif');
        if ($objectifId <= 0) {
            return redirect()->to('/objectif')->with('error', 'Veuillez d\'abord choisir un objectif.');
        }

        $dateDebutRegime = session()->get('date_debut_regime');
        if (!$dateDebutRegime) {
            return redirect()->to('/regime')->with('error', 'Veuillez d\'abord choisir un régime.');
        }

        $regimeService = new RegimeService();
        try {
            $imc = $regimeService->calculIMC((float) ($user['poids_initial'] ?? 0), (float) ($user['taille'] ?? 0));
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/imc')->with('error', 'Veuillez renseigner votre poids et votre taille.');
        }

        $age = (int) ($user['age'] ?? 0);
        $genre = $user['genre'] ?? '';

        $sports = $regimeService->getSuggestionsSportObjectif($imc, $objectifId, $age, $genre);

        $data['user'] = $user;
        $data['sports'] = is_string($sports) ? [] : $sports;
        $data['errorMessage'] = is_string($sports) ? $sports : null;
        $data['date_debut_regime'] = $dateDebutRegime;

        return view('suggestion_sport', $data);
    }

    public function setSport()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $sportObjectifId = (int) $this->request->getPost('sport_objectif_id');
        if ($sportObjectifId <= 0) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un sport.');
        }

        $objectifId = (int) session()->get('user_objectif');
        if ($objectifId <= 0) {
            return redirect()->to('/objectif')->with('error', 'Session expirée, veuillez rechoisir votre objectif.');
        }

        $dateDebutStr = trim((string) session()->get('date_debut_regime'));
        if (!$dateDebutStr) {
            return redirect()->to('/regime')->with('error', 'Session expirée, veuillez rechoisir un régime.');
        }

        $regimeService = new RegimeService();
        $sportObjectif = (new \App\Models\SportObjectifModel())->find($sportObjectifId);
        
        if (!$sportObjectif || (int) ($sportObjectif['objectif_id'] ?? 0) !== $objectifId) {
            return redirect()->back()->with('error', 'Sport invalide pour cet objectif.');
        }

        $userId = (int) session()->get('user_id');
        
        try {
            $dateDebut = new \DateTime($dateDebutStr);
        } catch (\Exception $e) {
            return redirect()->to('/regime')->with('error', 'Date invalide, veuillez rechoisir un régime.');
        }

        $regimeService->enregistrerChoixSportObjectif(
            $userId,
            $sportObjectifId,
            $dateDebut
        );

        session()->set('user_sport_objectif', $sportObjectifId);
        session()->setFlashdata('success', 'Sport enregistré avec succès !');

        return redirect()->to('/profile');
    }

    public function getSuggestionsRegimeObjectif(float $imc, int $objectifId)
    {   $regimeService = new RegimeService();
        $regimesObjectif = $regimeService->getSuggestionsRegime($imc, $objectifId);
        $data['regimes'] = $regimesObjectif;

        if (empty($regimesObjectif)) {
            return "Aucun régime disponible pour cet objectif.";
        }

        return view('suggestions_regime', $data);
    }

        // objectifId = 1 => Perte de poids
        // objectifId = 2 => Prise de poids
        // objectifId = 3 => IMC Ideal
}
