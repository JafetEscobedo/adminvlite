<?php

namespace App\Controllers\SaleDetail;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\SaleDetailModel;
use Throwable;

class ListController extends BaseController
{

  public function saleDetailsBySaleSerial(string $saleSerial = ''): ResponseInterface
  {
    try
    {
      $this->db->transBegin();
      $saleDetailModel = new SaleDetailModel();
      $result          = $saleDetailModel->listSaleDetailsBySaleSerial($saleSerial);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result) . " registro (s) listados en total")
      ]);
    }
    catch (Throwable $th)
    {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => 0,
          "message" => handle_response($th->getMessage())
      ]);
    }
  }
}