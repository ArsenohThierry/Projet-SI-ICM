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
use App\Models\PoidsUserModel;
use App\Models\UserModel;
use DateTime;

class DashboardService
{
    protected $regimeModel;
    protected $userRegimeModel;
    protected $userSportModel;
    protected $poidsUserModel;
    protected $userModel;
    protected $regimeService;
    protected $objectifModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
        $this->userRegimeModel = new UserRegimeModel();
        $this->userSportModel = new UserSportModel();
        $this->poidsUserModel = new PoidsUserModel();
        $this->userModel = new UserModel();
        $this->regimeService = new RegimeService();
        $this->objectifModel = new ObjectifModel();
    }


    public function getObjectifActif($userId): ?array
    {
        $user = $this->userModel->getUserWithMenuSelections($userId);
        if (!$user) {
            return null;
        }

        $regimeActif = $this->userRegimeModel
            ->where('user_id', $userId)
            ->orderBy('date_debut', 'DESC')
            ->first();

        if (!$regimeActif) {
            return null;
        }

        $regime = $this->regimeModel->find($regimeActif['regime_id']);
        if (!$regime) {
            return null;
        }

        $objectif = $this->objectifModel->find((int) ($regime['objectif_id'] ?? 0));
        if (!$objectif) {
            return null;
        }

        $color = 'var(--primary-dark)';
        if ($objectif['libelle'] === 'Prise de poids') {
            $color = 'var(--gold)';
        } elseif ($objectif['libelle'] === 'IMC Ideal') {
            $color = 'var(--info)';
        }

        return [
            'id' => (int) $objectif['id'],
            'libelle' => $objectif['libelle'] ?? 'Objectif',
            'color' => $color
        ];
    }


    public function getActivitesSelectionnees($userId, int $limit = 5): array
    {
        $activites = $this->userSportModel
            ->select('user_sport.date_save, user_sport.date_debut, sport.nom AS sport_nom, objectif.libelle AS objectif_libelle, sport_objectif.calories_brulees, sport_objectif.duree_recommandee')
            ->join('sport_objectif', 'sport_objectif.id = user_sport.sport_objectif_id')
            ->join('sport', 'sport.id = sport_objectif.sport_id')
            ->join('objectif', 'objectif.id = sport_objectif.objectif_id')
            ->where('user_sport.user_id', $userId)
            ->orderBy('user_sport.date_save', 'DESC')
            ->limit($limit)
            ->findAll();

        $data = [];

        foreach ($activites as $activity) {
            $dateSave = new DateTime($activity['date_save']);
            $dateDebut = !empty($activity['date_debut']) ? new DateTime($activity['date_debut']) : null;
            $now = new DateTime();
            $diff = $now->diff($dateSave);

            if ($diff->days === 0) {
                $timeLabel = 'Régulièrement';
            } elseif ($diff->days === 1) {
                $timeLabel = 'Hier';
            } elseif ($diff->days < 7) {
                $timeLabel = 'Il y a ' . $diff->days . 'j';
            } else {
                $timeLabel = $dateSave->format('d/m');
            }

            $data[] = [
                'sport_nom' => $activity['sport_nom'] ?? 'Sport',
                'objectif_libelle' => $activity['objectif_libelle'] ?? 'Objectif',
                'calories_brulees' => (float) ($activity['calories_brulees'] ?? 0),
                'duree_recommandee' => (int) ($activity['duree_recommandee'] ?? 0),
                'date_save' => $dateSave->format('Y-m-d H:i:s'),
                'date_debut' => $dateDebut ? $dateDebut->format('Y-m-d H:i:s') : null,
                'time_label' => $timeLabel,
            ];
        }

        return $data;
    }


    public function getStatistiques($userId)
    {
        $user = $this->userModel->getUserWithMenuSelections($userId);

        if (!$user) {
            return null;
        }

        $poidsActuel = $this->poidsUserModel
            ->where('user_id', $userId)
            ->orderBy('date_save', 'DESC')
            ->first();

        $poidsActuelValue = (float) ($poidsActuel ? $poidsActuel['poids'] : $user['poids_initial']);

        $poidsInitial = (float) $user['poids_initial'];

        $premierPoids = $this->poidsUserModel
            ->where('user_id', $userId)
            ->orderBy('date_save', 'ASC')
            ->first();

        $premierPoidsValue = (float) ($premierPoids ? $premierPoids['poids'] : $poidsInitial);
        $variationPoids = $poidsActuelValue - $premierPoidsValue;
        $joursEcoules = 0;

        $progression = $poidsInitial - $poidsActuelValue;
        $progressionPourcent = $poidsInitial > 0 ? ($progression / $poidsInitial) * 100 : 0;

        $regimeActif = $this->userRegimeModel
            ->where('user_id', $userId)
            ->orderBy('date_save', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        $regimeActifName = null;
        $durationRegime = null;
        if ($regimeActif) {
            $regime = $this->regimeModel->find($regimeActif['regime_id']);
            $regimeActifName = $regime['nom'] ?? 'Non disponible';

            $dateDebut = new DateTime($regimeActif['date_debut']);
            $dureeRegime = (int) ($regimeActif['duree'] ?? 30); // en semaines
            $dateFin = (clone $dateDebut)->add(new \DateInterval('P' . $dureeRegime . 'W'));
            $durationRegime = $dateDebut->diff($dateFin)->days;
        }

        $dateDebutEvolution = null;
        if ($regimeActif && !empty($regimeActif['date_debut'])) {
            $dateDebutEvolution = new DateTime($regimeActif['date_debut']);
        } elseif ($premierPoids && !empty($premierPoids['date_save'])) {
            $dateDebutEvolution = new DateTime($premierPoids['date_save']);
        }

        if ($dateDebutEvolution && $poidsActuel && !empty($poidsActuel['date_save'])) {
            $dateDernierPoids = new DateTime($poidsActuel['date_save']);
            $joursEcoules = $dateDebutEvolution->diff($dateDernierPoids)->days;
        }

        return [
            'poids_actuel' => round($poidsActuelValue, 2),
            'poids_initial' => round($poidsInitial, 2),
            'premier_poids' => round($premierPoidsValue, 2),
            'variation_poids' => round($variationPoids, 2),
            'jours_ecoules' => $joursEcoules,
            'progression_kg' => round($progression, 2),
            'progression_pourcent' => round($progressionPourcent, 2),
            'regime_actif' => $regimeActifName,
            'duree_regime_jours' => $durationRegime,
            'user_name' => $user['prenom'] . ' ' . $user['nom'],
            'nom' => $user['nom'] ?? '',
            'prenom' => $user['prenom'] ?? '',
            'username' => $user['username'] ?? '',
            'email' => $user['email'] ?? '',
            'objectif_choisi' => $user['objectif_choisi'] ?? '',
            'regime_choisi' => $user['regime_choisi'] ?? '',
            'sport_choisi' => $user['sport_choisi'] ?? '',
            'age' => $user['age'],
            'taille' => $user['taille'],
            'genre' => $user['genre']
        ];
    }

    public function getHistoriquePoids($userId, $limitJours = 90)
    {
        $dateDebut = date('Y-m-d', strtotime("-{$limitJours} days"));

        $poids = $this->poidsUserModel
            ->where('user_id', $userId)
            ->where('date_save >=', $dateDebut)
            ->orderBy('date_save', 'ASC')
            ->findAll();

        $data = [
            'labels' => [],
            'poids' => []
        ];

        foreach ($poids as $p) {
            $data['labels'][] = date('d/m', strtotime($p['date_save']));
            $data['poids'][] = (float) $p['poids'];
        }

        return $data;
    }


    public function getDonneesSimulation($userId)
    {
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            return null;
        }

        $poidsActuel = $this->poidsUserModel
            ->where('user_id', $userId)
            ->orderBy('date_save', 'DESC')
            ->first();
        $poidsActuelValue = $poidsActuel ? $poidsActuel['poids'] : $user['poids_initial'];

        $derniereRegime = $this->userRegimeModel
            ->where('user_id', $userId)
            ->orderBy('date_save', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        if (!$derniereRegime) {
            return [
                'labels' => [],
                'datasets' => []
            ];
        }

        $poidsInitial = (float) ($user['poids_initial'] ?? $poidsActuelValue);
        $regime = $this->regimeModel->find($derniereRegime['regime_id']);
        $poidsCible = $this->calculerPoidsCible($user);

        // Récupérer l'historique des poids sans filtrage sur le régime
        $historique = $this->getHistoriquePoids($userId, 365);

        if (!empty($historique['labels'])) {
            $labels = $historique['labels'];
            $poidsReelData = $historique['poids'];
        } else {
            $labels = ['Jour 0'];
            $poidsReelData = [round($poidsInitial, 2)];
        }

        $simulation = $this->calculerSimulationPoidsSurTimeline(
            $poidsInitial,
            $poidsCible,
            count($labels)
        );

        $simulationData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Poids réel',
                    'data' => $poidsReelData,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.1)',
                    'borderWidth' => 2,
                    'pointRadius' => 3,
                    'tension' => 0.35,
                    'fill' => false
                ]
            ]
        ];

        if ($regime) {
            $poidsCibleLabel = 'Poids cible: ' . number_format($poidsCible, 2, '.', ' ') . ' kg';

            $simulationData['datasets'][] = [
                'label' => 'Simulation: ' . $regime['nom'],
                'data' => $simulation,
                'borderColor' => 'rgba(245, 166, 35, 1)',
                'borderWidth' => 2,
                'borderDash' => [5, 5],
                'pointRadius' => 2,
                'tension' => 0.35,
                'fill' => false
            ];

            $simulationData['datasets'][] = [
                'label' => $poidsCibleLabel,
                'data' => array_fill(0, count($labels), $poidsCible),
                'borderColor' => 'rgba(220, 53, 69, 1)',
                'borderWidth' => 2,
                'borderDash' => [8, 6],
                'pointRadius' => 0,
                'fill' => false
            ];
        }

        return $simulationData;
    }


    private function calculerPoidsCible(array $user): float
    {
        try {
            return round($this->regimeService->calculPoidsIdeal((float) ($user['taille'] ?? 0)), 2);
        } catch (\Throwable $e) {
            return (float) ($user['poids_initial'] ?? 0);
        }
    }


    private function genererTimelineDates(DateTime $dateDebut, DateTime $dateFin, int $pasJours = 7): array
    {
        $dates = [];
        $courant = clone $dateDebut;
        $pas = new \DateInterval('P' . max(1, $pasJours) . 'D');

        while ($courant <= $dateFin) {
            $dates[] = clone $courant;
            $courant->add($pas);
        }

        $dernierIndex = count($dates) - 1;
        if ($dernierIndex < 0 || $dates[$dernierIndex]->format('Y-m-d') !== $dateFin->format('Y-m-d')) {
            $dates[] = clone $dateFin;
        }

        return $dates;
    }


    private function construireSerieReelleSurTimeline($userId, DateTime $dateDebut, DateTime $dateFin, array $timelineDates, float $poidsInitial): array
    {
        $poids = $this->poidsUserModel
            ->where('user_id', $userId)
            ->where('date_save >=', $dateDebut->format('Y-m-d H:i:s'))
            ->where('date_save <=', $dateFin->format('Y-m-d H:i:s'))
            ->orderBy('date_save', 'ASC')
            ->findAll();

        $points = [];
        foreach ($poids as $row) {
            $points[] = [
                'date' => new DateTime($row['date_save']),
                'poids' => (float) $row['poids']
            ];
        }

        if (empty($points)) {
            return array_fill(0, count($timelineDates), round($poidsInitial, 2));
        }

        $resultat = [];
        $nbPoints = count($points);

        foreach ($timelineDates as $index => $dateCourante) {
            if ($index === 0) {
                $resultat[] = round($poidsInitial, 2);
                continue;
            }

            $timestampCourant = $dateCourante->getTimestamp();
            $precedent = null;
            $suivant = null;

            foreach ($points as $point) {
                $timestampPoint = $point['date']->getTimestamp();
                if ($timestampPoint <= $timestampCourant) {
                    $precedent = $point;
                }
                if ($timestampPoint >= $timestampCourant) {
                    $suivant = $point;
                    break;
                }
            }

            if ($precedent === null) {
                $resultat[] = round($points[0]['poids'], 2);
                continue;
            }

            if ($suivant === null || $precedent['date']->getTimestamp() === $suivant['date']->getTimestamp()) {
                $resultat[] = round($precedent['poids'], 2);
                continue;
            }

            $debut = $precedent['date']->getTimestamp();
            $fin = $suivant['date']->getTimestamp();
            $ratio = ($timestampCourant - $debut) / max(1, $fin - $debut);
            $valeur = $precedent['poids'] + (($suivant['poids'] - $precedent['poids']) * $ratio);

            $resultat[] = round($valeur, 2);
        }

        return $resultat;
    }


    private function calculerSimulationPoidsSurTimeline(float $poidsInitial, float $poidsCible, int $nbPoints): array
    {
        if ($nbPoints <= 0) {
            return [];
        }

        $simulation = [];
        
        for ($i = 0; $i < $nbPoints; $i++) {
            $ratio = $nbPoints > 1 ? ($i / ($nbPoints - 1)) : 0;
            $poidsSimule = $poidsInitial + (($poidsCible - $poidsInitial) * $ratio);
            $simulation[] = round($poidsSimule, 2);
        }

        return $simulation;
    }
}
