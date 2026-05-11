<?php

namespace App\Models;

use CodeIgniter\Model;

class SportModel extends Model
{
    protected $table = 'sport';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nom'];

    // LISTE avec jointure
    public function getAllWithObjectif()
    {
        return $this->select('sport.id as sport_id, sport.nom, sport_objectif.id as sport_objectif_id, sport_objectif.objectif_id, sport_objectif.age_min, sport_objectif.age_max, sport_objectif.calories_brulees, sport_objectif.duree_recommandee, sport_objectif.genre, objectif.libelle')
            ->join('sport_objectif', 'sport_objectif.sport_id = sport.id')
            ->join('objectif', 'objectif.id = sport_objectif.objectif_id')
            ->orderBy('sport.id', 'DESC')
            ->findAll();
    }

    // DETAIL COMPLET
    public function getByIdWithDetails($id)
    {
        return $this->select('sport.id as sport_id, sport.nom, sport_objectif.id as sport_objectif_id, sport_objectif.objectif_id, sport_objectif.age_min, sport_objectif.age_max, sport_objectif.calories_brulees, sport_objectif.duree_recommandee, sport_objectif.genre, objectif.libelle')
            ->join('sport_objectif', 'sport_objectif.sport_id = sport.id')
            ->join('objectif', 'objectif.id = sport_objectif.objectif_id')
            ->where('sport.id', $id)
            ->first();
    }

    // UPDATE COMPLET (sport + sport_objectif)
    public function updateSportFull($id, $sportData, $objectifData, $sportObjectifModel)
    {
        $this->update($id, $sportData);

        $sportObjectifModel
            ->where('sport_id', $id)
            ->set($objectifData)
            ->update();
    }

    // DELETE COMPLET
    public function deleteSportFull($id, $sportObjectifModel)
    {
        $sportObjectifModel
            ->where('sport_id', $id)
            ->delete();

        $this->delete($id);
    }
}