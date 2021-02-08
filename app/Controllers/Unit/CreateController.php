<?php

namespace App\Controllers\Unit;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Entities\UnitEntity;
use App\Models\UnitModel;
use Throwable;

class CreateController extends BaseController
{

  public function single(): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $unitEntity = new UnitEntity();
      $unitModel  = new UnitModel();

      $unitEntity->unitSingularName = $this->request->getPost("unitSingularName");
      $unitEntity->unitPluralName   = $this->request->getPost("unitPluralName");
      $unitModel->createSingle($unitEntity);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $unitEntity,
          "message" => handle_response("Se creó correctamente la unidad en la base de datos"),
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