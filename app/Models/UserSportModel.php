<?php

namespace App\Models;

use CodeIgniter\Model;

class UserSportModel extends Model
{
    protected $table = 'user_sport';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'sport_objectif_id',
        'date_save',
        'date_debut'
    ];

}