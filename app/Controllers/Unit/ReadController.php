<?php

namespace App\Controllers\Unit;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UnitModel;
use Throwable;

class ReadController extends BaseController
{

  public function single(int $unitId): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $unitModel = new UnitModel();
      $result    = $unitModel->readSingle($unitId);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response("Se leyó correctamente la unidad de la base de datos"),
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