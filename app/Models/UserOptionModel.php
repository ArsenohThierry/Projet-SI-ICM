<?php

namespace App\Models;

use CodeIgniter\Model;

class UserOptionModel extends Model
{
    protected $table = 'user_option';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'option_id',
        'date_save'
    ];

    public function getLatestPlanByUserId(int $userId): ?string
    {
        $row = $this->select('option.libelle')
            ->join('option', 'option.id = user_option.option_id', 'left')
            ->where('user_option.user_id', $userId)
            ->orderBy('user_option.date_save', 'DESC')
            ->first();

        return $row['libelle'] ?? null;
    }

    public function assignOptionToUser(int $userId, int $optionId): bool
    {
        return (bool) $this->insert([
            'user_id' => $userId,
            'option_id' => $optionId,
            'date_save' => date('Y-m-d H:i:s')
        ]);
    }
}
