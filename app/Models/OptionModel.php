<?php

namespace App\Models;

use CodeIgniter\Model;

class OptionModel extends Model
{
    protected $table = 'option';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'libelle',
        'montant'
    ];

    public function findByLabel(string $label): ?array
    {
        return $this->where('libelle', $label)->first();
    }

    public function createOption(string $label, float $amount): int
    {
        return (int) $this->insert([
            'libelle' => $label,
            'montant' => $amount,
        ], true);
    }
}
