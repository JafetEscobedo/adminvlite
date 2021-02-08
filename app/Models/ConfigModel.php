<?php

namespace App\Models;

use App\Entities\ConfigEntity;
use CodeIgniter\Model;
use Exception;

class ConfigModel extends Model
{
  protected $table              = "config";
  protected $primaryKey         = "config_id";
  protected $returnType         = ConfigEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = true;
  protected $skipValidation     = false;
  protected $createdField       = "config_created_at";
  protected $updatedField       = "config_updated_at";
  protected $deletedField       = '';
  protected $validationMessages = [];
  protected $allowedFields      = [
    "config_business_name",
    "config_business_name_uc",
    "config_business_logo",
    "config_business_icon"
  ];
  protected $validationRules    = [
    "config_business_name"    => ["label" => "nombre de empresa", "rules" => "required|max_length[255]"],
    "config_business_name_uc" => ["label" => "nombre en mayusculas", "rules" => "required|in_list[y,n]"],
    "config_business_logo"    => ["label" => "logotipo de empresa", "rules" => "required|alpha_numeric_punct|max_length[255]"],
    "config_business_icon"    => ["label" => "ícono de empresa", "rules" => "required|alpha_numeric_punct|max_length[255]"]
  ];

  public function readSingle(int $configId = 1): ConfigEntity
  {
    $configEntity = $this->find($configId);
    $errors       = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($configEntity, ConfigEntity::class))
    {
      throw new Exception("No existe la configuración solicitada");
    }

    return $configEntity;
  }

  public function updateSingle(ConfigEntity &$configEntity): ConfigEntity
  {
    $configId   = $configEntity->configId ?? 1;
    $baseEntity = $this->readSingle($configId);

    $this->set("config_business_name", $configEntity->configBusinessName ?? $baseEntity->configBusinessName);
    $this->set("config_business_name_uc", $configEntity->configBusinessNameUc ?? $baseEntity->configBusinessNameUc);
    $this->set("config_business_logo", $configEntity->configBusinessLogo ?? $baseEntity->configBusinessLogo);
    $this->set("config_business_icon", $configEntity->configBusinessIcon ?? $baseEntity->configBusinessIcon);

    if (!$this->update($configId))
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    $configEntity = $this->readSingle($configId);
    return $configEntity;
  }
}