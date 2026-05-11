<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CodeModel;
use App\Models\UserModel;

class CodeController extends BaseController
{
    public function index()
    {
        $model = new CodeModel();
        return view('admin/codes/index', [
            'codes' => $model->orderBy('id', 'DESC')->findAll(),
            'user' => $this->getSessionUser()
        ]);
    }

    public function create()
    {
        return view('admin/codes/create', [
            'user' => $this->getSessionUser()
        ]);
    }

    public function store()
    {
        $rules = [
            'valeur' => 'required|min_length[4]',
            'montant' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/codes/new')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new CodeModel();
        $model->insert([
            'valeur' => trim((string) $this->request->getPost('valeur')),
            'montant' => (float) $this->request->getPost('montant'),
            'status' => 'unused'
        ]);

        return redirect()->to('/admin/codes')->with('success', 'Code cree.');
    }

    public function delete($id)
    {
        $model = new CodeModel();
        if ($model->find($id)) {
            $model->delete($id);
        }

        return redirect()->to('/admin/codes')->with('success', 'Code supprime.');
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
