<?php

namespace App\Controllers\UserAccess;

use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\UserAccessModel;
use Throwable;

class ListController extends BaseController
{

  public function userAccess(): ResponseInterface
  {
    try
    {
      $config = [
        "userId" => $this->request->getPost("userId", FILTER_VALIDATE_INT),
        "offset" => $this->request->getPost("offset"),
        "limit"  => $this->request->getPost("limit"),
        "needle" => $this->request->getPost("needle"),
        "column" => $this->request->getPost("column"),
        "order"  => $this->request->getPost("order"),
        "sdate"  => $this->request->getPost("sdate"),
        "fdate"  => $this->request->getPost("fdate")
      ];

      $this->db->transBegin();
      $userAccessModel = new UserAccessModel();
      $result          = $userAccessModel->listUserAccess($config);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $result,
          "message" => handle_response(count($result->data) . " registros listados ahora")
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