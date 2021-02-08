<?php

namespace App\Controllers\ItemHistory;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Entities\ItemHistoryEntity;
use App\Models\ItemHistoryModel;
use Throwable;

class CreateController extends BaseController
{

  public function singleUsingBatch(): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $itemHistoryModel  = new ItemHistoryModel();
      $itemHistoryEntity = new ItemHistoryEntity();
      $json              = $this->request->getPost("itemHistoryJsonString");
      $batch             = $itemHistoryEntity->convertJsonStringToArray($json);
      $result            = $itemHistoryModel->createSingleUsingBatch($batch);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response("Inventario actualizado correctamente"),
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => 0,
          "message" => handle_response($th->getMessage()),
      ]);
    }
  }
}