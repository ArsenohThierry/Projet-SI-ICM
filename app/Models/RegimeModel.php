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

    public function getSuggestionsRegimePriseDePoids() : array
    {
        $db = $this->db;

        $objectifId = 2;


        return $this->where('objectif_id', $objectifId)
            ->where('variation_poids >', 0)
            ->orderBy('(variation_poids * 0.7) - (montant * 0.3)', 'DESC')
            ->limit(3)
            ->findAll();
    }

    public function getSuggestionsRegimePerteDePoids() : array
    {
        $db = $this->db;

        $objectifId = 1;

        return $this->where('objectif_id', $objectifId)
            ->where('variation_poids <', 0)
            ->orderBy('(ABS(variation_poids) * 0.7) - (montant * 0.3)', 'DESC')
            ->limit(3)
            ->findAll();
    }

    public function getSuggestionsRegimeIMCIdeal() : array
    {
        $db = $this->db;

        $objectifId = 3;

        return $this->where('objectif_id', $objectifId)
            ->orderBy('(ABS(variation_poids) * 0.7) - (montant * 0.3)', 'ASC')
            ->limit(3)
            ->findAll();
    }
}
