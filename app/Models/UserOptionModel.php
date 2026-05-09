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
}
