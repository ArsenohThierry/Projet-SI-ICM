<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\MouvementModel;

class CodeModel extends Model
{
    protected $table = 'code';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'valeur',
        'montant',
        'status'
    ];

    public function redeemForUser(string $codeValue, int $userId): array
    {
        $db = $this->db;
        $db->transStart();

        $code = $this->where('valeur', $codeValue)
            ->where('status', 'unused')
            ->first();

        if (!$code) {
            $db->transComplete();
            return ['ok' => false, 'message' => 'Code invalide ou deja utilise.'];
        }

        $this->update($code['id'], ['status' => 'used']);

        $mouvementModel = new MouvementModel();
        $mouvementModel->insert([
            'type' => 'mampiditra',
            'user_id' => $userId,
            'montant' => (float) $code['montant'],
            'date_mouvement' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();
        if ($db->transStatus() === false) {
            return ['ok' => false, 'message' => 'Erreur de traitement du code.'];
        }

        return ['ok' => true, 'montant' => (float) $code['montant']];
    }
}
