<?php

namespace App\Models;

use App\Entities\ItemEntity;
use App\Entities\UnitEntity;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use Config\Services;
use Exception;

class ItemModel extends Model
{
  protected $table              = "item";
  protected $primaryKey         = "item_id";
  protected $returnType         = ItemEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = true;
  protected $skipValidation     = false;
  protected $createdField       = "item_created_at";
  protected $updatedField       = "item_updated_at";
  protected $deletedField       = '';
  protected $validationMessages = [];
  protected $allowedFields      = [
    "item_code",
    "item_name",
    "item_description",
    "item_cost",
    "item_price",
    "item_brand",
    "item_category",
    "item_image",
    "item_active",
    "item_inactivated_at",
    "unit_id"
  ];
  protected $validationRules    = [
    "item_code"           => ["label" => "código de artículo", "rules" => "required|alpha_numeric|max_length[50]"],
    "item_name"           => ["label" => "nombre de artículo", "rules" => "required|max_length[50]"],
    "item_description"    => ["label" => "descripción de artículo", "rules" => "required|max_length[65000]"],
    "item_cost"           => ["label" => "costo de artículo", "rules" => "required|decimal"],
    "item_price"          => ["label" => "precio de artículo", "rules" => "required|decimal"],
    "item_brand"          => ["label" => "marca de artículo", "rules" => "required|max_length[50]"],
    "item_category"       => ["label" => "categoría de artículo", "rules" => "required|max_length[50]"],
    "item_active"         => ["label" => "estatus de arículo", "rules" => "required|in_list[y,n]"],
    "item_inactivated_at" => ["label" => "inactivado de artículo", "rules" => "permit_empty|valid_date[Y-m-d H:i:s]"],
    "unit_id"             => ["label" => "unidad de artículo", "rules" => "required|is_natural_no_zero"]
  ];

  public function createSingle(ItemEntity &$itemEntity): ItemEntity
  {
    $unitModel  = new UnitModel();
    $unitEntity = $unitModel->readSingle($itemEntity->unitId);

    if (!$unitEntity->isActive())
    {
      throw new Exception("La unidad no se puede asignar porque se encuentra inactiva");
    }

    $this->set("item_code", $itemEntity->itemCode);
    $this->set("item_name", $itemEntity->itemName);
    $this->set("item_description", $itemEntity->itemDescription);
    $this->set("item_cost", $itemEntity->itemCost);
    $this->set("item_price", $itemEntity->itemPrice);
    $this->set("item_brand", $itemEntity->itemBrand);
    $this->set("item_category", $itemEntity->itemCategory);
    $this->set("item_active", 'y');
    $this->set("unit_id", $itemEntity->unitId);

    if (!$this->insert())
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    $itemEntity = $this->readSingle($this->db->insertId());
    return $itemEntity;
  }

  public function readSingle(int $itemId): ItemEntity
  {
    $itemEntity = $this->find($itemId);
    $errors     = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($itemEntity, ItemEntity::class))
    {
      throw new Exception("No existe el artículo solicitado");
    }

