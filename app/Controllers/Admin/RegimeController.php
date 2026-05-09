<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegimeModel;
use App\Models\UserModel;

class RegimeController extends BaseController
{
    public function index()
    {
        $model = new RegimeModel();
        $data = [
            'regimes' => $model->orderBy('id', 'DESC')->findAll(),
            'user' => $this->getSessionUser()
        ];

        return view('admin/regimes/index', $data);
    }

    public function show($id)
    {
        $model = new RegimeModel();
        $regime = $model->find($id);
        if (!$regime) {
            return redirect()->to('/admin/regimes')->with('error', 'Regime introuvable.');
        }

        return view('admin/regimes/show', [
            'regime' => $regime,
            'user' => $this->getSessionUser()
        ]);
    }

    public function create()
    {
        return view('admin/regimes/create', [
            'user' => $this->getSessionUser()
        ]);
    }

    public function store()
    {
        $rules = [
            'nom' => 'required|min_length[2]',
            'pourcentage_viande' => 'required|numeric',
            'pourcentage_volaille' => 'required|numeric',
            'pourcentage_poisson' => 'required|numeric',
            'montant' => 'required|numeric',
            'variation_poids' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/regimes/new')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new RegimeModel();
        $model->insert([
            'nom' => trim((string) $this->request->getPost('nom')),
            'pourcentage_viande' => (float) $this->request->getPost('pourcentage_viande'),
            'pourcentage_volaille' => (float) $this->request->getPost('pourcentage_volaille'),
            'pourcentage_poisson' => (float) $this->request->getPost('pourcentage_poisson'),
            'montant' => (float) $this->request->getPost('montant'),
            'variation_poids' => (float) $this->request->getPost('variation_poids')
        ]);

        return redirect()->to('/admin/regimes')->with('success', 'Regime ajoute.');
    }

    public function edit($id)
    {
        $model = new RegimeModel();
        $regime = $model->find($id);
        if (!$regime) {
            return redirect()->to('/admin/regimes')->with('error', 'Regime introuvable.');
        }

        return view('admin/regimes/edit', [
            'regime' => $regime,
            'user' => $this->getSessionUser()
        ]);
    }

    public function update($id)
    {
        $rules = [
            'nom' => 'required|min_length[2]',
            'pourcentage_viande' => 'required|numeric',
            'pourcentage_volaille' => 'required|numeric',
            'pourcentage_poisson' => 'required|numeric',
            'montant' => 'required|numeric',
            'variation_poids' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to("/admin/regimes/{$id}/edit")
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new RegimeModel();
        if (!$model->find($id)) {
            return redirect()->to('/admin/regimes')->with('error', 'Regime introuvable.');
        }

        $model->update($id, [
            'nom' => trim((string) $this->request->getPost('nom')),
            'pourcentage_viande' => (float) $this->request->getPost('pourcentage_viande'),
            'pourcentage_volaille' => (float) $this->request->getPost('pourcentage_volaille'),
            'pourcentage_poisson' => (float) $this->request->getPost('pourcentage_poisson'),
            'montant' => (float) $this->request->getPost('montant'),
            'variation_poids' => (float) $this->request->getPost('variation_poids')
        ]);

        return redirect()->to('/admin/regimes')->with('success', 'Regime modifie.');
    }

    public function delete($id)
    {
        $model = new RegimeModel();
        if ($model->find($id)) {
            $model->delete($id);
        }

        return redirect()->to('/admin/regimes')->with('success', 'Regime supprime.');
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
