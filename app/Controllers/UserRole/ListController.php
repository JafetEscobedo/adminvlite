<?php

namespace App\Controllers\UserRole;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\UserRoleModel;
use Throwable;

class ListController extends BaseController
{

  public function userRoles(): ResponseInterface
  {
    try {
      $config = [
        "offset" => $this->request->getPost("offset"),
        "limit"  => $this->request->getPost("limit"),
        "needle" => $this->request->getPost("needle"),
        "column" => $this->request->getPost("column"),
        "order"  => $this->request->getPost("order")
      ];

      $this->db->transBegin();
      $userRoleModel = new UserRoleModel();
      $result        = $userRoleModel->listUserRoles($config);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result->data) . " roles de usuario listados ahora")
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => ["total" => 0, "filtered" => 0, "data" => []],
          "message" => handle_response($th->getMessage())
      ]);
    }
  }

  public function activeUserRoles(): ResponseInterface
  {
    try {
      $config = [
        "offset" => $this->request->getPost("offset"),
        "limit"  => $this->request->getPost("limit"),
        "needle" => $this->request->getPost("needle"),
        "column" => $this->request->getPost("column"),
        "order"  => $this->request->getPost("order"),
        "status" => "active"
      ];

      $this->db->transBegin();
      $userRoleModel = new UserRoleModel();
      $result        = $userRoleModel->listUserRoles($config);

      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result->data) . " unidades listadas ahora")
      ]);
    } catch (Throwable $th) {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => ["total" => 0, "filtered" => 0, "data" => []],
          "message" => handle_response($th->getMessage())
      ]);
    }
  }
}