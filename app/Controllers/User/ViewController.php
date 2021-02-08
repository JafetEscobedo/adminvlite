<?php

namespace App\Controllers\User;

use App\Models\UserModel;
use App\Controllers\BaseController;

class ViewController extends BaseController
{

  public function create(): string
  {
    return $this->template->render("user/create");
  }

  public function update(int $userId): string
  {
    $this->db->transBegin();
    $userModel          = new UserModel();
    $data["userEntity"] = $userModel->readSingle($userId);
    $this->db->transCommit();
    return $this->template->render("user/update", $data);
  }

  public function usersList(): string
  {
    return $this->template->render("user/users_list");
  }
}