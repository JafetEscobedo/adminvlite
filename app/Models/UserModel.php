<?php

namespace App\Models;

use App\Entities\UserEntity;
use App\Entities\UserRoleEntity;
use Config\Services;
use CodeIgniter\Model;
use CodeIgniter\I18n\Time;
use Exception;

class UserModel extends Model
{
  protected $table              = "user";
  protected $primaryKey         = "user_id";
  protected $returnType         = UserEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = true;
  protected $skipValidation     = false;
  protected $createdField       = "user_created_at";
  protected $updatedField       = "user_updated_at";
  protected $deletedField       = '';
  protected $validationMessages = [];
  protected $allowedFields      = [
    "user_nickname",
    "user_password",
    "user_name",
    "user_surname",
    "user_active",
    "user_inactivated_at",
    "user_role_id"
  ];
  protected $validationRules    = [
    "user_nickname"       => ["label" => "nombre de usuario", "rules" => "required|alpha_numeric|max_length[50]"],
    "user_password"       => ["label" => "contraseña", "rules" => "required|max_length[255]"],
    "user_name"           => ["label" => "nombre (s)", "rules" => "required|max_length[50]"],
    "user_surname"        => ["label" => "apellido (s)", "rules" => "required|max_length[50]"],
    "user_active"         => ["label" => "estatus", "rules" => "required|in_list[y,n]"],
    "user_inactivated_at" => ["label" => "fecha de inactivado", "rules" => "permit_empty|valid_date[Y-m-d H:i:s]"],
    "user_role_id"        => ["label" => "rol", "rules" => "required|is_natural_no_zero"]
  ];

  public function updateSingle(UserEntity &$userEntity): UserEntity
  {
    $baseEntity             = $this->readSingle($userEntity->userId);
    $baseEntity->userActive = $userEntity->userActive;

    if ($baseEntity->hasChanged("user_active"))
    {
      if ($baseEntity->isActive())
      {
        $this->set("user_inactivated_at", null);
      }
      else
      {
        $this->set("user_inactivated_at", Time::now()->toDateTimeString());
      }
    }

    $this->set("user_nickname", $userEntity->userNickname ?? $baseEntity->userNickname);
    $this->set("user_password", $userEntity->userPassword ?? $baseEntity->userPassword);
    $this->set("user_name", $userEntity->userName ?? $baseEntity->userName);
    $this->set("user_surname", $userEntity->userSurname ?? $baseEntity->userSurname);
    $this->set("user_active", $userEntity->userActive ?? $baseEntity->userActive);
    $this->set("user_role_id", $userEntity->userRoleId ?? $baseEntity->userRoleId);

    if (!$this->update($userEntity->userId))
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    return $this->readSingle($userEntity->userId);
  }

  public function createSingle(UserEntity &$userEntity): UserEntity
  {
    $this->set("user_nickname", $userEntity->userNickname);
    $this->set("user_password", $userEntity->userPassword);
    $this->set("user_name", $userEntity->userName);
    $this->set("user_surname", $userEntity->userSurname);
    $this->set("user_active", 'y');
    $this->set("user_role_id", $userEntity->userRoleId);

    if (!$this->insert())
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    $userEntity = $this->readSingle($this->db->insertId());
    return $userEntity;
  }

  public function readSingle(int $userId): UserEntity
  {
    $userEntity = $this->find($userId);
    $errors     = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($userEntity, UserEntity::class))
    {
      throw new Exception("No se proporcionó un ID de usuario válido");
    }

    return $userEntity;
  }

  public function readSingleByUserNickname(string $userNickname): UserEntity
  {
    $this->where("user_nickname", $userNickname);
    $userEntity = $this->first();
    $errors     = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($userEntity, UserEntity::class))
    {
      throw new Exception("El nickname {$userNickname} no existe");
    }

    return $userEntity;
  }

  public function listUsers(array $config): object
  {
    $this->validateUsersListPagination($config);

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
    $this->select("user.*");
    $this->select("user_role.user_role_name AS userRoleName");
    $this->join("user_role", "user.user_role_id = user_role.user_role_id");

    foreach ($config["ordering"] as $ordering)
    {
      $this->orderBy($ordering["column"], mb_strtoupper($ordering["order"]));
    }

    $config["status"] == "active" && $this->where("user.user_active", 'y');
    $config["status"] == "inactive" && $this->where("user.user_active", 'n');
  }

  private function buildFilterQuery(array $config): void
  {
    $this->groupStart();
    $this->like("user.user_nickname", $config["needle"]);
    $this->orLike("user.user_name", $config["needle"]);
    $this->orLike("user.user_surname", $config["needle"]);
    $this->orLike("CONCAT_WS(' ', user.user_name, user.user_surname)", $config["needle"]);
    $this->orLike("user_role.user_role_name", $config["needle"]);
    $this->groupEnd();
  }

  private function validateUsersListPagination(array &$config): array
  {
    $validation = Services::validation();
    $config     = [
      "offset" => $config["offset"] ?? 0,
      "limit"  => $config["limit"] ?? 10,
      "column" => $config["column"] ?? "userCreatedAt",
      "order"  => $config["order"] ?? "DESC",
      "status" => $config["status"] ?? '',
      "needle" => $config["needle"] ?? ''
    ];

    $validation->setRules([
      "offset" => ["label" => "inicio de paginación", "rules" => "greater_than_equal_to[0]"],
      "limit"  => ["label" => "tamaño de paginación", "rules" => "greater_than[0]|less_than_equal_to[100]"],
      "status" => ["label" => "estatus de unidad", "rules" => "permit_empty|in_list[active,inactive]"]
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

    if (count($columns) != count($orders))
    {
      throw new Exception("La relación entre columna y orden no es correcta");
    }

    for ($i = 0; $i < count($columns); $i++)
    {
      $userEntity     = new UserEntity();
      $userRoleEntity = new UserRoleEntity();

      $userColumn     = $userEntity->getDatamapValue($columns[$i]);
      $userRoleColumn = $userRoleEntity->getDatamapValue($columns[$i]);

      if (!$userColumn && !$userRoleColumn)
      {
        throw new Exception("La columna {$columns[$i]} no es válida");
      }

      if (!$validation->check($orders[$i], "permit_empty|in_list[asc,desc,ASC,DESC]"))
      {
        throw new Exception("El valor de ordenamiento '{$orders[$i]}' no es válido");
      }

      $userColumn && $column = "user." . $userColumn;
      $userRoleColumn && $column = "user_role." . $userRoleColumn;

      $config["ordering"][] = [
        "column" => $column,
        "order"  => $orders[$i],
      ];
    }

    return $config;
  }
}