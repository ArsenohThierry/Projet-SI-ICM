<?php

namespace App\Models;

use CodeIgniter\Model;

class SportModel extends Model
{
    protected $table = 'sport';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nom'
    ];

}