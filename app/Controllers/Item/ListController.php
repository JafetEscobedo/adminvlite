<?php

namespace App\Controllers\Item;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class ListController extends BaseController
{

  public function items(): ResponseInterface
  {
    try {
      $config = [
        "offset" => $this->request->getPost("offset"),
        "limit"  => $this->request->getPost("limit"),
        "needle" => $this->request->getPost("needle"),
        "column" => $this->request->getPost("column"),
        "order"  => $this->request->getPost("order")
      ];

      $this->db->transBegin();
      $itemModel = new ItemModel();
      $result    = $itemModel->listItems($config);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result->data) . " artículos listados ahora"),
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => ["total" => 0, "filtered" => 0, "data" => []],
          "message" => handle_response($th->getMessage()),
      ]);
    }
  }

  public function activeItems(): ResponseInterface
  {
    try {
      $config = [
        "offset" => $this->request->getPost("offset"),
        "limit"  => $this->request->getPost("limit"),
        "needle" => $this->request->getPost("needle"),
        "column" => $this->request->getPost("column"),
        "order"  => $this->request->getPost("order"),
        "status" => "active"
      ];

      $this->db->transBegin();
      $itemModel = new ItemModel();
      $result    = $itemModel->listItems($config);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result->data) . " artículos listados ahora"),
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => ["total" => 0, "filtered" => 0, "data" => []],
          "message" => handle_response($th->getMessage()),
      ]);
    }
  }
}