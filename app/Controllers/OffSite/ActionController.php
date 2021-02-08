<?php

namespace App\Controllers\OffSite;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Throwable;

class ActionController extends BaseController
{

  public function login(): ResponseInterface
  {
    try
    {
      $this->db->transBegin();

      $userNickname = $this->request->getPost("userNickname") ?? '';
      $userPassword = $this->request->getPost("userPassword") ?? '';

      $userModel  = new UserModel();
      $userEntity = $userModel->readSingleByUserNickname($userNickname);

      if (!$userEntity->isActive())
      {
        throw new Exception("El usuario {$userNickname} no está activo");
      }

      if (!$userEntity->isCorrectPassword($userPassword))
      {
        throw new Exception("La contraseña no es correcta");
      }

      $userEntity->sessionStart();

      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $userEntity,
          "message" => handle_response("Acceso correcto. Espere un momento..."),
      ]);
    }
    catch (Throwable $th)
    {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => null,
          "message" => handle_response($th->getMessage()),
      ]);
    }
  }

  public function logout(): RedirectResponse
  {
    $this->db->transBegin();
    $userModel  = new UserModel();
    $userEntity = $userModel->readSingle($this->session->get("user_id"));
    $userEntity->sessionEnd();
    $this->db->transCommit();
    return redirect("/");
  }
}