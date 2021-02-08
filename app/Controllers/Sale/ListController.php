<?php

namespace App\Controllers\Sale;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\SaleModel;
use Throwable;

class ListController extends BaseController
{

  public function sales(): ResponseInterface
  {
    try {
      $config = [
        "offset" => $this->request->getPost("offset"),
        "limit"  => $this->request->getPost("limit"),
        "needle" => $this->request->getPost("needle"),
        "column" => $this->request->getPost("column"),
        "order"  => $this->request->getPost("order"),
        "status" => $this->request->getPost("status"),
        "sdate"  => $this->request->getPost("sdate"),
        "fdate"  => $this->request->getPost("fdate"),
      ];

      $this->db->transBegin();
      $saleModel = new SaleModel();
      $result    = $saleModel->listSales($config);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result->data) . " ventas listadas ahora")
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => ["total" => 0, "filtered" => 0, "data" => []],
          "message" => handle_response($th->getMessage())
      ]);
    }
  }
}