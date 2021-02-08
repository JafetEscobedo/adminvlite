<?php

namespace App\Controllers\OffSite;

use App\Controllers\BaseController;

class ViewController extends BaseController
{

  public function login(): string
  {
    return view("offsite/login");
  }
}