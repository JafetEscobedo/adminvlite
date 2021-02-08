<?php

namespace App\Controllers\Item;

use App\Controllers\BaseController;
use App\Models\ItemHistoryModel;
use App\Models\ItemModel;

class ViewController extends BaseController
{

  public function create(): string
  {
    return $this->template->render("item/create");
  }

  public function update(int $itemId): string
  {
    $this->db->transBegin();
    $itemModel          = new ItemModel();
    $data["itemEntity"] = $itemModel->readSingle($itemId);
    $this->db->transCommit();

    return $this->template->render("item/update", $data);
  }

  public function itemsList(): string
  {
    return $this->template->render("item/items_list");
  }

  public function history(int $itemId): string
  {
    $this->db->transBegin();
    $itemModel        = new ItemModel();
    $itemHistoryModel = new ItemHistoryModel();

    $config["limit"]         = 100;
    $config["order"]         = $this->request->getGet("order") ?? "desc";
    $data["order"]           = $config["order"];
    $data["itemEntity"]      = $itemModel->readSingle($itemId);
    $data["itemHistoryList"] = $itemHistoryModel->listByItemId($itemId, $config);
    $this->db->transCommit();

    return $this->template->render("item/history", $data);
  }
}