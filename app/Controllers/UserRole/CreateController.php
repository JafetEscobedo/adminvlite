<?php

namespace App\Controllers\UserRole;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Entities\UserRoleEntity;
use App\Models\UserRoleModel;
use Throwable;

class CreateController extends BaseController
{

  public function single(): ResponseInterface
  {
    try {
      $this->db->transBegin();

      $userRoleEntity = new UserRoleEntity();
      $userRoleModel  = new UserRoleModel();

      $userRoleEntity->userRoleName = $this->request->getPost("userRoleName");
      $userRoleEntity->setUserRoleAccessFromRequest($this->request, $this->menus);
      $userRoleModel->createSingle($userRoleEntity);

      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $userRoleEntity,
          "message" => handle_response("Se creó correctamente el rol de usuario en la base de datos")
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