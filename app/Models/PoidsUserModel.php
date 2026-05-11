<?php

namespace App\Models;

use CodeIgniter\Model;

class PoidsUserModel extends Model
{
    protected $table = 'poids_user';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'poids',
        'date_save'
    ];

}