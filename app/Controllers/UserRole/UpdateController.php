<?php

namespace App\Controllers\UserRole;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Entities\UserRoleEntity;
use App\Models\UserRoleModel;
use Throwable;

class UpdateController extends BaseController
{

  public function single(): ResponseInterface
  {
    try {
      $this->db->transBegin();

      $userRoleEntity = new UserRoleEntity();
      $userRoleModel  = new UserRoleModel();

      $userRoleEntity->userRoleId     = $this->request->getPost("userRoleId", FILTER_VALIDATE_INT);
      $userRoleEntity->userRoleName   = $this->request->getPost("userRoleName");
      $userRoleEntity->userRoleActive = $this->request->getPost("userRoleActive");
      $userRoleEntity->setUserRoleAccessFromRequest($this->request, $this->menus);
      $userRoleModel->updateSingle($userRoleEntity);

      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $userRoleEntity,
          "message" => handle_response("Se actualizó correctamente el rol de usuario en la base de datos")
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