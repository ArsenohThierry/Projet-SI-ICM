<?php

namespace App\Models;

use CodeIgniter\Model;

class ObjectifModel extends Model
{
    protected $table = 'objectif';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'libelle'
    ];
    public function getObjectifs(){
        return $this->findAll();
    }
}