<?php

namespace App\Controllers\ItemHistory;

use App\Controllers\BaseController;
use App\Models\ItemHistoryEventModel;

class ViewController extends BaseController
{

  public function ingress(): string
  {
    return $this->template->render("item_history/ingress");
  }

  public function egress(): string
  {
    $this->db->transBegin();
    $itemHistoryEventModel = new ItemHistoryEventModel();
    $config["type"]        = "egress";
    $config["system"]      = 'n';
    $data["events"]        = $itemHistoryEventModel->listItemHistoryEvents($config)->data;
    $this->db->transCommit();
    return $this->template->render("item_history/egress", $data);
  }

  public function inventory(): string
  {
    return $this->template->render("item_history/inventory");
  }
}