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

        return $this->select('sport_objectif.*, sport.nom')
            ->join('sport', 'sport.id = sport_objectif.sport_id')
            ->where('sport_objectif.objectif_id', $objectifId)
            ->where('sport_objectif.age_min <=', $age)
            ->where('sport_objectif.age_max >=', $age)
            ->groupStart()
            ->where('sport_objectif.genre', $genre)
            ->orWhere('sport_objectif.genre IS NULL')
            ->groupEnd()
            ->orderBy('(sport_objectif.calories_brulees * sport_objectif.duree_recommandee)', 'ASC')
            ->limit(3)
            ->findAll();
    }

    public function getSuggestionsSportPerteDePoids(int $age, String $genre): array
    {
        $objectifId = 1;

        return $this->select('sport_objectif.*, sport.nom')
            ->join('sport', 'sport.id = sport_objectif.sport_id')
            ->where('sport_objectif.objectif_id', $objectifId)
            ->where('sport_objectif.age_min <=', $age)
            ->where('sport_objectif.age_max >=', $age)
            ->groupStart()
            ->where('sport_objectif.genre', $genre)
            ->orWhere('sport_objectif.genre IS NULL')
            ->groupEnd()
            ->orderBy('(sport_objectif.calories_brulees * sport_objectif.duree_recommandee)', 'DESC')
            ->limit(3)
            ->findAll();
    }

    public function getSuggestionsSportIMCIdeal(int $age, String $genre): array
    {
        $objectifId = 3;

        return $this->select('sport_objectif.*, sport.nom')
            ->join('sport', 'sport.id = sport_objectif.sport_id')
            ->where('sport_objectif.objectif_id', $objectifId)
            ->where('sport_objectif.age_min <=', $age)
            ->where('sport_objectif.age_max >=', $age)
            ->groupStart()
            ->where('sport_objectif.genre', $genre)
            ->orWhere('sport_objectif.genre IS NULL')
            ->groupEnd()
            ->where('(sport_objectif.calories_brulees * sport_objectif.duree_recommandee) BETWEEN 200 AND 400')
            ->orderBy('(sport_objectif.calories_brulees * sport_objectif.duree_recommandee)', 'DESC')
            ->limit(3)
            ->findAll();
    }
}
