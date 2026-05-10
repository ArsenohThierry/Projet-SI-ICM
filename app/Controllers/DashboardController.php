<?php

namespace App\Controllers;

use App\Services\DashboardService;

class DashboardController extends BaseController
{
    protected $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    public function showDashboard()
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return redirect()->to('/login');
        }

        if (session()->get('role_user') === 'admin') {
            return redirect()->to('/admin/regimes');
        }

        $statistiques = $this->dashboardService->getStatistiques($userId);
        $historiquePoids = $this->dashboardService->getHistoriquePoids($userId, 90);
        $donneesSimulation = $this->dashboardService->getDonneesSimulation($userId);
        $activitesSelectionnees = $this->dashboardService->getActivitesSelectionnees($userId);
        $objectifActif = $this->dashboardService->getObjectifActif($userId);

        $mouvementModel = new \App\Models\MouvementModel();
        return view('dashboard', [
            'statistiques' => $statistiques,
            'historiquePoids' => $historiquePoids,
            'donneesSimulation' => $donneesSimulation,
            'activitesSelectionnees' => $activitesSelectionnees,
            'objectifActif' => $objectifActif,
            'balance' => $mouvementModel->getBalanceByUserId($userId)
        ]);
    }

    public function addPoids()
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return redirect()->to('/login');
        }

        $poids = (float) $this->request->getPost('poids');
        $dateSave = trim((string) $this->request->getPost('date_save'));

        if ($poids <= 0 || $poids > 500 || !$dateSave) {
            return redirect()->back()->with('error', 'Veuillez entrer une date et un poids valides.');
        }

        try {
            $dateObj = new \DateTime($dateSave);
            $dateFormatted = $dateObj->format('Y-m-d 12:00:00');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Date invalide.');
        }

        $poidsUserModel = new \App\Models\PoidsUserModel();
        $poidsUserModel->insert([
            'user_id' => $userId,
            'poids' => $poids,
            'date_save' => $dateFormatted
        ]);

        return redirect()->to('/dashboard')->with('success', 'Poids enregistré avec succès !');
    }

    public function unauthorized()
    {
        return view('errors/unauthorized');
    }
}
