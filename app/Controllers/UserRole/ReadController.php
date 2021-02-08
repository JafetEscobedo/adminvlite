<?php

namespace App\Controllers\UserRole;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserRoleModel;
use Throwable;

class ReadController extends BaseController
{

  public function single(int $userRoleId): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $userRoleModel = new UserRoleModel();
      $result        = $userRoleModel->readSingle($userRoleId);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response("Se leyó correctamente el rol de usuario de la base de datos"),
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