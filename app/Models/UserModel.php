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
        'age',
        'genre',
        'taille',
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

    public function getUserWithMenuSelections($id): ?array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return null;
        }

        return $this->appendMenuSelections($user);
    }

    public function appendMenuSelections(array $user): array
    {
        $userId = (int) ($user['id'] ?? 0);
        if ($userId <= 0) {
            return $user;
        }

        $objectif = $this->db->table('user_objectif')
            ->select('objectif.libelle')
            ->join('objectif', 'objectif.id = user_objectif.objectif_id')
            ->where('user_objectif.user_id', $userId)
            ->orderBy('user_objectif.date_save', 'DESC')
            ->orderBy('user_objectif.id', 'DESC')
            ->get()
            ->getRowArray();

        $regime = $this->db->table('user_regime')
            ->select('regime.nom')
            ->join('regime', 'regime.id = user_regime.regime_id')
            ->where('user_regime.user_id', $userId)
            ->orderBy('user_regime.date_save', 'DESC')
            ->orderBy('user_regime.id', 'DESC')
            ->get()
            ->getRowArray();

        $sport = $this->db->table('user_sport')
            ->select('sport.nom')
            ->join('sport_objectif', 'sport_objectif.id = user_sport.sport_objectif_id')
            ->join('sport', 'sport.id = sport_objectif.sport_id')
            ->where('user_sport.user_id', $userId)
            ->orderBy('user_sport.date_save', 'DESC')
            ->orderBy('user_sport.id', 'DESC')
            ->get()
            ->getRowArray();

        $user['objectif_choisi'] = $objectif['libelle'] ?? '';
        $user['regime_choisi'] = $regime['nom'] ?? '';
        $user['sport_choisi'] = $sport['nom'] ?? '';

        return $user;
    }

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }

    public function createUser(array $data): int
    {
        return (int) $this->insert($data, true);
    }
}
