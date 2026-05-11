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
use App\Models\UserOptionModel;
use App\Models\MouvementModel;
use DateTime;

class DashboardAdminService
{
    protected $regimeModel;
    protected $userRegimeModel;
    protected $userSportModel;
    protected $poidsUserModel;
    protected $userModel;
    protected $regimeService;
    protected $objectifModel;
    protected $userOptionModel;
    protected $mouvementModel;
    protected $userObjectifModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
        $this->userRegimeModel = new UserRegimeModel();
        $this->userSportModel = new UserSportModel();
        $this->poidsUserModel = new PoidsUserModel();
        $this->userModel = new UserModel();
        $this->regimeService = new RegimeService();
        $this->objectifModel = new ObjectifModel();
        $this->userOptionModel = new UserOptionModel();
        $this->mouvementModel = new MouvementModel();
        $this->userObjectifModel = new UserObjectifModel();
    }

    public function getNombreUtilisateurs()
    {
        return $this->userModel->countAllResults();
    }

    public function getNombreUtilisateursNonAdmin(): int
    {
        return $this->userModel
            ->groupStart()
            ->where('role_user <>', 'admin')
            ->groupEnd()
            ->countAllResults();
    }

    public function getNombreUtilisateursGold()
    {
        return $this->userOptionModel->where('option_id', 2)->countAllResults();
    }

    public function getNombreUtilisateursGoldActifs(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM (
                SELECT uo.user_id, MAX(uo.id) AS max_id
                FROM user_option uo
                GROUP BY uo.user_id
            ) uo_last
            INNER JOIN user_option uo ON uo.id = uo_last.max_id
            INNER JOIN `option` o ON o.id = uo.option_id
            INNER JOIN user u ON u.id = uo.user_id
            WHERE o.libelle = 'gold'
              AND (u.role_user <> 'admin' OR u.role_user IS NULL)
        ";

        return (int) $this->userOptionModel
            ->db
            ->query($sql)
            ->getRow()
            ->total;
    }

    public function getRevenueTotal()
    {
        $revenus = 0.00;

        $mouvements = $this->mouvementModel->findAll();

        foreach ($mouvements as $mouvement) {
            if ($mouvement['type'] === 'mamoaka') {
                $revenus += floatval($mouvement['montant']);
            }
        }

        return $revenus;
    }

    public function getNombreRegimesActifs()
    {
        $sql = "
        SELECT COUNT(DISTINCT regime_id) AS total
        FROM user_regime
        WHERE id IN (
            SELECT MAX(id)
            FROM user_regime
            GROUP BY user_id
        )
    ";

        return $this->userRegimeModel
            ->db
            ->query($sql)
            ->getRow()
            ->total;
    }

    public function getNombreRegimeTotal()
    {
        return $this->regimeModel->countAllResults();
    }

    public function countUserByObjectif(int $objectifId)
    {
        $sql = "
        SELECT COUNT(DISTINCT user_id) AS total
        FROM user_objectif
        WHERE objectif_id = ?
          AND id IN (
              SELECT MAX(id)
              FROM user_objectif
              GROUP BY user_id
          )";

          $count = $this->userObjectifModel
              ->db
              ->query($sql, [$objectifId])
              ->getRow()
              ->total;
    }

    public function countNbrDeFoisRegimeChoisi(int $regimeId)
    {
        return $this->userRegimeModel
            ->where('regime_id', $regimeId)
            ->countAllResults();
    }

    public function getTableauCroiseRegimeObjectif(): array
    {
        $sql = "
            SELECT
                ur.regime_id AS regime_id,
                uo.objectif_id AS objectif_id,
                COUNT(DISTINCT ur.user_id) AS total
            FROM user_regime ur
            INNER JOIN (
                SELECT user_id, MAX(id) AS max_id
                FROM user_regime
                GROUP BY user_id
            ) ur_last ON ur.id = ur_last.max_id
            INNER JOIN user_objectif uo ON uo.user_id = ur.user_id
            INNER JOIN (
                SELECT user_id, MAX(id) AS max_id
                FROM user_objectif
                GROUP BY user_id
            ) uo_last ON uo.id = uo_last.max_id
            GROUP BY ur.regime_id, uo.objectif_id
            ORDER BY ur.regime_id, uo.objectif_id
        ";

        return $this->userRegimeModel
            ->db
            ->query($sql)
            ->getResultArray();
    }

    public function getObjectifsRepartition(): array
    {
        $sql = "
            SELECT o.libelle AS libelle, COUNT(*) AS total
            FROM (
                SELECT user_id, MAX(id) AS max_id
                FROM user_objectif
                GROUP BY user_id
            ) uo_last
            INNER JOIN user_objectif uo ON uo.id = uo_last.max_id
            INNER JOIN objectif o ON o.id = uo.objectif_id
            INNER JOIN user u ON u.id = uo.user_id
            WHERE u.role_user <> 'admin' OR u.role_user IS NULL
            GROUP BY o.libelle
            ORDER BY o.id
        ";

        $rows = $this->userObjectifModel
            ->db
            ->query($sql)
            ->getResultArray();

        $data = [];
        foreach ($rows as $row) {
            $data[$row['libelle']] = (int) $row['total'];
        }

        return $data;
    }

    public function getRegimesPopulaires(): array
    {
        $sql = "
            SELECT r.nom AS nom, COUNT(*) AS total
            FROM (
                SELECT user_id, MAX(id) AS max_id
                FROM user_regime
                GROUP BY user_id
            ) ur_last
            INNER JOIN user_regime ur ON ur.id = ur_last.max_id
            INNER JOIN regime r ON r.id = ur.regime_id
            INNER JOIN user u ON u.id = ur.user_id
            WHERE u.role_user <> 'admin' OR u.role_user IS NULL
            GROUP BY r.nom
            ORDER BY total DESC, r.nom ASC
        ";

        $rows = $this->userRegimeModel
            ->db
            ->query($sql)
            ->getResultArray();

        $data = [];
        foreach ($rows as $row) {
            $data[$row['nom']] = (int) $row['total'];
        }

        return $data;
    }

    public function getInscriptionsParMois(): array
    {
        $sql = "
            SELECT t.mois AS mois, COUNT(*) AS total
            FROM (
                SELECT u.id AS user_id, DATE_FORMAT(MIN(uo.date_save), '%Y-%m') AS mois
                FROM user u
                LEFT JOIN user_objectif uo ON uo.user_id = u.id
                WHERE u.role_user <> 'admin' OR u.role_user IS NULL
                GROUP BY u.id
            ) t
            WHERE t.mois IS NOT NULL
            GROUP BY t.mois
            ORDER BY t.mois
        ";

        $rows = $this->userObjectifModel
            ->db
            ->query($sql)
            ->getResultArray();

        $data = [];
        foreach ($rows as $row) {
            $data[$row['mois']] = (int) $row['total'];
        }

        return $data;
    }

    public function getUsersWithImcObjectifRegime(): array
    {
        $sql = "
            SELECT
                u.id AS user_id,
                u.nom AS nom,
                u.prenom AS prenom,
                u.username AS username,
                u.email AS email,
                u.poids_initial AS poids_initial,
                u.taille AS taille,
                ROUND(u.poids_initial / POW(u.taille / 100, 2), 2) AS imc,
                uo.objectif_id AS objectif_id,
                o.libelle AS objectif_libelle,
                ur.regime_id AS regime_id,
                r.nom AS regime_nom
            FROM user u
            LEFT JOIN (
                SELECT user_id, MAX(id) AS max_id
                FROM user_objectif
                GROUP BY user_id
            ) uo_last ON uo_last.user_id = u.id
            LEFT JOIN user_objectif uo ON uo.id = uo_last.max_id
            LEFT JOIN objectif o ON o.id = uo.objectif_id
            LEFT JOIN (
                SELECT user_id, MAX(id) AS max_id
                FROM user_regime
                GROUP BY user_id
            ) ur_last ON ur_last.user_id = u.id
            LEFT JOIN user_regime ur ON ur.id = ur_last.max_id
            LEFT JOIN regime r ON r.id = ur.regime_id
            WHERE u.role_user <> 'admin'
            ORDER BY u.id
        ";

        return $this->userModel
            ->db
            ->query($sql)
            ->getResultArray();
    }

    


}