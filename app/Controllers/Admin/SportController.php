<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SportModel;
use App\Models\UserModel;

class SportController extends BaseController
{
    public function index()
    {
        $model = new SportModel();
        return view('admin/sports/index', [
            'sports' => $model->orderBy('id', 'DESC')->findAll(),
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
        return view('admin/sports/create', [
            'user' => $this->getSessionUser()
        ]);
    }

    public function store()
    {
        $rules = [
            'nom' => 'required|min_length[2]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/sports/new')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new SportModel();
        $model->insert([
            'nom' => trim((string) $this->request->getPost('nom'))
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

        return view('admin/sports/edit', [
            'sport' => $sport,
            'user' => $this->getSessionUser()
        ]);
    }

    public function update($id)
    {
        $rules = [
            'nom' => 'required|min_length[2]'
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

        $model->update($id, [
            'nom' => trim((string) $this->request->getPost('nom'))
        ]);

        return redirect()->to('/admin/sports')->with('success', 'Activite modifiee.');
    }

    public function delete($id)
    {
        $model = new SportModel();
        if ($model->find($id)) {
            $model->delete($id);
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
        return $userModel->getUserById($id);
    }
}
