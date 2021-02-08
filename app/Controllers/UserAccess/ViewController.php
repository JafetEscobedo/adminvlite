<?php

namespace App\Controllers\UserAccess;

use App\Controllers\BaseController;

class ViewController extends BaseController
{

  public function userAccessList(): string
  {
    return $this->template->render("user_access/user_access_list");
  }
}