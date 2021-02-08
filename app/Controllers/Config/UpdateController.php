<?php

namespace App\Controllers\Config;

use App\Controllers\BaseController;
use App\Entities\ConfigEntity;
use App\Models\ConfigModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Throwable;

class UpdateController extends BaseController
{

  public function businessLogo(): ResponseInterface
  {
    try
    {
      $this->db->transBegin();
      $file = $this->request->getFile("configBusinessLogo");

      if (!$file->isValid())
      {
        throw new Exception($file->getErrorString() . "({$file->getError()})");
      }

      if ($file->hasMoved())
      {
        throw new Exception("El archivo ya no está disponible");
      }

      $this->validate([
        "configBusinessLogo" => [
          "label" => "Logo principal de empresa",
          "rules" => "is_image[configBusinessLogo]|max_size[configBusinessLogo,2048]"
      ]]);

      if (!$this->validator->run())
      {
        $errors = $this->validator->getErrors();
        throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors
        ]));
      }

      $configEntity = new ConfigEntity();
      $configModel  = new ConfigModel();

      $configEntity->configBusinessLogo = date("YmdHis_") . $file->getName();
      $configModel->updateSingle($configEntity);
      $file->move("public/img/config", $configEntity->configBusinessLogo);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $configEntity,
          "message" => handle_response("Logo actualizado correctamente, vuelva a iniciar sesión")
      ]);
    }
    catch (Throwable $th)
    {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => null,
          "message" => handle_response($th->getMessage())
      ]);
    }
  }

  public function businessIcon(): ResponseInterface
  {
    try
    {
      $this->db->transBegin();
      $file = $this->request->getFile("configBusinessIcon");

      if (!$file->isValid())
      {
        throw new Exception($file->getErrorString() . "({$file->getError()})");
      }

      if ($file->hasMoved())
      {
        throw new Exception("El archivo ya no está disponible");
      }

      $this->validate([
        "configBusinessIcon" => [
          "label" => "Ícono principal de empresa",
          "rules" => "is_image[configBusinessIcon]|max_size[configBusinessIcon,512]"
      ]]);

      if (!$this->validator->run())
      {
        $errors = $this->validator->getErrors();
        throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors
        ]));
      }

      $configEntity = new ConfigEntity();
      $configModel  = new ConfigModel();

      $configEntity->configBusinessIcon = date("ymdHis_") . $file->getName();
      $configModel->updateSingle($configEntity);
      $file->move("public/img/config", $configEntity->configBusinessIcon);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $configEntity,
          "message" => handle_response("Ícono actualizado correctamente, vuelva a iniciar sesión")
      ]);
    }
    catch (Throwable $th)
    {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => null,
          "message" => handle_response($th->getMessage())
      ]);
    }
  }

  public function businessName(): ResponseInterface
  {
    try
    {
      $this->db->transBegin();
      $configEntity = new ConfigEntity();
      $configModel  = new ConfigModel();

      $configEntity->configBusinessName   = $this->request->getPost("configBusinessName");
      $configEntity->configBusinessNameUc = $this->request->getPost("configBusinessNameUc");
      $configModel->updateSingle($configEntity);
      $this->db->transCommit();

      return $this->response->setJSON([
          "ok"      => true,
          "result"  => $configEntity,
          "message" => handle_response("Nombre actualizado correctamente, vuelva a iniciar sesión")
      ]);
    }
    catch (Throwable $th)
    {
      return $this->response->setStatusCode(400)->setJSON([
          "ok"      => false,
          "result"  => null,
          "message" => handle_response($th->getMessage())
      ]);
    }
  }
}