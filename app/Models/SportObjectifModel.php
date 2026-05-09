<?php

namespace App\Models;

use CodeIgniter\Model;

class SportObjectifModel extends Model
{
    protected $table = 'sport_objectif';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'sport_id',
        'objectif_id',
        'age_min',
        'age_max',
        'calories_brulees',
        'duree_recommandee',
        'genre'
    ];

}