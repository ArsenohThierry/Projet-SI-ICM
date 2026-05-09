<?php

namespace App\Models;

use CodeIgniter\Model;

class RegimeModel extends Model
{
    protected $table = 'regime';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nom',
        'pourcentage_viande',
        'pourcentage_volaille',
        'pourcentage_poisson',
        'montant',
        'variation_poids',
        'objectif_id'
    ];

}