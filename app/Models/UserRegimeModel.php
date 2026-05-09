<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRegimeModel extends Model
{
    protected $table = 'user_regime';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'regime_id',
        'date_save',
        'date_debut',
        'duree'
    ];

}