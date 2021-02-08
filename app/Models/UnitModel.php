<?php

namespace App\Models;

use App\Entities\UnitEntity;
use Config\Services;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use Exception;

class UnitModel extends Model
{
  protected $table              = "unit";
  protected $primaryKey         = "unit_id";
  protected $returnType         = UnitEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = true;
  protected $skipValidation     = false;
  protected $createdField       = "unit_created_at";
  protected $updatedField       = "unit_updated_at";
  protected $deletedField       = '';
  protected $validationMessages = [];
  protected $allowedFields      = [
    "unit_singular_name",
    "unit_plural_name",
    "unit_active",
    "unit_inactivated_at"
  ];
  protected $validationRules    = [
    "unit_singular_name"  => ["label" => "nombre singular de la unidad", "rules" => "required|max_length[50]"],
    "unit_plural_name"    => ["label" => "nombre plural de la unidad", "rules" => "required|max_length[50]"],
    "unit_active"         => ["label" => "estatus de la unidad", "rules" => "required|in_list[y,n]"],
    "unit_inactivated_at" => ["label" => "inactivado de la unidad", "rules" => "permit_empty|valid_date[Y-m-d H:i:s]"]
  ];

  public function createSingle(UnitEntity &$unitEntity): UnitEntity
  {
    $this->set("unit_singular_name", $unitEntity->unitSingularName);
    $this->set("unit_plural_name", $unitEntity->unitPluralName);
    $this->set("unit_active", 'y');

    if (!$this->insert())
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    $unitEntity = $this->readSingle($this->db->insertId());
    return $unitEntity;
  }

  public function readSingle(?int $unitId): UnitEntity
  {
    $unitEntity = $this->find($unitId);
    $errors     = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($unitEntity, UnitEntity::class))
    {
      throw new Exception("No existe la unidad solicitada");
    }

    return $unitEntity;
  }

  public function updateSingle(UnitEntity &$unitEntity): unitEntity
  {
    $baseEntity             = $this->readSingle($unitEntity->unitId);
    $baseEntity->unitActive = $unitEntity->unitActive;

    if ($baseEntity->hasChanged("unit_active"))
    {
      if ($baseEntity->isActive())
      {
        $this->set("unit_inactivated_at", null);
      }
      else
      {
        $this->set("unit_inactivated_at", Time::now()->toDateTimeString());
      }
    }

    $this->set("unit_singular_name", $unitEntity->unitSingularName ?? $baseEntity->unitSingularName);
    $this->set("unit_plural_name", $unitEntity->unitPluralName ?? $baseEntity->unitPluralName);
    $this->set("unit_active", $unitEntity->unitActive ?? $baseEntity->unitActive);

    if (!$this->update($unitEntity->unitId))
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    $unitEntity = $this->readSingle($unitEntity->unitId);
    return $unitEntity;
  }

  public function listUnits(array $config): object
  {
    $this->validateUnitsListPagination($config);

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

    $config["status"] == "active" && $this->where("unit.unit_active", 'y');
    $config["status"] == "inactive" && $this->where("unit.unit_active", 'n');
  }

  private function buildFilterQuery(array $config): void
  {
    $this->groupStart();
    $this->like("unit.unit_singular_name", $config["needle"]);
    $this->orLike("unit.unit_plural_name", $config["needle"]);
    $this->groupEnd();
  }

  private function validateUnitsListPagination(array &$config): array
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
      $unitEntity = new UnitEntity();
      $unitColumn = $unitEntity->getDatamapValue($columns[$i]);

      if (!$unitColumn)
      {
        throw new Exception("La columna {$columns[$i]} no es válida");
      }

      if (!$validation->check($orders[$i], "permit_empty|in_list[asc,desc,ASC,DESC]"))
      {
        throw new Exception("El valor de ordenamiento '{$orders[$i]}' no es válido");
      }

      $unitColumn && $column = "unit." . $unitColumn;

      $config["ordering"][] = [
        "column" => $column,
        "order"  => $orders[$i],
      ];
    }

    return $config;
  }
}