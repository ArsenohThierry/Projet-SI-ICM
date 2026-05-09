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
        $user = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->to('/login')
                ->withInput()
                ->with('error', 'Identifiants incorrects.')
                ->with('auth_tab', 'login');
        }

        $userOptionModel = new UserOptionModel();
        $plan = $userOptionModel->getLatestPlanByUserId((int) $user['id']);
        $this->setUserSession($user, $plan);

        return redirect()->to('/profile');
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

        $userId = $userModel->createUser($userData);
        if (!$userId) {
            return redirect()->to('/login')
                ->withInput()
                ->with('error', 'Erreur lors de la creation du compte.')
                ->with('auth_tab', 'register');
        }

        $plan = $this->assignDefaultOption($userId);

        $user = $userModel->find($userId);
        $this->setUserSession($user, $plan);

        // Calcul direct de l'IMC avec les données initiales
        $taille = (float) $userData['taille'] / 100; // convertir cm en m
        $imc = (float) $userData['poids_initial'] / ($taille * $taille);

        $data['imc'] = $imc;
        $data['user'] = $user;
        return view('imc', $data);
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
            'user_option' => $plan
        ]);
    }

    private function assignDefaultOption(int $userId): string
    {
        $optionModel = new OptionModel();
        $userOptionModel = new UserOptionModel();

        $freeOptionId = $this->ensureOptionExists($optionModel, 'free', 0);
        $this->ensureOptionExists($optionModel, 'gold', 0);

        $userOptionModel->assignOptionToUser($userId, $freeOptionId);

        return 'free';
    }

    private function ensureOptionExists(OptionModel $optionModel, string $label, float $amount): int
    {
        $existing = $optionModel->findByLabel($label);

        if ($existing) {
            return (int) $existing['id'];
        }

        return $optionModel->createOption($label, $amount);
    }
}
