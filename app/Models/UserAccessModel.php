<?php

namespace App\Models;

use App\Entities\UserAccessEntity;
use App\Entities\UserEntity;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use Config\Services;
use Exception;

class UserAccessModel extends Model
{
  protected $table              = "user_access";
  protected $primaryKey         = "user_access_id";
  protected $returnType         = UserAccessEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = false;
  protected $skipValidation     = false;
  protected $createdField       = '';
  protected $updatedField       = '';
  protected $deletedField       = '';
  protected $validationMessages = [];
  protected $allowedFields      = [
    "user_access_first",
    "user_access_last",
    "user_id"
  ];
  protected $validationRules    = [
    "user_role_first" => ["label" => "primer acceso", "rules" => "permit_empty|valid_date[Y-m-d H:i:s]"],
    "user_role_last"  => ["label" => "último acceso", "rules" => "permit_empty|valid_date[Y-m-d H:i:s]"],
    "user_id"         => ["label" => "usuario", "rules" => "required|is_natural_no_zero"]
  ];

  public function isLoggedToday(int $userId): bool
  {
    $this->where("user_access_first >=", Time::today()->toDateTimeString());
    $this->where("user_id", $userId);
    $result = (int) $this->countAllResults();
    $errors = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    return $result === 1;
  }

  public function updateSingle(UserAccessEntity &$userAccessEntity): UserAccessEntity
  {
    $this->set("user_access_last", Time::now()->toDateTimeString());

    if (!$this->update($userAccessEntity->userAccessId))
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    $userAccessEntity = $this->readSingle($userAccessEntity->userAccessId);
    return $userAccessEntity;
  }

  public function createSingle(UserAccessEntity &$userAccessEntity): UserAccessEntity
  {
    $this->set("user_access_first", Time::now()->toDateTimeString());
    $this->set("user_id", $userAccessEntity->userId);

    if (!$this->insert())
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    $userAccessEntity = $this->readSingle($this->db->insertId());
    return $userAccessEntity;
  }

  public function readSingle(int $userAccessId): UserAccessEntity
  {
    $userAccessEntity = $this->find($userAccessId);
    $errors           = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    if (!is_a($userAccessEntity, UserAccessEntity::class))
    {
      throw new Exception("No se proporcionó un ID de acceso de usuario válido");
    }

    return $userAccessEntity;
  }

  public function readSingleByDate(string $date)
  {
    $this->where("user_access_first >=", Time::parse($date)->today()->toDateTimeString());
    $this->where("user_access_first <", Time::parse($date)->tomorrow()->toDateTimeString());

    $userAccessEntity = $this->first();
    $errors           = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    if (!is_a($userAccessEntity, UserAccessEntity::class))
    {
      throw new Exception("No hay un primer acceso para la fecha proporcionada");
    }

    return $userAccessEntity;
  }

  public function listUserAccess(array $config): object
  {
    $this->validateUserAccessPagination($config);

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
        "data"     => $data
    ];
  }

  private function buildFilterQuery(array $config): void
  {
    $sdate = $config["sdate"];
    $fdate = $config["fdate"];

    $this->groupStart();
    $this->like("user.user_nickname", $config["needle"]);
    $this->orLike("user.user_name", $config["needle"]);
    $this->orLike("user.user_surname", $config["needle"]);
    $this->orLike("CONCAT_WS(' ', user.user_name, user.user_surname)", $config["needle"]);
    $this->groupEnd();

    $config["userId"] && $this->where("user_access.user_id", $config["userId"]);

    if (!empty($sdate) && !empty($fdate))
    {
      if (Time::parse($sdate)->isAfter($fdate))
      {
        throw new Exception("La fecha inicial no puede ser mayor a la fecha final");
      }
    }

    empty($sdate) || $this->where("user_access.user_access_first >=", Time::parse($sdate)->toDateTimeString());
    empty($fdate) || $this->where("user_access.user_access_first <", Time::parse($fdate)->addDays(1)->toDateTimeString());
  }

  private function buildSelectQuery(array $config): void
  {
    $this->select("user_access.*");
    $this->select("user.user_nickname AS userNickname");
    $this->select("user.user_name AS userName");
    $this->select("user.user_surname AS userSurname");
    $this->join("user", "user.user_id = user_access.user_id");

    foreach ($config["ordering"] as $ordering)
    {
      $this->orderBy($ordering["column"], mb_strtoupper($ordering["order"]));
    }
  }

  private function validateUserAccessPagination(array &$config): bool
  {
    $validation = Services::validation();

    $config = [
      "offset" => $config["offset"] ?? 0,
      "limit"  => $config["limit"] ?? 10,
      "column" => $config["column"] ?? "userAccessFirst",
      "order"  => $config["order"] ?? "DESC",
      "needle" => $config["needle"] ?? '',
      "sdate"  => $config["sdate"] ?? '',
      "fdate"  => $config["fdate"] ?? '',
      "userId" => $config["userId"] ?? ''
    ];

    $validation->setRules([
      "offset" => ["label" => "inicio de paginación", "rules" => "greater_than_equal_to[0]"],
      "limit"  => ["label" => "tamaño de paginación", "rules" => "greater_than[0]|less_than_equal_to[100]"],
      "sdate"  => ["label" => "fecha inicial", "rules" => "permit_empty|valid_date[Y-m-d]"],
      "fdate"  => ["label" => "fecha final", "rules" => "permit_empty|valid_date[Y-m-d]"],
      "userId" => ["label" => "usuario", "rules" => "permit_empty|is_natural"] // 0 = Todos los usuarios
    ]);

    if (!$validation->run($config))
    {
      $errors = $validation->getErrors();
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    $orders  = explode(',', trim($config["order"], ','));
    $columns = explode(',', trim($config["column"], ','));

    if (count($columns) !== count($orders))
    {
      throw new Exception("La relación entre columna y orden no es correcta");
    }

    $config["ordering"] = [];

    for ($i = 0; $i < count($columns); $i++)
    {
      $userEntity       = new UserEntity();
      $userAccessEntity = new UserAccessEntity();
      $userColumn       = $userEntity->getDatamapValue($columns[$i]);
      $userAccessColumn = $userAccessEntity->getDatamapValue($columns[$i]);

      if (!$userAccessColumn && !$userColumn)
      {
        throw new Exception("La columna {$columns[$i]} no es válida");
      }

      if (!$validation->check($orders[$i], "permit_empty|in_list[asc,desc,ASC,DESC]"))
      {
        throw new Exception("El valor de ordenamiento '{$orders[$i]}' no es vÃ¡lido");
      }

      $userAccessColumn && $column = "user_access." . $userAccessColumn;
      $userColumn && $column = "user." . $userColumn;

      $config["ordering"][] = [
        "column" => $column,
        "order"  => $orders[$i],
      ];
    }

    return true;
  }
}