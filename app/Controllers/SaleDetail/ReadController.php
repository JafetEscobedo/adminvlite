<?php

namespace App\Controllers\SaleDetail;

use App\Controllers\BaseController;
use App\Models\SaleDetailModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class ReadController extends BaseController
{

  public function salesGlobalSummary(): ResponseInterface
  {
    try {
      $config = [
        "sdate"  => $this->request->getPost("sdate"),
        "edate"  => $this->request->getPost("edate"),
        "status" => $this->request->getPost("status"),
        "needle" => $this->request->getPost("needle")
      ];

      $this->db->transBegin();
      $saleDetailModel = new SaleDetailModel();
      $result          = $saleDetailModel->readSalesGlobalSummary($config);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response("Resumen de ventas generado correctamente")
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => null,
          "message" => handle_response($th->getMessage())
      ]);
    }
  }
}