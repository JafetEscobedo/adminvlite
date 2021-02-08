<?php

namespace App\Controllers\User;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\UserModel;
use Throwable;

class ListController extends BaseController
{

  public function users(): ResponseInterface
  {
    try
    {
      $config = [
        "offset" => $this->request->getPost("offset"),
        "limit"  => $this->request->getPost("limit"),
        "needle" => $this->request->getPost("needle"),
        "column" => $this->request->getPost("column"),
        "order"  => $this->request->getPost("order")
      ];

      $this->db->transBegin();
      $userModel = new UserModel();
      $result    = $userModel->listUsers($config);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result->data) . " usuarios listados ahora")
      ]);
    }
    catch (Throwable $th)
    {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => ["total" => 0, "filtered" => 0, "data" => []],
          "message" => handle_response($th->getMessage())
      ]);
    }
  }

  public function activeUsers(): ResponseInterface
  {
    try
    {
      $config = [
        "offset" => $this->request->getPost("offset"),
        "limit"  => $this->request->getPost("limit"),
        "needle" => $this->request->getPost("needle"),
        "column" => $this->request->getPost("column"),
        "order"  => $this->request->getPost("order"),
        "status" => "active"
      ];

      $this->db->transBegin();
      $userModel = new UserModel();
      $result    = $userModel->listUsers($config);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result->data) . " usuarios listados ahora")
      ]);
    }
    catch (Throwable $th)
    {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => ["total" => 0, "filtered" => 0, "data" => []],
          "message" => handle_response($th->getMessage())
      ]);
    }
  }
}