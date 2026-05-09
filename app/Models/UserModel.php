<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'password_hash',
        'username',
        'poids_initial',
        'genre',
        'taille',
        'age',
        'role_user'
    ];

    // ##################### les fonctions comencent ici #################

    // public function insertUser($data)
    // {
    //     $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
    //     unset($data['password']);
    //     return $this->insert($data);
    // }

    public function getUserById($id)
    {
        return $this->find($id);
    }
}