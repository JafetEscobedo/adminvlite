<?php

namespace App\Controllers\ItemHistory;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\ItemHistoryModel;
use Throwable;

class ListController extends BaseController
{

  public function byItemId(int $itemId): ResponseInterface
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
      $itemHistoryModel = new ItemHistoryModel();
      $result           = $itemHistoryModel->listByItemId($itemId, $config);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result->data) . " registros listados ahora"),
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