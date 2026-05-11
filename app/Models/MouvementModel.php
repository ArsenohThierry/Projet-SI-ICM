<?php

namespace App\Models;

use CodeIgniter\Model;

class MouvementModel extends Model
{
    protected $table = 'mouvement';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'type',
        'user_id',
        'montant',
        'date_mouvement'
    ];

    public function getBalanceByUserId(int $userId): float
    {
        $rows = $this->where('user_id', $userId)->findAll();
        $balance = 0.0;

        foreach ($rows as $row) {
            $amount = (float) ($row['montant'] ?? 0);
            $type = (string) ($row['type'] ?? '');

            if ($type === 'mampiditra') {
                $balance += $amount;
            } elseif ($type === 'mamoaka') {
                $balance -= $amount;
            }
        }

        return $balance;
    }
}
