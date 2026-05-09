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

    public function getSuggestionsSportPriseDePoids(int $age, String $genre): array
    {
        $objectifId = 2;

        return $this->where('objectif_id', $objectifId)
            ->where('age_min <=', $age)
            ->where('age_max >=', $age)
            ->groupStart()
            ->where('genre', $genre)
            ->orWhere('genre IS NULL')
            ->groupEnd()
            ->orderBy('(calories_brulees * duree_recommandee)', 'ASC')
            ->limit(3)
            ->findAll();
    }

    public function getSuggestionsSportPerteDePoids(int $age, String $genre): array
    {
        $objectifId = 1;

        return $this->where('objectif_id', $objectifId)
            ->where('age_min <=', $age)
            ->where('age_max >=', $age)
            ->groupStart()
            ->where('genre', $genre)
            ->orWhere('genre IS NULL')
            ->groupEnd()
            ->orderBy('(calories_brulees * duree_recommandee)', 'DESC')
            ->limit(3)
            ->findAll();
    }

    public function getSuggestionsSportIMCIdeal(int $age, String $genre): array
    {
        $objectifId = 3;

        return $this->where('objectif_id', $objectifId)
            ->where('age_min <=', $age)
            ->where('age_max >=', $age)
            ->groupStart()
            ->where('genre', $genre)
            ->orWhere('genre IS NULL')
            ->groupEnd()
            ->where('(calories_brulees * duree_proposee) BETWEEN 200 AND 400')
            ->orderBy('(calories_brulees * duree_recommandee)', 'DESC')
            ->limit(3)
            ->findAll();
    }
}
