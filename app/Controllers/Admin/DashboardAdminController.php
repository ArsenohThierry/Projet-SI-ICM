<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ObjectifModel;
use App\Models\RegimeModel;
use App\Models\UserModel;
use App\Services\DashboardAdminService;

class DashboardAdminController extends BaseController
{
    public function index()
    {
        $service = new DashboardAdminService();

        $data = [];
        $data['totalUsers'] = $service->getNombreUtilisateursNonAdmin();
        $data['totalGold'] = $service->getNombreUtilisateursGoldActifs();
        $data['totalRevenu'] = $service->getRevenueTotal();
        $data['regimesActifs'] = $service->getNombreRegimesActifs();

        $data['dataObjectifs'] = $service->getObjectifsRepartition();
        $data['dataRegimes'] = $service->getRegimesPopulaires();
        $data['dataInscriptions'] = $service->getInscriptionsParMois();

        $data['tableauCroise'] = $service->getTableauCroiseRegimeObjectif();
        $data['listeUtilisateurs'] = $service->getUsersWithImcObjectifRegime();

        $data['objectifs'] = (new ObjectifModel())->orderBy('id', 'ASC')->findAll();
        $data['regimes'] = (new RegimeModel())->orderBy('nom', 'ASC')->findAll();
        $data['user'] = $this->getSessionUser();

        return view('admin/dashboard', $data);
    }

    private function getSessionUser(): ?array
    {
        $id = (int) session()->get('user_id');
        if ($id <= 0) {
            return null;
        }

        $userModel = new UserModel();
        return $userModel->getUserWithMenuSelections($id);
    }
}
