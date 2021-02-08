<?php

namespace App\Controllers\Sale;

use App\Controllers\BaseController;
use App\Entities\SaleEntity;
use App\Models\SaleModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class CreateController extends BaseController
{

  public function singleAndDetails(): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $saleModel  = new SaleModel();
      $saleEntity = new SaleEntity();

      $json               = $this->request->getPost("saleJsonString");
      $data["cash"]       = $this->request->getPost("cash");
      $data["cashBack"]   = $this->request->getPost("cashBack");
      $data["totalToPay"] = $this->request->getPost("totalToPay");
      $data["saleEntity"] = &$saleEntity;

      $rawDetails = $saleEntity->convertJsonStringToArray($json);
      $saleEntity = $saleModel->createSingleAndDetails($rawDetails);

      $result = view("sale/note", $data);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response("Venta realizada correctamente")
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