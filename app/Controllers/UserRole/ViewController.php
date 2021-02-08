<?php

namespace App\Controllers\UserRole;

use App\Models\UserRoleModel;
use App\Controllers\BaseController;

class ViewController extends BaseController
{

  public function create(): string
  {
    return $this->template->render("user_role/create");
  }

  public function update(int $userRoleId): string
  {
    $this->db->transBegin();
    $userRoleModel          = new UserRoleModel();
    $data["userRoleEntity"] = $userRoleModel->readSingle($userRoleId);
    $this->db->transCommit();
    return $this->template->render("user_role/update", $data);
  }

  public function userRolesList(): string
  {
    return $this->template->render("user_role/user_roles_list");
  }
}