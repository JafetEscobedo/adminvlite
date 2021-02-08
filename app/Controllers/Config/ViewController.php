<?php

namespace App\Controllers\Config;

use App\Controllers\BaseController;
use App\Models\ConfigModel;

class ViewController extends BaseController
{

  public function businessImg(): string
  {
    $configModel  = new ConfigModel();
    $configEntity = $configModel->readSingle();
    return $this->template->render("config/business_img", compact("configEntity"));
  }

  public function businessName(): string
  {
    $configModel  = new ConfigModel();
    $configEntity = $configModel->readSingle();
    return $this->template->render("config/business_name", compact("configEntity"));
  }
}