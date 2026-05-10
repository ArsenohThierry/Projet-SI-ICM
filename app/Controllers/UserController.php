<?php

namespace App\Controllers;

use App\Models\ObjectifModel;
use App\Models\OptionModel;
use App\Models\UserModel;
use App\Models\UserObjectifModel;
use App\Models\UserOptionModel;
use App\Services\RegimeService;

class UserController extends BaseController
{
    public function imcForm(): string
    {
        return $this->getIMC();
    }

    public function getIMC()
    {
        $user = $this->getSessionUser();
        $data['user'] = $user;

        if (!$user) {
            return redirect()->to('/login');
        }

        $poids = (float) ($user['poids_initial'] ?? 0);
        $taille = (float) ($user['taille'] ?? 0);

        if ($poids > 0 && $taille > 0) {
            $taille = $taille / 100;
            $data['imc'] = $poids / ($taille * $taille);
        }

        return view('imc', $data);
    }

    public function IMCresult()
    {
        $poids = (float) $this->request->getPost('poids');
        $taille = (float) $this->request->getPost('taille');
        if ($poids <= 0 || $taille <= 0) {
            return redirect()->to('/imc')->with('error', 'Veuillez saisir des valeurs valides.');
        }

        $taille = $taille / 100;
        $imc = $poids / ($taille * $taille);

        $data['imc'] = $imc;
        $data['user'] = $this->getSessionUser();
        return view('imc', $data);
    }

    public function objectifUser()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $objectifModel = new ObjectifModel();
        $session = session();

        $data['user'] = $user;
        $data['objectifs'] = $objectifModel->getObjectifs();
        $data['disableUnavailable'] = (bool) $session->getFlashdata('disable_unavailable');
        $data['availableObjectifIds'] = $session->getFlashdata('available_objectif_ids') ?? [];
        $data['errorMessage'] = $session->getFlashdata('objectif_validation_error');

