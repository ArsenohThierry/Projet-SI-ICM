<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OptionModel;
use App\Models\UserOptionModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/imc');
        }

        return view('login');
    }


    public function authenticate()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/login')
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('auth_tab', 'login');
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->to('/login')
                ->withInput()
                ->with('error', 'Identifiants incorrects.')
                ->with('auth_tab', 'login');
        }

        $plan = $this->getUserPlan((int) $user['id']);
        $this->setUserSession($user, $plan);

        if (($user['role_user'] ?? 'user') === 'admin') {
            return redirect()->to('/admin/regimes');
        }

        return redirect()->to('/imc');
    }

    public function register()
    {
        $rules = [
            'nom' => 'required|min_length[2]',
            'prenom' => 'required|min_length[2]',
            'username' => 'required|min_length[3]|is_unique[user.username]',
            'email' => 'required|valid_email|is_unique[user.email]',
            'password' => 'required|min_length[8]',
            'taille' => 'required|numeric',
            'poids_initial' => 'required|numeric',
            'age' => 'required|integer',
            'genre' => 'required|in_list[Homme,Femme]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/login')
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('auth_tab', 'register');
        }

        $userModel = new UserModel();

        $userData = [
            'nom' => trim((string) $this->request->getPost('nom')),
            'prenom' => trim((string) $this->request->getPost('prenom')),
            'username' => trim((string) $this->request->getPost('username')),
            'email' => trim((string) $this->request->getPost('email')),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'poids_initial' => (float) $this->request->getPost('poids_initial'),
            'taille' => (float) $this->request->getPost('taille'),
            'age' => (int) $this->request->getPost('age'),
            'genre' => (string) $this->request->getPost('genre'),
            'role_user' => 'user'
        ];

        $userId = $userModel->insert($userData, true);
        if (!$userId) {
            return redirect()->to('/login')
                ->withInput()
                ->with('error', 'Erreur lors de la creation du compte.')
                ->with('auth_tab', 'register');
        }

        $plan = $this->assignDefaultOption($userId);

        $user = $userModel->find($userId);
        $this->setUserSession($user, $plan);

        return redirect()->to('/imc');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    private function setUserSession(array $user, ?string $plan = null): void
    {
        $session = session();
        $session->regenerate();
        $session->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role_user' => $user['role_user'] ?? 'user',
            'user_option' => $plan,
            'logged_in' => true
        ]);
    }

    private function assignDefaultOption(int $userId): string
    {
        $optionModel = new OptionModel();
        $userOptionModel = new UserOptionModel();

        $freeOptionId = $this->ensureOptionExists('free', 0);
        $this->ensureOptionExists('gold', 0);

        $userOptionModel->insert([
            'user_id' => $userId,
            'option_id' => $freeOptionId,
            'date_save' => date('Y-m-d H:i:s')
        ]);

        return 'free';
    }

    private function getUserPlan(int $userId): ?string
    {
        $userOptionModel = new UserOptionModel();
        $row = $userOptionModel
            ->select('option.libelle')
            ->join('option', 'option.id = user_option.option_id', 'left')
            ->where('user_option.user_id', $userId)
            ->orderBy('user_option.date_save', 'DESC')
            ->first();

        return $row['libelle'] ?? null;
    }

    private function ensureOptionExists(string $label, float $amount): int
    {
        $optionModel = new OptionModel();
        $existing = $optionModel->where('libelle', $label)->first();

        if ($existing) {
            return (int) $existing['id'];
        }

        return (int) $optionModel->insert([
            'libelle' => $label,
            'montant' => $amount
        ], true);
    }
}
