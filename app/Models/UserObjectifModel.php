<?php

namespace App\Models;

use CodeIgniter\Model;

class UserObjectifModel extends Model
{
    protected $table = 'user_objectif';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'objectif_id',
        'date_save'
    ];

    public function assignObjectifToUser(int $userId, int $objectifId, int $objectifAppliqueId): bool
    {
        return (bool) $this->insert([
            'user_id' => $userId,
            'objectif_id' => $objectifId,
            'objectif_applique_id' => $objectifAppliqueId,
            'date_save' => date('Y-m-d H:i:s')
        ]);
    }
}