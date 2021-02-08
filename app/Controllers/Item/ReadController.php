<?php

namespace App\Controllers\Item;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class ReadController extends BaseController
{

  public function single(int $itemId): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $itemModel = new ItemModel();
      $result    = $itemModel->readSingle($itemId);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response("Se leyó correctamente el artículo de la base de datos"),
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => null,
          "message" => handle_response($th->getMessage()),
      ]);
    }
  }

  public function singleByItemCode(string $code = ''): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $itemModel = new ItemModel();
      $result    = $itemModel->readSingleByItemCode($code);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response("Se leyó correctamente el artículo de la base de datos"),
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => null,
          "message" => handle_response($th->getMessage()),
      ]);
    }
  }

  public function itemsSummary(): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $itemModel = new ItemModel();
      $result    = $itemModel->readItemsSummary();
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response("Resumén generado correctamente"),
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => ["totalItems" => 0, "totalCost" => 0, "totalPrice" => []],
          "message" => handle_response($th->getMessage()),
      ]);
    }
  }
}