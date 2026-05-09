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
}
