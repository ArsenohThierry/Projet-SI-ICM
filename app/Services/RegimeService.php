<?php

namespace App\Services;

// Import des Models
use App\Models\RegimeModel;
use App\Models\SportModel;
use App\Models\SportObjectifModel;
use App\Models\ObjectifModel;
use App\Models\UserObjectifModel;
use App\Models\UserRegimeModel;
use App\Models\UserSportModel;
use DateTime;

class RegimeService
{
    protected $regimeModel;
    protected $sportModel;
    protected $sportObjectifModel;
    protected $objectifModel;
    protected $userObjectifModel;
    protected $userRegimeModel;
    protected $userSportModel;

    public function __construct()
    {
        $this->regimeModel      = new RegimeModel();
        $this->sportModel       = new SportModel();
        $this->objectifModel    = new ObjectifModel();
        $this->userObjectifModel = new UserObjectifModel();
        $this->userRegimeModel  = new UserRegimeModel();
        $this->userSportModel   = new UserSportModel();
        $this->sportObjectifModel = new SportObjectifModel();
    }

    public function calculIMC($poids, $taille)
    {
        if ($taille <= 0) {
            throw new \InvalidArgumentException("La taille doit être supérieure à zéro.");
        }
        return $poids / (($taille / 100) ** 2);
    }

    public function calculPoidsIdeal($taille)
    {
        if ($taille <= 0) {
            throw new \InvalidArgumentException("La taille doit être supérieure à zéro.");
        }
        // poids_ideal = imc_ideal * (taille / 100) ** 2
        return 22 * (($taille / 100) ** 2);
    }

    public function calculDureeRegime($poids, $poidsIdeal, $variationPoids)
    {
        return abs($poids - $poidsIdeal) / abs($variationPoids);
    }

    public function getObjectifsDisponible($imc)
    {
        $listObjectifs = array();

        if ($imc < 18.5) {
            $listObjectifs[] = $this->objectifModel->where('libelle', 'Prise de poids')->first();
            $listObjectifs[] = $this->objectifModel->where('libelle', 'IMC Ideal')->first();
        } elseif ($imc >= 18.5 && $imc < 25) {
            $listObjectifs[] = $this->objectifModel->where('libelle', 'IMC Ideal')->first();
        } else {
            $listObjectifs[] = $this->objectifModel->where('libelle', 'Perte de poids')->first();
            $listObjectifs[] = $this->objectifModel->where('libelle', 'IMC Ideal')->first();
        }
        return $listObjectifs;
    }

    public function validerObjectif($objectifId, $imc)
    {
        $objectif = $this->objectifModel->find($objectifId);
        if (!$objectif) {
            throw new \InvalidArgumentException("Objectif non trouvé.");
        }

        if ($objectif['libelle'] === 'Prise de poids' && $imc >= 18.5) {
            throw new \InvalidArgumentException("L'objectif 'Prise de poids' n'est pas adapté pour un IMC de $imc.");
        }

        if ($objectif['libelle'] === 'Perte de poids' && $imc < 18.5) {
            throw new \InvalidArgumentException("L'objectif 'Perte de poids' n'est pas adapté pour un IMC de $imc.");
        }

        return true;
    }

    public function getRegimesPourObjectif($objectifId)
    {
        return $this->regimeModel->where('objectif_id', $objectifId)->findAll();
    }

    public function getSportObjectifsPourObjectif($objectifId)
    {
        return $this->sportObjectifModel
            ->where('sport_objectif.objectif_id', $objectifId)
            ->findAll();
    }

    public function getSuggestionsRegime($imc, $objectifId)
    {
        $regimes = $this->getRegimesPourObjectif($objectifId);

        if (empty($regimes)) {
            return "Aucun régime disponible pour cet objectif.";
        }

        // objectifId = 1 => Perte de poids
        // objectifId = 2 => Prise de poids
        // objectifId = 3 => IMC Ideal

        if ($objectifId == 2) {
            return $this->regimeModel->getSuggestionsRegimePriseDePoids();
        } elseif ($objectifId == 1) {
            return $this->regimeModel->getSuggestionsRegimePerteDePoids();
        } elseif ($objectifId == 3) {
            if ($imc >= 18.5 && $imc < 25) {
                return $this->regimeModel->getSuggestionsRegimeIMCIdeal();
            } elseif ($imc < 18.5) {
                return $this->regimeModel->getSuggestionsRegimePriseDePoids();
            } elseif ($imc >= 25) {
                return $this->regimeModel->getSuggestionsRegimePerteDePoids();
            } else {
                return "Aucun régime ne correspond à votre IMC et à votre objectif.";
            }
        }

        return "Aucun régime ne correspond à votre IMC et à votre objectif.";
    }

    public function getSuggestionsSportObjectif(float $imc, int $objectifId, int $age, String $genre)
    {
        $sportsObjectif = $this->getSportObjectifsPourObjectif($objectifId);

        if (empty($sportsObjectif)) {
            return "Aucun sports disponible pour cet objectif.";
        }

        // objectifId = 1 => Perte de poids
        // objectifId = 2 => Prise de poids
        // objectifId = 3 => IMC Ideal

        if ($objectifId == 2) {
            return $this->sportObjectifModel->getSuggestionsSportPriseDePoids($age, $genre);
        } elseif ($objectifId == 1) {
            return $this->sportObjectifModel->getSuggestionsSportPerteDePoids($age, $genre);
        } elseif ($objectifId == 3) {
            if ($imc >= 18.5 && $imc < 25) {
            return $this->sportObjectifModel->getSuggestionsSportIMCIdeal($age, $genre);
            } elseif ($imc < 18.5) {
            return $this->sportObjectifModel->getSuggestionsSportPriseDePoids($age, $genre);
            } elseif ($imc >= 25) {
            return $this->sportObjectifModel->getSuggestionsSportPerteDePoids($age, $genre);
            } else {
                return "Aucun sports ne correspond à votre IMC et à votre objectif.";
            }
        }

        return "Aucun sports ne correspond à votre IMC et à votre objectif.";
    }

    public function enregistrerChoixRegime(int $userId, int $regimeId, DateTime $dateDebut, int $duree)
    {
        $this->userRegimeModel->insert([
            'user_id' => $userId,
            'regime_id' => $regimeId,
            'date_save' => date('Y-m-d H:i:s'),
            'date_debut' => $dateDebut,
            'duree' => $duree
        ]);
    }

    public function enregistrerChoixSportObjectif(int $userId, int $sportObjectifId, DateTime $dateDebut)
    {
        $this->userSportModel->insert([
            'user_id' => $userId,
            'sport_objectif_id' => $sportObjectifId,
            'date_save' => date('Y-m-d H:i:s'),
            'date_debut' => $dateDebut
        ]);
    }
}


// si imc < 18.5 => prise de poids, imc ideal 
// si imc >= 18.5 et imc < 25 => imc ideal 
// si imc >= 25 => perte de poids, imc ideal

// elseif ($imc >= 18.5 && $imc < 25 && $objectifId == 3) {
//             return $this->regimeModel->getSuggestionsRegimeIMCIdeal();
//         } elseif ($imc >= 25 && $objectifId == 1) {
//             return $this->regimeModel->getSuggestionsRegimePerteDePoids();
//         } else {
//             return "Aucun régime ne correspond à votre IMC et à votre objectif.";
//         }