        return view('objectifUser', $data);
    }

    public function setObjectif()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $id = (int) $this->request->getPost('objectif_id');
        // objectif_applique_id can be computed server-side based on chosen objectif and IMC
        $idObjectifApplique = (int) $this->request->getPost('objectif_applique_id');

        
        if ($id <= 0) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un objectif.');
        }

        $objectifModel = new ObjectifModel();
        $objectif = $objectifModel->find($id);
        if (!$objectif) {
            return redirect()->back()->with('error', 'Objectif invalide.');
        }

        $regimeService = new RegimeService();
        try {
            $imc = $regimeService->calculIMC((float) ($user['poids_initial'] ?? 0), (float) ($user['taille'] ?? 0));
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/imc')->with('error', 'Veuillez d\'abord renseigner votre poids et votre taille pour choisir un objectif.');
        }

        try {
            $regimeService->validerObjectif((int) $objectif['id'], $imc);
        } catch (\InvalidArgumentException $e) {
            $objectifsDisponibles = $regimeService->getObjectifsDisponible($imc);
            $availableObjectifIds = array_values(array_filter(array_map(
                static fn($item) => (int) ($item['id'] ?? 0),
                $objectifsDisponibles
            )));

            return redirect()->to('/objectif')
                ->with('disable_unavailable', true)
                ->with('available_objectif_ids', $availableObjectifIds)
                ->with('objectif_validation_error', "Cet objectif ne correspond pas avec votre IMC. Pour votre bien-être, merci de choisir parmi les objectifs disponibles.");
        }

        $chosenId = (int) $objectif['id'];
        $appliqueId = $chosenId;
        if ($chosenId === 3) {
            if ($imc < 18.5) {
                $appliqueId = 2; // prise de poids
            } elseif ($imc >= 25) {
                $appliqueId = 1; // perte de poids
            } else {
                $appliqueId = 3; // imc ideal
            }
        }

        if ($idObjectifApplique > 0) {
            $appliqueId = $idObjectifApplique;
        }

        $userId = (int) session()->get('user_id');
        $userObjectifModel = new UserObjectifModel();
        $saved = $userObjectifModel->assignObjectifToUser($userId, $chosenId, $appliqueId);

        if (!$saved) {
            return redirect()->back()->with('error', 'Impossible d’enregistrer l’objectif.');
        }

        session()->set('user_objectif', $chosenId);
        session()->set('user_objectif_applique', $appliqueId);
        session()->setFlashdata('success', 'Objectif enregistré.');

        return redirect()->to('/regime');
    }

    public function userProfile()
    {
        $usermodel = new UserModel();
        $id = (int) session()->get('user_id');

        $user = $usermodel->getUserWithMenuSelections($id);
        if (!$user) {
            session()->destroy();
            return redirect()->to('/login');
        }

        $data['user'] = $user;
        return view('profil-user', $data);
    }

    public function programme()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $userId = (int) ($user['id'] ?? 0);
        $db = \Config\Database::connect();

        $objectif = $db->table('user_objectif')
            ->select('objectif.*, user_objectif.date_save')
            ->join('objectif', 'objectif.id = user_objectif.objectif_id')
            ->where('user_objectif.user_id', $userId)
            ->orderBy('user_objectif.date_save', 'DESC')
            ->orderBy('user_objectif.id', 'DESC')
            ->get()
            ->getRowArray();

        $regime = $db->table('user_regime')
            ->select('regime.*, user_regime.date_save, user_regime.date_debut, user_regime.duree')
            ->join('regime', 'regime.id = user_regime.regime_id')
            ->where('user_regime.user_id', $userId)
            ->orderBy('user_regime.date_save', 'DESC')
            ->orderBy('user_regime.id', 'DESC')
            ->get()
            ->getRowArray();

        $sport = $db->table('user_sport')
            ->select('sport.nom, sport_objectif.calories_brulees, sport_objectif.duree_recommandee, user_sport.date_save, user_sport.date_debut, objectif.libelle AS objectif_libelle')
            ->join('sport_objectif', 'sport_objectif.id = user_sport.sport_objectif_id')
            ->join('sport', 'sport.id = sport_objectif.sport_id')
            ->join('objectif', 'objectif.id = sport_objectif.objectif_id')
            ->where('user_sport.user_id', $userId)
            ->orderBy('user_sport.date_save', 'DESC')
            ->orderBy('user_sport.id', 'DESC')
            ->get()
            ->getRowArray();

        $regimeService = new RegimeService();
        $poids_actuel = $db->table('poids_user')
            ->select('poids')
            ->where('user_id', $userId)
            ->orderBy('date_save', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $poids_initial = $db->table('poids_user')
            ->select('poids')
            ->where('user_id', $userId)
            ->orderBy('date_save', 'ASC')
            ->get()
            ->getRowArray();

        $imc_actuel = $regimeService->calculIMC((float) ($poids_actuel['poids'] ?? 0), (float) ($user['taille'] ?? 0));

        $poids_cible = $regimeService->calculPoidsIdeal(
            (float) ($user['taille'] ?? 0),
        );

        $progression = round((($poids_initial['poids'] - $poids_actuel['poids']) / ($poids_initial['poids'] - $poids_cible)) * 100, 2);

        return view('programme', [
            'user' => $user,
            'objectif' => $objectif,
            'regime' => $regime,
            'sport' => $sport,
            'imc_actuel' => $imc_actuel,
            'poids_actuel' => $poids_actuel['poids'],
            'poids_cible' => $poids_cible,
            'progression' => $progression
        ]);
    }

    public function exportPdf()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $objectif   = null;
        $regime     = null;
        $sport      = null;
        $sportCal   = 0;
        $sportDuree = 0;

        $objectifId = (int) session()->get('user_objectif');
        if ($objectifId > 0) {
            $objectif = (new \App\Models\ObjectifModel())->find($objectifId);
        }

        $userRegimeModel = new \App\Models\UserRegimeModel();
        $lastRegime = $userRegimeModel->where('user_id', (int) $user['id'])
            ->orderBy('date_save', 'DESC')->first();
        if ($lastRegime) {
            $regime    = (new \App\Models\RegimeModel())->find((int) $lastRegime['regime_id']);
            $dateDebut = $lastRegime['date_debut'] ?? date('Y-m-d');
            $duree     = (int) ($lastRegime['duree'] ?? 0);
        } else {
            $dateDebut = session()->get('date_debut_regime') ?? date('Y-m-d');
            $duree     = 0;
        }

        $userSportModel = new \App\Models\UserSportModel();
        $lastSport = $userSportModel->where('user_id', (int) $user['id'])
            ->orderBy('date_save', 'DESC')->first();
        if ($lastSport) {
            $sportObj = (new \App\Models\SportObjectifModel())->find((int) $lastSport['sport_objectif_id']);
            if ($sportObj) {
                $sport      = (new \App\Models\SportModel())->find((int) $sportObj['sport_id']);
                $sportCal   = (float) ($sportObj['calories_brulees'] ?? 0);
                $sportDuree = (int)   ($sportObj['duree_recommandee'] ?? 0);
            }
        }

        try {
            $start = new \DateTime($dateDebut);
        } catch (\Exception $e) {
            $start = new \DateTime();
        }

        $dureeEffective = $duree > 0 ? $duree : 4;
        $end          = (clone $start)->add(new \DateInterval('P' . $dureeEffective . 'W'));
        $days         = $start->diff($end)->days;
        $variation    = (float) ($regime['variation_poids'] ?? 0);
        $objectifGain = $variation * $dureeEffective;

        require_once __DIR__ . '/../../fpdf186/rounded_rect2.php';

        $t = static function ($v) {
            return mb_convert_encoding((string) $v, 'ISO-8859-1', 'UTF-8');
        };

        $fullName  = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
        $fullName  = $fullName !== '' ? $fullName : ($user['username'] ?? 'Utilisateur');
        $tailleM   = ((float) ($user['taille'] ?? 0)) / 100;
        $imc       = $tailleM > 0 ? ((float) ($user['poids_initial'] ?? 0)) / ($tailleM ** 2) : 0;

        $imcTxt = 'Normal';
        if ($imc < 18.5) $imcTxt = 'Insuffisance pondérale';
        elseif ($imc >= 25 && $imc < 30) $imcTxt = 'Surpoids';
        elseif ($imc >= 30) $imcTxt = 'Obésité';

        $green      = [24, 201, 122];    
        $greenDark  = [15, 165,  99];    
        $greenLight = [232, 250, 243];   
        $white      = [255, 255, 255];
        $gray50     = [244, 250, 247];   
        $gray100    = [227, 240, 233];   
        $gray200    = [217, 237, 229];   
        $gray400    = [139, 169, 154];  
        $gray600    = [74,  98,  85];    
        $gray900    = [13,  31,  20];    

        $pdf = new class('P', 'mm', 'A4') extends \PDF {
            public function Circle($x, $y, $r, $style = 'D')
            {
                $this->Ellipse($x, $y, $r, $r, $style);
            }

            public function Ellipse($x, $y, $rx, $ry, $style = 'D')
            {
                if ($style === 'F') {
                    $op = 'f';
                } elseif ($style === 'FD' || $style === 'DF') {
                    $op = 'B';
                } else {
                    $op = 'S';
                }

                $lx = 4 / 3 * (M_SQRT2 - 1) * $rx;
                $ly = 4 / 3 * (M_SQRT2 - 1) * $ry;
                $k = $this->k;
                $h = $this->h;

                $this->_out(sprintf(
                    '%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
                    ($x + $rx) * $k,
                    ($h - $y) * $k,
                    ($x + $rx) * $k,
                    ($h - ($y - $ly)) * $k,
                    ($x + $lx) * $k,
                    ($h - ($y - $ry)) * $k,
                    $x * $k,
                    ($h - ($y - $ry)) * $k
                ));
                $this->_out(sprintf(
                    '%.2F %.2F %.2F %.2F %.2F %.2F c',
                    ($x - $lx) * $k,
                    ($h - ($y - $ry)) * $k,
                    ($x - $rx) * $k,
                    ($h - ($y - $ly)) * $k,
                    ($x - $rx) * $k,
                    ($h - $y) * $k
                ));
                $this->_out(sprintf(
                    '%.2F %.2F %.2F %.2F %.2F %.2F c',
                    ($x - $rx) * $k,
                    ($h - ($y + $ly)) * $k,
                    ($x - $lx) * $k,
                    ($h - ($y + $ry)) * $k,
                    $x * $k,
                    ($h - ($y + $ry)) * $k
                ));
                $this->_out(sprintf(
                    '%.2F %.2F %.2F %.2F %.2F %.2F c %s',
                    ($x + $lx) * $k,
                    ($h - ($y + $ry)) * $k,
                    ($x + $rx) * $k,
                    ($h - ($y + $ly)) * $k,
                    ($x + $rx) * $k,
                    ($h - $y) * $k,
                    $op
                ));
            }
        };

        $pdf->SetTitle('Bilan Personnel NutriFit');
        $pdf->SetAuthor('NutriFit');
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->AddPage();

        $W  = 210;
        $M  = 16;          
        $CW = $W - $M * 2; // Largeur utile de 178mm

        $pdf->SetFillColor(...$gray50);
        $pdf->Rect(0, 0, $W, 297, 'F');


        $pdf->SetFillColor(...$white);
        $pdf->SetDrawColor(...$gray200);
        $pdf->SetLineWidth(0.2);
        $pdf->Rect(0, 0, $W, 22, 'DF');

        $logoPath = __DIR__ . '/../../public/assets/logo_pdf.png';
        if (is_file($logoPath)) {
            $pdf->Image($logoPath, $M + 1, -3.0, 55, 30);
        } else {
            $pdf->SetFillColor(...$green);
            $pdf->RoundedRect($M + 1, 6, 10, 10, 3, '1234', 'F');
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetTextColor(...$white);
            $pdf->SetXY($M + 1, 7.2);
            $pdf->Cell(10, 6, $t('N'), 0, 0, 'C');
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(...$gray600);
        $pdf->SetXY($W - $M - 80, 8);

        $profileY = 32;

        $pdf->SetFillColor(...$white);
        $pdf->SetDrawColor(...$gray200);
        $pdf->RoundedRect($M, $profileY, $CW, 46, 3, '1234', 'DF');

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(...$gray900);
        $pdf->SetXY($M + 8, $profileY + 6);
        $pdf->Cell(100, 6, $t($fullName), 0, 1);

        $pdf->SetFont('Arial', '', 8.5);
        $pdf->SetTextColor(...$gray600);
        $pdf->SetX($M + 8);
        $pdf->Cell(100, 5, $t('ID Membre : #' . ($user['id'] ?? 'N/A') . '  |  Contact : ' . ($user['email'] ?? 'Non renseigné')), 0, 1);

        $pdf->Line($M + 8, $profileY + 19, $M + $CW - 8, $profileY + 19);

        $characs = [
            ['Poids initial', number_format((float)($user['poids_initial'] ?? 0), 1) . ' kg'],
            ['Taille',        number_format((float)($user['taille'] ?? 0), 0) . ' cm'],
            ['IMC calculé',    number_format($imc, 1) . ' (' . $imcTxt . ')'],
            ['Genre',         ucfirst(strtolower($user['genre'] ?? 'Non renseigné'))]
        ];

        $cx = $M + 8;
        $cw = ($CW - 16) / 4;
        foreach ($characs as [$label, $val]) {
            $pdf->SetFont('Arial', '', 7.5);
            $pdf->SetTextColor(...$gray400);
            $pdf->SetXY($cx, $profileY + 23);
            $pdf->Cell($cw, 4, $t(strtoupper($label)), 0, 0, 'L');

            $pdf->SetFont('Arial', 'B', 9.5);
            $pdf->SetTextColor(...$gray900);
            $pdf->SetXY($cx, $profileY + 27);
            $pdf->Cell($cw, 5, $t($val), 0, 0, 'L');

            $cx += $cw;
        }

        $pdf->SetFillColor(...$greenLight);
        $pdf->RoundedRect($M + 8, $profileY + 36, $CW - 16, 6, 1, '1234', 'F');

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetTextColor(...$greenDark);
        $pdf->SetXY($M + 12, $profileY + 36.5);
        $pdf->Cell($CW - 24, 5, $t('OBJECTIF PRINCIPAL : ' . ($objectif['libelle'] ?? 'Non défini')), 0, 0, 'L');

        $colY = $profileY + 54;
        $colW = ($CW - 6) / 2; // 86mm de large par colonne

        $pdf->SetFillColor(...$white);
        $pdf->SetDrawColor(...$gray200);
        $pdf->RoundedRect($M, $colY, $colW, 82, 3, '1234', 'DF');

        $pdf->SetFillColor(...$greenLight);
        $pdf->RoundedRect($M, $colY, $colW, 8, 3, '12', 'F');
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->SetTextColor(...$greenDark);
        $pdf->SetXY($M + 6, $colY + 1.5);
        $pdf->Cell($colW - 12, 5, $t('RÉGIME SELECTIONNÉ'), 0, 0, 'L');

        if ($regime) {
            $pdf->SetFont('Arial', 'B', 10.5);
            $pdf->SetTextColor(...$gray900);
            $pdf->SetXY($M + 6, $colY + 13);
            $pdf->MultiCell($colW - 12, 4.5, $t($regime['nom'] ?? 'Régime'), 0, 'L');

            $macros = [
                ['Viande',   (float)($regime['pourcentage_viande']   ?? 0), $greenDark],
                ['Volaille', (float)($regime['pourcentage_volaille'] ?? 0), $gray900],
                ['Poisson',  (float)($regime['pourcentage_poisson']  ?? 0), $gray600],
            ];

            $my = $colY + 28;
            foreach ($macros as [$ml, $mp, $mc]) {
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetTextColor(...$gray600);
                $pdf->SetXY($M + 6, $my);
                $pdf->Cell(40, 4, $t($ml), 0, 0);

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetTextColor(...$mc);
                $pdf->SetXY($M + $colW - 16, $my);
                $pdf->Cell(10, 4, $t(number_format($mp, 0) . '%'), 0, 0, 'R');

                $pdf->SetFillColor(...$gray100);
                $pdf->RoundedRect($M + 6, $my + 4.5, $colW - 12, 1.8, 0.9, '1234', 'F');
                if ($mp > 0) {
                    $pdf->SetFillColor(...$mc);
                    $pdf->RoundedRect($M + 6, $my + 4.5, ($colW - 12) * ($mp / 100), 1.8, 0.9, '1234', 'F');
                }
                $my += 12;
            }

            $pdf->SetFillColor(...$gray50);
            $pdf->RoundedRect($M + 6, $colY + 70, $colW - 12, 7, 1, '1234', 'F');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetTextColor(...$gray900);
            $pdf->SetXY($M + 6, $colY + 71);
            $pdf->Cell($colW - 12, 5, $t('Coût estimé : ' . number_format((float)($regime['montant'] ?? 0), 2) . ' Ar / jour'), 0, 0, 'C');
        } else {
            $pdf->SetFont('Arial', 'I', 8.5);
            $pdf->SetTextColor(...$gray400);
            $pdf->SetXY($M + 6, $colY + 36);
            $pdf->Cell($colW - 12, 5, $t('Aucun régime sélectionné actuellement.'), 0, 0, 'C');
        }

        $colX2 = $M + $colW + 6;
        $pdf->SetFillColor(...$white);
        $pdf->SetDrawColor(...$gray200);
        $pdf->RoundedRect($colX2, $colY, $colW, 82, 3, '1234', 'DF');

        $pdf->SetFillColor(...$greenLight);
        $pdf->RoundedRect($colX2, $colY, $colW, 8, 3, '12', 'F');
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->SetTextColor(...$greenDark);
        $pdf->SetXY($colX2 + 6, $colY + 1.5);
        $pdf->Cell($colW - 12, 5, $t('PROGRAMME SPORTIF'), 0, 0, 'L');

        if ($sport) {
            $pdf->SetFont('Arial', 'B', 10.5);
            $pdf->SetTextColor(...$gray900);
            $pdf->SetXY($colX2 + 6, $colY + 13);
            $pdf->MultiCell($colW - 12, 4.5, $t($sport['nom'] ?? 'Sport'), 0, 'L');

            $totalCal  = $sportCal * $sportDuree;
            $sportRows = [
                ['Dépense théorique',  number_format($sportCal, 1) . ' kcal / min'],
                ['Durée recommandée',  $sportDuree . ' minutes'],
                ['Total par séance',   number_format($totalCal, 0) . ' kcal brûlées'],
            ];

            $sry = $colY + 28;
            foreach ($sportRows as [$sl, $sv]) {
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetTextColor(...$gray600);
                $pdf->SetXY($colX2 + 6, $sry);
                $pdf->Cell($colW - 12, 4, $t($sl), 0, 1);

                $pdf->SetFont('Arial', 'B', 8.5);
                $pdf->SetTextColor(...$gray900);
                $pdf->SetX($colX2 + 6);
                $pdf->Cell($colW - 12, 5, $t($sv), 0, 1);

                $pdf->SetDrawColor(...$gray100);
                $pdf->Line($colX2 + 6, $sry + 10, $colX2 + $colW - 6, $sry + 10);
                $sry += 13;
            }

            $pdf->SetFillColor(...$gray50);
            $pdf->RoundedRect($colX2 + 6, $colY + 70, $colW - 12, 7, 1, '1234', 'F');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetTextColor(...$gray600);
            $pdf->SetXY($colX2 + 6, $colY + 71);
            $pdf->Cell($colW - 12, 5, $t('Fréquence : À pratiquer régulièrement'), 0, 0, 'C');
        } else {
            $pdf->SetFont('Arial', 'I', 8.5);
            $pdf->SetTextColor(...$gray400);
            $pdf->SetXY($colX2 + 6, $colY + 36);
            $pdf->Cell($colW - 12, 5, $t('Aucune activité physique programmée.'), 0, 0, 'C');
        }


        $sumY = $colY + 88;

        $pdf->SetFillColor(...$white);
        $pdf->SetDrawColor(...$gray200);
        $pdf->RoundedRect($M, $sumY, $CW, 40, 3, '1234', 'DF');

        $pdf->SetFillColor(...$greenLight);
        $pdf->RoundedRect($M, $sumY, $CW, 8, 3, '12', 'F');
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->SetTextColor(...$greenDark);
        $pdf->SetXY($M + 6, $sumY + 1.5);
        $pdf->Cell($CW - 12, 5, $t('PLANIFICATION DU PROGRAMME'), 0, 0, 'L');

        $tileW = ($CW - 12) / 4;
        $tiles = [
            ['Date de début',    $start->format('d/m/Y'),   $gray900],
            ['Fin estimée',     $end->format('d/m/Y'),     $gray900],
            ['Durée totale',    $days . ' jours',          $gray900],
            ['Variation visée', (($objectifGain >= 0) ? '+' : '') . number_format($objectifGain, 2) . ' kg', $greenDark],
        ];

        $tx = $M + 6;
        foreach ($tiles as $i => [$label, $val, $textCol]) {
            $pdf->SetFont('Arial', '', 7.5);
            $pdf->SetTextColor(...$gray400);
            $pdf->SetXY($tx, $sumY + 15);
            $pdf->Cell($tileW, 4, $t(strtoupper($label)), 0, 0, 'L');

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(...$textCol);
            $pdf->SetXY($tx, $sumY + 20);
            $pdf->Cell($tileW, 6, $t($val), 0, 0, 'L');

            if ($i < 3) {
                $pdf->SetDrawColor(...$gray100);
                $pdf->Line($tx + $tileW - 2, $sumY + 15, $tx + $tileW - 2, $sumY + 32);
            }

            $tx += $tileW;
        }

        $pdf->SetFont('Arial', 'I', 7.5);
        $pdf->SetTextColor(...$gray600);
        $pdf->SetXY($M + 6, $sumY + 31);
        $pdf->Cell($CW - 12, 5, $t('Ces estimations dépendent du respect de votre calendrier nutritionnel et de votre métabolisme.'), 0, 0, 'L');


        $pdf->SetDrawColor(...$gray200);
        $pdf->SetLineWidth(0.2);
        $pdf->Line($M, 276, $M + $CW, 276);

        $pdf->SetFont('Arial', '', 7.5);
        $pdf->SetTextColor(...$gray400);
        $pdf->SetXY($M, 278);
        $pdf->Cell($CW, 4, $t('Document généré automatiquement par NutriFit. Les données présentées proviennent de votre tableau de bord personnel.'), 0, 1, 'L');
        $pdf->SetX($M);
        $pdf->Cell($CW, 4, $t('Edité le ' . date('d/m/Y à H:i')), 0, 0, 'L');

        $filename = 'plan_' . preg_replace('/[^a-z0-9_-]/i', '_', ($user['username'] ?? 'user')) . '.pdf';
        return $pdf->Output('D', $filename);
    }

    public function pageAbonnement()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $optionModel = new OptionModel();
        $mouvementModel = new \App\Models\MouvementModel();

        return view('abonnement', [
            'user' => $user,
            'options' => $optionModel->getAllOptions(),
            'balance' => $mouvementModel->getBalanceByUserId((int) $user['id']),
        ]);
    }

    public function apiBalance()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $mouvementModel = new \App\Models\MouvementModel();
        $balance = $mouvementModel->getBalanceByUserId((int) $user['id']);

        return $this->response->setJSON(['balance' => (float) $balance]);
    }

    public function upgradeToGold()
    {
        $userId = (int) session()->get('user_id');
        $optionModel = new OptionModel();
        $mouvementModel = new \App\Models\MouvementModel();
        $goldLabel = 'gold';
        $goldDefaultAmount = 20.0;
        
        // Récupérer l'option GOLD
        $gold = $optionModel->where('libelle', $goldLabel)->first();
        if (!$gold) {
            $goldId = (int) $optionModel->insert([
                'libelle' => $goldLabel,
                'montant' => $goldDefaultAmount,
            ], true);
            $goldMontant = $goldDefaultAmount;
        } else {
            $goldId = (int) $gold['id'];
            $goldMontant = (float) ($gold['montant'] ?? 0);
            if ($goldMontant <= 0) {
                $goldMontant = $goldDefaultAmount;
                $optionModel->update($goldId, ['montant' => $goldMontant]);
            }
        }

        // Vérifier la balance
        $balance = $mouvementModel->getBalanceByUserId($userId);
        if ($balance < $goldMontant) {
            return redirect()->to('/codes/redeem-register')
                ->with('error', 'Solde insuffisant pour s\'abonner à GOLD. Veuillez créditer votre compte.');
        }

        // Assigner l'option GOLD à l'utilisateur
        (new UserModel())->assignOptionToUser($userId, $goldId);

        // Déduire le montant de la balance
        if ($goldMontant > 0) {
            $mouvementModel->insert([
                'type' => 'mamoaka',
                'user_id' => $userId,
                'montant' => $goldMontant,
                'date_mouvement' => date('Y-m-d H:i:s'),
            ]);
        }

        session()->set('user_option', 'gold');

        return redirect()->to('/regime')->with('success', 'Abonnement GOLD activé avec succès !');
    }

    public function regimeSelection()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $objectifId = (int) (session()->get('user_objectif_applique') ?: session()->get('user_objectif'));
        if ($objectifId <= 0) {
            return redirect()->to('/objectif')->with('error', 'Veuillez d\'abord choisir un objectif.');
        }

        $regimeService = new RegimeService();
        try {
            $imc = $regimeService->calculIMC((float) ($user['poids_initial'] ?? 0), (float) ($user['taille'] ?? 0));
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/imc')->with('error', 'Veuillez renseigner votre poids et votre taille.');
        }

        $regimes = $regimeService->getSuggestionsRegime($imc, $objectifId);

        $data['user'] = $user;
        $data['regimes'] = is_string($regimes) ? [] : $regimes;
        $data['errorMessage'] = is_string($regimes) ? $regimes : null;
        $data['isGold'] = session()->get('user_option') === 'gold';

        return view('suggestion_regime', $data);
    }

    public function setRegime()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $regimeId = (int) $this->request->getPost('regime_id');
        if ($regimeId <= 0) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un régime.');
        }

        $dateDebutStr = trim((string) $this->request->getPost('date_debut'));
        if (!$dateDebutStr) {
            return redirect()->back()->with('error', 'Veuillez indiquer une date de début.');
        }

        try {
            $dateDebut = new \DateTime($dateDebutStr);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Date de début invalide.');
        }

        $objectifId = (int) session()->get('user_objectif');
        $objectifAppliqueId = (int) (session()->get('user_objectif_applique') ?: $objectifId);

        if ($objectifId <= 0) {
            return redirect()->to('/objectif')->with('error', 'Session expirée, veuillez rechoisir votre objectif.');
        }

        $regimeService = new RegimeService();
        $regime = (new \App\Models\RegimeModel())->find($regimeId);

        if (!$regime || (int) ($regime['objectif_id'] ?? 0) !== $objectifAppliqueId) {
            return redirect()->back()->with('error', 'Régime invalide pour cet objectif.');
        }

        $userId = (int) session()->get('user_id');
        $poidsIdeal = $regimeService->calculPoidsIdeal((float) ($user['taille'] ?? 0));
        $dureeRegime = (int) ceil($regimeService->calculDureeRegime(
            (float) ($user['poids_initial'] ?? 0),
            $poidsIdeal,
            (float) ($regime['variation_poids'] ?? 0)
        ));

        if ($dureeRegime <= 0) {
            $dureeRegime = 30;
        }

        $regimeService->enregistrerChoixRegime(
            $userId,
            $regimeId,
            $dateDebut,
            $dureeRegime
        );

        session()->set('user_regime', $regimeId);
        session()->set('date_debut_regime', $dateDebut->format('Y-m-d'));
        session()->setFlashdata('success', 'Régime enregistré avec succès !');

        return redirect()->to('/sport');
    }

    public function sportSelection()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $objectifId = (int) (session()->get('user_objectif_applique') ?: session()->get('user_objectif'));
        if ($objectifId <= 0) {
            return redirect()->to('/objectif')->with('error', 'Veuillez d\'abord choisir un objectif.');
        }

        $dateDebutRegime = session()->get('date_debut_regime');
        if (!$dateDebutRegime) {
            return redirect()->to('/regime')->with('error', 'Veuillez d\'abord choisir un régime.');
        }

        $regimeService = new RegimeService();
        try {
            $imc = $regimeService->calculIMC((float) ($user['poids_initial'] ?? 0), (float) ($user['taille'] ?? 0));
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('/imc')->with('error', 'Veuillez renseigner votre poids et votre taille.');
        }

        $age = (int) ($user['age'] ?? 0);
        $genre = $user['genre'] ?? '';
        $sports = $regimeService->getSuggestionsSportObjectif($imc, $objectifId, $age, $genre);

        $data['user'] = $user;
        $data['sports'] = is_string($sports) ? [] : $sports;
        $data['errorMessage'] = is_string($sports) ? $sports : null;
        $data['date_debut_regime'] = $dateDebutRegime;

        return view('suggestion_sport', $data);
    }

    public function setSport()
    {
        $user = $this->getSessionUser();
        if (!$user) {
            return redirect()->to('/login');
        }

        $sportObjectifId = (int) $this->request->getPost('sport_objectif_id');
        if ($sportObjectifId <= 0) {
            return redirect()->back()->with('error', 'Veuillez sélectionner un sport.');
        }

        $objectifId = (int) (session()->get('user_objectif_applique') ?: session()->get('user_objectif'));
        if ($objectifId <= 0) {
            return redirect()->to('/objectif')->with('error', 'Session expirée, veuillez rechoisir votre objectif.');
        }

        $dateDebutStr = trim((string) session()->get('date_debut_regime'));
        if (!$dateDebutStr) {
            return redirect()->to('/regime')->with('error', 'Session expirée, veuillez rechoisir un régime.');
        }

        $regimeService = new RegimeService();
        $sportObjectif = (new \App\Models\SportObjectifModel())->find($sportObjectifId);

        if (!$sportObjectif || (int) ($sportObjectif['objectif_id'] ?? 0) !== $objectifId) {
            return redirect()->back()->with('error', 'Sport invalide pour cet objectif.');
        }

        $userId = (int) session()->get('user_id');

        try {
            $dateDebut = new \DateTime($dateDebutStr);
        } catch (\Exception $e) {
            return redirect()->to('/regime')->with('error', 'Date invalide, veuillez rechoisir un régime.');
        }

        $regimeService->enregistrerChoixSportObjectif(
            $userId,
            $sportObjectifId,
            $dateDebut
        );

        session()->set('user_sport_objectif', $sportObjectifId);
        session()->setFlashdata('success', 'Sport enregistré avec succès !');

        return redirect()->to('/profile');
    }

    public function getSuggestionsRegimeObjectif(float $imc, int $objectifId)
    {
        $regimeService = new RegimeService();
        $regimesObjectif = $regimeService->getSuggestionsRegime($imc, $objectifId);
        $data['regimes'] = $regimesObjectif;

        if (empty($regimesObjectif)) {
            return 'Aucun régime disponible pour cet objectif.';
        }

        return view('suggestions_regime', $data);
    }

    private function getSessionUser(): ?array
    {
        $id = (int) session()->get('user_id');
        if ($id <= 0) {
            return null;
        }

        $userModel = new UserModel();
        return $userModel->getUserWithMenuSelections($id);
    }
}
