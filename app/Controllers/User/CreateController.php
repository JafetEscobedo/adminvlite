<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Entities\UserEntity;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class CreateController extends BaseController
{

  public function single(): ResponseInterface
  {
    try {
      $this->db->transBegin();
      $userModel  = new UserModel();
      $userEntity = new UserEntity();
      $userEntity->fill($this->request->getPost());
      $userModel->createSingle($userEntity);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $userEntity,
          "message" => handle_response("Se creó correctamente el usuario en la base de datos")
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
