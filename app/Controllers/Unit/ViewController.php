<?php

namespace App\Controllers\Unit;

use App\Controllers\BaseController;
use App\Models\UnitModel;

class ViewController extends BaseController
{

  public function create(): string
  {
    return $this->template->render("unit/create");
  }

  public function update(int $unitId): string
  {
    $this->db->transBegin();
    $unitModel          = new UnitModel();
    $data["unitEntity"] = $unitModel->readSingle($unitId);
    $this->db->transCommit();
    return $this->template->render("unit/update", $data);
  }

  public function unitsList(): string
  {
    return $this->template->render("unit/units_list");
  }
}