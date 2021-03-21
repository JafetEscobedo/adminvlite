<?php

namespace App\Models;

use App\Entities\UserRoleEntity;
use CodeIgniter\Model;
use CodeIgniter\I18n\Time;
use Config\Services;
use Exception;

class UserRoleModel extends Model
{
  protected $table              = "user_role";
  protected $primaryKey         = "user_role_id";
  protected $returnType         = UserRoleEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = true;
  protected $skipValidation     = false;
  protected $createdField       = "user_role_created_at";
  protected $updatedField       = "user_role_updated_at";
  protected $deletedField       = '';
  protected $validationMessages = [];
  protected $allowedFields      = [
    "user_role_name",
    "user_role_access",
    "user_role_active",
    "user_role_inactivated_at"
  ];
  protected $validationRules    = [
    "user_role_name"           => ["label" => "nombre de rol", "rules" => "required|max_length[50]"],
    "user_role_access"         => ["label" => "accesos de rol", "rules" => "required|valid_json"],
    "user_role_active"         => ["label" => "estatus de rol", "rules" => "required|in_list[y,n]"],
    "user_role_inactivated_at" => ["label" => "fecha de inactivo", "rules" => "permit_empty|valid_date[Y-m-d H:i:s]"]
  ];

  public function updateSingle(UserRoleEntity &$userRoleEntity): UserRoleEntity
  {
    $baseEntity                 = $this->readSingle($userRoleEntity->userRoleId);
    $baseEntity->userRoleActive = $userRoleEntity->userRoleActive;

    if ($baseEntity->hasChanged("user_role_active"))
    {
      if ($baseEntity->isActive())
      {
        $this->set("user_role_inactivated_at", null);
      }
      else
      {
        $this->set("user_role_inactivated_at", Time::now()->toDateTimeString());
      }
    }

    $this->set("user_role_name", $userRoleEntity->userRoleName ?? $baseEntity->userRoleName);
    $this->set("user_role_access", $userRoleEntity->userRoleAccess ?? $baseEntity->userRoleAccess);
    $this->set("user_role_active", $userRoleEntity->userRoleActive ?? $baseEntity->userRoleActive);

    if (!$this->update($userRoleEntity->userRoleId))
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    $userRoleEntity = $this->readSingle($userRoleEntity->userRoleId);
    return $userRoleEntity;
  }

  public function createSingle(UserRoleEntity &$userRoleEntity): UserRoleEntity
  {
    $this->set("user_role_name", $userRoleEntity->userRoleName);
    $this->set("user_role_access", $userRoleEntity->userRoleAccess);
    $this->set("user_role_active", 'y');

    if (!$this->insert())
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    $userRoleEntity = $this->readSingle($this->db->insertId());
    return $userRoleEntity;
  }

  public function readSingle(int $userRoleId): UserRoleEntity
  {
    $userRoleEntity = $this->find($userRoleId);
    $errors         = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    if (!is_a($userRoleEntity, UserRoleEntity::class))
    {
      throw new Exception("No se proporcionó un ID de rol de usuario válido");
    }

    return $userRoleEntity;
  }

  public function listUserRoles(array $config): object
  {
    $this->validateUserRolesListPagination($config);

    $this->buildSelectQuery($config);
    $total    = $this->countAllResults();
    $errors[] = $this->errors();

    $this->buildSelectQuery($config);
    $this->buildFilterQuery($config);
    $filtered = $this->countAllResults();
    $errors[] = $this->errors();

    $this->buildSelectQuery($config);
    $this->buildFilterQuery($config);
    $data     = $this->findAll($config["limit"], $config["offset"]);
    $errors[] = $this->errors();

    foreach ($errors as $err)
    {
      if ($err)
      {
        throw new Exception(json_encode([
              "type" => gettype($err),
              "data" => $err,
        ]));
      }
    }

    return (object) [
        "total"    => $total,
        "filtered" => $filtered,
        "data"     => $data,
    ];
  }

  // Métodos privados
  private function buildSelectQuery(array $config): void
  {
    foreach ($config["ordering"] as $ordering)
    {
      $this->orderBy($ordering["column"], mb_strtoupper($ordering["order"]));
    }

    $config["status"] === "active" && $this->where("user_role.user_role_active", 'y');
    $config["status"] === "inactive" && $this->where("user_role.user_role_active", 'n');
  }

  private function buildFilterQuery(array $config): void
  {
    $this->groupStart();
    $this->like("user_role.user_role_name", $config["needle"]);
    $this->groupEnd();
  }

  private function validateUserRolesListPagination(array &$config): array
  {
    $validation = Services::validation();
    $config     = [
      "offset" => $config["offset"] ?? 0,
      "limit"  => $config["limit"] ?? 10,
      "column" => $config["column"] ?? "unitCreatedAt",
      "order"  => $config["order"] ?? "DESC",
      "status" => $config["status"] ?? '',
      "needle" => $config["needle"] ?? ''
    ];

    $validation->setRules([
      "offset" => ["label" => "inicio de paginación", "rules" => "greater_than_equal_to[0]"],
      "limit"  => ["label" => "tamaño de paginación", "rules" => "greater_than[0]|less_than_equal_to[100]"],
      "status" => ["label" => "estatus de rol de usuario", "rules" => "permit_empty|in_list[active,inactive]"]
    ]);

    if (!$validation->run($config))
    {
      $errors = $validation->getErrors();
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    $columns = explode(',', trim($config["column"], ','));
    $orders  = explode(',', trim($config["order"], ','));

    if (count($columns) !== count($orders))
    {
      throw new Exception("La relación entre columna y orden no es correcta");
    }

    for ($i = 0; $i < count($columns); $i++)
    {
      $userRoleEntity = new UserRoleEntity();
      $userRoleColumn = $userRoleEntity->getDatamapValue($columns[$i]);

      if (!$userRoleColumn)
      {
        throw new Exception("La columna {$columns[$i]} no es válida");
      }

      if (!$validation->check($orders[$i], "permit_empty|in_list[asc,desc,ASC,DESC]"))
      {
        throw new Exception("El valor de ordenamiento '{$orders[$i]}' no es válido");
      }

      $userRoleColumn && $column = "user_role." . $userRoleColumn;

      $config["ordering"][] = [
        "column" => $column,
        "order"  => $orders[$i],
      ];
    }

    return $config;
  }
}