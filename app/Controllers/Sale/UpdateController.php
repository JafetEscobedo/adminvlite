<?php

namespace App\Controllers\Sale;

use App\Controllers\BaseController;
use App\Entities\SaleEntity;
use App\Models\SaleModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class UpdateController extends BaseController
{

  public function cancelSingle(): ResponseInterface
  {
    try {
      $this->db->transBegin();

      $saleModel  = new SaleModel();
      $saleEntity = new SaleEntity();

      $saleEntity->saleSerial     = $this->request->getPost("saleSerial");
      $saleEntity->saleCancelNote = $this->request->getPost("saleCancelNote");

      $saleEntity = $saleModel->cancelSingle($saleEntity);
      $result     = $saleEntity->getSaleDetails();

      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response("Venta cancelada correctamente e inventario ajustado")
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => 0,
          "message" => handle_response($th->getMessage())
      ]);
    }
  }
}