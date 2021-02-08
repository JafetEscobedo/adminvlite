<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Entities\UserEntity;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class UpdateController extends BaseController
{

  public function single(): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $userModel  = new UserModel();
      $userEntity = new UserEntity();

      $userEntity->userId       = $this->request->getPost("userId", FILTER_VALIDATE_INT);
      $userEntity->userRoleId   = $this->request->getPost("userRoleId", FILTER_VALIDATE_INT);
      $userEntity->userName     = $this->request->getPost("userName");
      $userEntity->userSurname  = $this->request->getPost("userSurname");
      $userEntity->userActive   = $this->request->getPost("userActive");
      $userEntity->userNickname = $this->request->getPost("userNickname");
      $userPlainPassword        = $this->request->getPost("userPassword");

      if ($userPlainPassword) {
        $userEntity->userPassword = $userPlainPassword;
      }

      $userModel->updateSingle($userEntity);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $userEntity,
          "message" => handle_response("Se actualizó correctamente el usuario en la base de datos")
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