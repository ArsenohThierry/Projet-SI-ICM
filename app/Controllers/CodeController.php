<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\UserModel;

class CodeController extends BaseController
{
    public function redeemForm()
    {
        $data['user'] = $this->getSessionUser();
        return view('codes/redeem', $data);
    }

    public function redeem()
    {
        $rules = [
            'code' => 'required|min_length[4]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/codes/redeem')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userId = (int) session()->get('user_id');
        $codeValue = trim((string) $this->request->getPost('code'));

        $codeModel = new CodeModel();
        $result = $codeModel->redeemForUser($codeValue, $userId);

        if (!$result['ok']) {
            return redirect()->to('/codes/redeem')->with('error', $result['message']);
        }

        return redirect()->to('/codes/redeem')->with('success', 'Code applique. Montant credite: ' . $result['montant']);
    }

    private function getSessionUser(): ?array
    {
        $id = (int) session()->get('user_id');
        if ($id <= 0) {
            return null;
        }

        $userModel = new UserModel();
        return $userModel->getUserById($id);
    }
}
