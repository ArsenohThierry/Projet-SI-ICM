<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SportModel;
use App\Models\SportObjectifModel;
use App\Models\ObjectifModel;
use App\Models\UserModel;

class SportController extends BaseController
{
    public function index()
    {
        $model = new SportModel();
        return view('admin/sports/index', [
            'sports' => $model->getAllWithObjectif(),
            'user' => $this->getSessionUser()
        ]);
    }

    public function show($id)
    {
        $model = new SportModel();
        $sport = $model->find($id);
        if (!$sport) {
            return redirect()->to('/admin/sports')->with('error', 'Activite introuvable.');
        }

        return view('admin/sports/show', [
            'sport' => $sport,
            'user' => $this->getSessionUser()
        ]);
    }

    public function create()
    {
        $objectifs = (new ObjectifModel())->getObjectifs();
        return view('admin/sports/create', [
            'user' => $this->getSessionUser(),
            'objectifs' => $objectifs
        ]);
    }

    public function store()
    {
        $rules = [
            'nom' => 'required|min_length[2]',
            'objectif_id' => 'required|is_natural_no_zero',
            'age_min' => 'permit_empty|is_natural',
            'calories_brulees' => 'required|numeric',
            'duree_recommandee' => 'required|is_natural',
            'genre' => 'permit_empty|max_length[1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/sports/new')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // 1. insert sport
        $model = new SportModel();
        $model->insert([
            'nom' => trim((string) $this->request->getPost('nom'))
        ]);
        $sportId = (int) $model->getInsertID();

        // 2. insert sport_objectif
        $soModel = new SportObjectifModel();
        $soModel->insert([
            'sport_id' => $sportId,
            'objectif_id' => (int) $this->request->getPost('objectif_id'),
            'age_min' => $this->request->getPost('age_min') === '' ? null : (int) $this->request->getPost('age_min'),
            'age_max' => $this->request->getPost('age_max') === '' ? null : (int) $this->request->getPost('age_max'),
            'calories_brulees' => (float) $this->request->getPost('calories_brulees'),
            'duree_recommandee' => (int) $this->request->getPost('duree_recommandee'),
            'genre' => $this->request->getPost('genre') ?: null
        ]);

        return redirect()->to('/admin/sports')->with('success', 'Activite ajoutee.');
    }

    public function edit($id)
    {
        $model = new SportModel();
        $sport = $model->find($id);
        if (!$sport) {
            return redirect()->to('/admin/sports')->with('error', 'Activite introuvable.');
        }
        $soModel = new SportObjectifModel();
        $so = $soModel->where('sport_id', $id)->first();
        $objectifs = (new ObjectifModel())->getObjectifs();

        return view('admin/sports/edit', [
            'sport' => $sport,
            'so' => $so,
            'objectifs' => $objectifs,
            'user' => $this->getSessionUser()
        ]);
    }

    public function update($id)
    {
        $rules = [
            'nom' => 'required|min_length[2]',
            'objectif_id' => 'required|is_natural_no_zero',
            'age_min' => 'permit_empty|is_natural',
            'calories_brulees' => 'required|numeric',
            'duree_recommandee' => 'required|is_natural',
            'genre' => 'permit_empty|max_length[1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to("/admin/sports/{$id}/edit")
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new SportModel();
        if (!$model->find($id)) {
            return redirect()->to('/admin/sports')->with('error', 'Activite introuvable.');
        }

        // update sport
        $model->update($id, [
            'nom' => trim((string) $this->request->getPost('nom'))
        ]);

        // update or insert sport_objectif
        $soModel = new SportObjectifModel();
        $existing = $soModel->where('sport_id', $id)->first();
        $soData = [
            'sport_id' => $id,
            'objectif_id' => (int) $this->request->getPost('objectif_id'),
            'age_min' => $this->request->getPost('age_min') === '' ? null : (int) $this->request->getPost('age_min'),
            'age_max' => $this->request->getPost('age_max') === '' ? null : (int) $this->request->getPost('age_max'),
            'calories_brulees' => (float) $this->request->getPost('calories_brulees'),
            'duree_recommandee' => (int) $this->request->getPost('duree_recommandee'),
            'genre' => $this->request->getPost('genre') ?: null
        ];

        if ($existing) {
            $soModel->update($existing['id'], $soData);
        } else {
            $soModel->insert($soData);
        }

        return redirect()->to('/admin/sports')->with('success', 'Activite modifiee.');
    }

    public function delete($id)
    {
        $sportModel = new SportModel();
        $sport = $sportModel->find($id);
        if (!$sport) {
            return redirect()->to('/admin/sports')->with('error', 'Activite introuvable.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $soModel = new SportObjectifModel();
        $sportModel->deleteSportFull($id, $soModel);

        $db->transComplete();

        if ($db->transStatus() === false || $sportModel->find($id)) {
            return redirect()->to('/admin/sports')->with('error', 'Impossible de supprimer cette activite.');
        }

        return redirect()->to('/admin/sports')->with('success', 'Activite supprimee.');
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