    return $itemEntity;
  }

  public function updateSingle(ItemEntity &$itemEntity): ItemEntity
  {
    $unitModel        = new UnitModel();
    $itemHistoryModel = new ItemHistoryModel();

    $unitEntity             = $unitModel->readSingle($itemEntity->unitId);
    $baseEntity             = $this->readSingle($itemEntity->itemId);
    $baseEntity->itemActive = $itemEntity->itemActive;

    if (!$unitEntity->isActive())
    {
      throw new Exception("La unidad que intenta asignar está inactiva");
    }

    if ($baseEntity->hasChanged("item_active") && !$itemEntity->isActive())
    {
      if ($itemHistoryModel->readStockByItemId($itemEntity->itemId))
      {
        throw new Exception("No se puede inactivar un artículo con existencias");
      }

      $this->set("item_inactivated_at", Time::now()->toDateTimeString());
    }

    if ($baseEntity->hasChanged("item_active") && $itemEntity->isActive())
    {
      $this->set("item_inactivated_at", null);
    }

    $this->set("item_code", $itemEntity->itemCode ?? $baseEntity->itemCode);
    $this->set("item_name", $itemEntity->itemName ?? $baseEntity->itemName);
    $this->set("item_description", $itemEntity->itemDescription ?? $baseEntity->itemDescription);
    $this->set("item_cost", $itemEntity->itemCost ?? $baseEntity->itemCost);
    $this->set("item_price", $itemEntity->itemPrice ?? $baseEntity->itemPrice);
    $this->set("item_brand", $itemEntity->itemBrand ?? $baseEntity->itemBrand);
    $this->set("item_category", $itemEntity->itemCategory ?? $baseEntity->itemCategory);
    $this->set("item_active", $itemEntity->itemActive ?? $baseEntity->itemActive);
    $this->set("unit_id", $itemEntity->unitId ?? $baseEntity->unitId);

    if (!$this->update($itemEntity->itemId))
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    $itemEntity = $this->readSingle($itemEntity->itemId);
    return $itemEntity;
  }

  public function readSingleByItemCode(string $code): ItemEntity
  {
    $unitModel        = new UnitModel();
    $itemHistoryModel = new ItemHistoryModel();
    $itemEntity       = $this->where("item_code", $code)->first();
    $errors           = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($itemEntity, ItemEntity::class))
    {
      throw new Exception("No existe el artículo solicitado");
    }

    $unitEntity = $unitModel->readSingle($itemEntity->unitId);

    // Información adicional a retornar
    $itemEntity->itemStock        = $itemHistoryModel->readStockByItemId($itemEntity->itemId);
    $itemEntity->itemLastEntry    = $itemHistoryModel->readLastIngressByItemId($itemEntity->itemId);
    $itemEntity->itemLastEgress   = $itemHistoryModel->readLastEgressByItemId($itemEntity->itemId);
    $itemEntity->unitSingularName = $unitEntity->unitSingularName;
    $itemEntity->unitPluralName   = $unitEntity->unitPluralName;

    return $itemEntity;
  }

  public function listItems(array $config): object
  {
    $this->validateItemsListPagination($config);

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

  public function readItemsSummary(): object
  {
    $itemStockQuery = ''
      . "SELECT IFNULL(SUM(item_history.item_history_stock_on_move), 0) "
      . "FROM item_history WHERE item.item_id = item_history.item_id";

    $this->selectCount("*", "totalItems");
    $this->where("item_active", 'y');
    $totalItems = $this->get()->getRow()->totalItems;
    $errors[]   = $this->errors();

    $this->select("SUM(item.item_cost * ({$itemStockQuery})) AS totalCost");
    $this->where("item_active", 'y');
    $totalCost = $this->get()->getRow()->totalCost;
    $errors[]  = $this->errors();

    $this->select("SUM(item.item_price * ({$itemStockQuery})) AS totalPrice");
    $this->where("item_active", 'y');
    $totalPrice = $this->get()->getRow()->totalPrice;
    $errors[]   = $this->errors();

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
        "totalItems" => $totalItems,
        "totalCost"  => $totalCost,
        "totalPrice" => $totalPrice,
    ];
  }

  // Métodos privados
  private function buildSelectQuery(array $config): void
  {
    $itemStockQuery = ''
      . "SELECT item_history.item_history_new_stock FROM item_history "
      . "WHERE item.item_id = item_history.item_id "
      . "ORDER BY item_history.item_history_created_at DESC LIMIT 1 ";

    $itemLowStockQuery = ''
      . "SELECT CASE WHEN item_history.item_history_new_stock < 3 THEN 'y' ELSE 'n' END FROM item_history "
      . "WHERE item.item_id = item_history.item_id "
      . "ORDER BY item_history.item_history_created_at DESC LIMIT 1 ";

    $itemLastIngressQuery = ''
      . "SELECT item_history.item_history_created_at FROM item_history "
      . "JOIN item_history_event ON item_history.item_history_event_id = item_history_event.item_history_event_id "
      . "WHERE item_history_event.item_history_event_type = 'ingress' "
      . "AND item.item_id = item_history.item_id "
      . "ORDER BY item_history.item_history_created_at DESC LIMIT 1";

    $itemLastEgressQuery = ''
      . "SELECT item_history.item_history_created_at FROM item_history "
      . "JOIN item_history_event ON item_history.item_history_event_id = item_history_event.item_history_event_id "
      . "WHERE item_history_event.item_history_event_type = 'egress' "
      . "AND item.item_id = item_history.item_id "
      . "ORDER BY item_history.item_history_created_at DESC LIMIT 1";

    $this->select("item.*");
    $this->select("unit.unit_singular_name AS unitSingularName");
    $this->select("unit.unit_plural_name AS unitPluralName");
    $this->select("IFNULL(({$itemStockQuery}), 0) AS itemStock");
    $this->select("IFNULL(({$itemLowStockQuery}), 'y') AS itemLowStock");
    $this->select("IFNULL(($itemLastIngressQuery), '') AS itemLastIngress");
    $this->select("IFNULL(($itemLastEgressQuery), '') AS itemLastEgress");
    $this->join("unit", "item.unit_id = unit.unit_id");

    $config["status"] == "active" && $this->where("item.item_active", 'y');
    $config["status"] == "inactive" && $this->where("item.item_active", 'n');

    foreach ($config["ordering"] as $ordering)
    {
      $this->orderBy($ordering["column"], mb_strtoupper($ordering["order"]));
    }
  }

  private function buildFilterQuery(array $config): void
  {
    $this->groupStart();
    $this->like("item.item_code", $config["needle"]);
    $this->orLike("item.item_name", $config["needle"]);
    $this->orLike("item.item_cost", $config["needle"]);
    $this->orLike("item.item_price", $config["needle"]);
    $this->orLike("item.item_description", $config["needle"]);
    $this->orLike("item.item_brand", $config["needle"]);
    $this->orLike("item.item_category", $config["needle"]);
    $this->orLike("unit.unit_singular_name", $config["needle"]);
    $this->orLike("unit.unit_plural_name", $config["needle"]);
    $this->groupEnd();
  }

  private function validateItemsListPagination(array &$config): array
  {
    $validation = Services::validation();
    $config     = [
      "offset" => $config["offset"] ?? 0,
      "limit"  => $config["limit"] ?? 10,
      "column" => $config["column"] ?? "itemCreatedAt",
      "order"  => $config["order"] ?? "DESC",
      "status" => $config["status"] ?? '',
      "needle" => $config["needle"] ?? ''
    ];

    $validation->setRules([
      "offset" => ["label" => "inicio de paginación", "rules" => "greater_than_equal_to[0]"],
      "limit"  => ["label" => "tamaño de paginación", "rules" => "greater_than[0]|less_than_equal_to[100]"],
      "status" => ["label" => "estatus de artículo", "rules" => "permit_empty|in_list[active,inactive]"]
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
      $itemEntity = new ItemEntity();
      $unitEntity = new UnitEntity();

      $itemColumn           = $itemEntity->getDatamapValue($columns[$i]);
      $unitColumn           = $unitEntity->getDatamapValue($columns[$i]);
      $isTemporalItemColumn = $itemEntity->isOnlyReadAttribute($columns[$i]);

      if (!$itemColumn && !$unitColumn && !$isTemporalItemColumn)
      {
        throw new Exception("La columna {$columns[$i]} no es válida");
      }

      if (!$validation->check($orders[$i], "permit_empty|in_list[asc,desc,ASC,DESC]"))
      {
        throw new Exception("El valor de ordenamiento '{$orders[$i]}' no es válido");
      }

      $itemColumn && $column = "item." . $itemColumn;
      $unitColumn && $column = "unit." . $unitColumn;
      $isTemporalItemColumn && $column = $columns[$i];

      $config["ordering"][] = [
        "column" => $column,
        "order"  => $orders[$i],
      ];
    }

    return $config;
  }
}