<?php

namespace App\Controllers\Item;

use App\Controllers\BaseController;
use App\Entities\ItemEntity;
use App\Models\ItemModel;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class CreateController extends BaseController
{

  public function single(): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $itemEntity = new ItemEntity();
      $itemModel  = new ItemModel();
      $itemEntity->fill($this->request->getPost());
      $itemModel->createSingle($itemEntity);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $itemEntity,
          "message" => handle_response("Se creó correctamente el artículo en la base de datos"),
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => null,
          "message" => handle_response($th->getMessage()),
      ]);
    }
  }
}