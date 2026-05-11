<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SportModel;
use App\Models\SportObjectifModel;
use App\Models\ObjectifModel;

class SportController extends BaseController
{
    protected $sportModel;
    protected $sportObjectifModel;
    protected $objectifModel;

    public function __construct()
    {
        $this->sportModel = new SportModel();
        $this->sportObjectifModel = new SportObjectifModel();
        $this->objectifModel = new ObjectifModel();
    }

    //liste des index
    public function index()
    {
        $data = [
            'sports' => $this->sportModel->getAllWithObjectif()
        ];

        return view('admin/sports/index', $data);
    }


    // form create
    public function create()
    {
        $data = [
            'objectifs' => $this->objectifModel->findAll()
        ];

        return view('admin/sports/create', $data);
    }

    //store (insert sport + sport_objectif)
    public function store()
    {
        // 1. insert sport
        $this->sportModel->insert([
            'nom' => $this->request->getPost('nom')
        ]);

        $sportId = $this->sportModel->insertID();

        // 2. insert sport_objectif
        $this->sportObjectifModel->insert([
            'sport_id' => $sportId,
            'objectif_id' => $this->request->getPost('objectif_id'),
            'age_min' => $this->request->getPost('age_min'),
            'age_max' => $this->request->getPost('age_max'),
            'calories_brulees' => $this->request->getPost('calories_brulees'),
            'duree_recommandee' => $this->request->getPost('duree_recommandee'),
            'genre' => $this->request->getPost('genre')
        ]);

        return redirect()->to('/admin/sports');
    }
    //form edit 
    public function edit($id)
    {
        $data = [
            'sport' => $this->sportModel->getByIdWithDetails($id),
            'objectifs' => $this->objectifModel->findAll()
        ];

        return view('admin/sports/edit', $data);
    }


    public function update($id)
    {
        $sportData = [
            'nom' => $this->request->getPost('nom')
        ];

        $objectifData = [
            'objectif_id' => $this->request->getPost('objectif_id'),
            'age_min' => $this->request->getPost('age_min'),
            'age_max' => $this->request->getPost('age_max'),
            'calories_brulees' => $this->request->getPost('calories_brulees'),
            'duree_recommandee' => $this->request->getPost('duree_recommandee'),
            'genre' => $this->request->getPost('genre')
        ];

        $this->sportModel->updateSportFull(
            $id,
            $sportData,
            $objectifData,
            $this->sportObjectifModel
        );

        return redirect()->to('/admin/sports');
    }

    // delete (sport + sport_objectif)
    public function delete($id)
    {
        $this->sportModel->deleteSportFull($id, $this->sportObjectifModel);

        return redirect()->to('/admin/sports');
    }
}