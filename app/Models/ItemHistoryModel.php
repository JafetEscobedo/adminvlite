<?php

namespace App\Models;

use App\Entities\ItemHistoryEntity;
use App\Entities\ItemHistoryEventEntity;
use CodeIgniter\Model;
use Config\Services;
use Exception;

class ItemHistoryModel extends Model
{
  protected $table              = "item_history";
  protected $primaryKey         = "item_history_id";
  protected $returnType         = ItemHistoryEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = true;
  protected $skipValidation     = false;
  protected $createdField       = "item_history_created_at";
  protected $updatedField       = "";
  protected $deletedField       = "";
  protected $validationMessages = [];
  protected $allowedFields      = [
    "item_history_cost",
    "item_history_price",
    "item_history_stock_on_move",
    "item_history_new_stock",
    "item_history_note",
    "item_history_event_id",
    "item_id"
  ];
  protected $validationRules    = [
    "item_history_cost"          => ["label" => "costo del artículo", "rules" => "required|decimal"],
    "item_history_price"         => ["label" => "precio del artículo", "rules" => "required|decimal"],
    "item_history_stock_on_move" => ["label" => "stock en movimiento", "rules" => "required|integer"],
    "item_history_new_stock"     => ["label" => "nuevo stock del artículo", "rules" => "required|integer|greater_than_equal_to[0]"],
    "item_history_note"          => ["label" => "anotación del movimiento", "rules" => "permit_empty|max_length[65000]"],
    "item_history_event_id"      => ["label" => "evento realizado en historial", "rules" => "required|is_natural_no_zero"],
    "item_id"                    => ["label" => "artículo en historial", "rules" => "required|is_natural_no_zero"]
  ];

  public function createSingle(ItemHistoryEntity &$itemHistoryEntity): ItemHistoryEntity
  {
    $this->set("item_history_cost", $itemHistoryEntity->itemHistoryCost);
    $this->set("item_history_price", $itemHistoryEntity->itemHistoryPrice);
    $this->set("item_history_stock_on_move", $itemHistoryEntity->itemHistoryStockOnMove);
    $this->set("item_history_new_stock", $itemHistoryEntity->itemHistoryNewStock);
    $this->set("item_history_note", $itemHistoryEntity->itemHistoryNote);
    $this->set("item_history_event_id", $itemHistoryEntity->itemHistoryEventId);
    $this->set("item_id", $itemHistoryEntity->itemId);

    if (!$this->insert())
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    return $itemHistoryEntity;
  }

  public function createSingleUsingBatch(array $batch): array
  {
    foreach ($batch as $element)
    {
      $itemModel              = new ItemModel();
      $itemHistoryEntity      = new ItemHistoryEntity();
      $itemHistoryEventModel  = new ItemHistoryEventModel();
      $itemEntity             = $itemModel->readSingle($element->itemId);
      $itemHistoryEventEntity = $itemHistoryEventModel->readSingle($element->itemHistoryEventId ?? 1);
      $itemStock              = $this->readStockByItemId($element->itemId);

      if (abs($element->itemHistoryStockOnMove) == 0)
      {
        throw new Exception("Cantidad indicada para {$itemEntity->itemCode} - {$itemEntity->itemName} no puede ser 0");
      }

      // Validaciones según el tipo de movimiento
      switch ($itemHistoryEventEntity->itemHistoryEventType)
      {
        case "ingress":
          $itemHistoryEntity->itemHistoryStockOnMove = abs($element->itemHistoryStockOnMove);
          $itemHistoryEntity->itemHistoryEventId     = $itemHistoryEventEntity->itemHistoryEventId;
          break;
        case "egress":
          if (abs($element->itemHistoryStockOnMove) > $itemStock)
          {
            throw new Exception("{$itemEntity->itemCode} - {$itemEntity->itemName} sin existencias suficientes");
          }

          if (empty($element->itemHistoryNote))
          {
            throw new Exception("{$itemEntity->itemCode} - {$itemEntity->itemName} sin anotación especificada");
          }

          $itemHistoryEntity->itemHistoryEventId     = $element->itemHistoryEventId;
          $itemHistoryEntity->itemHistoryStockOnMove = abs($element->itemHistoryStockOnMove) * -1;
          break;
        default: throw new Exception("{$itemEntity->itemCode} - {$itemEntity->itemName} con movimiento no válido");
      }

      $itemHistoryEntity->itemId              = $element->itemId;
      $itemHistoryEntity->itemHistoryCost     = abs($element->itemCost) * abs($element->itemHistoryStockOnMove);
      $itemHistoryEntity->itemHistoryPrice    = abs($element->itemPrice) * abs($element->itemHistoryStockOnMove);
      $itemHistoryEntity->itemHistoryNewStock = $itemStock + $itemHistoryEntity->itemHistoryStockOnMove;
      $itemHistoryEntity->itemHistoryNote     = $element->itemHistoryNote;

      $itemEntity->itemCost  = $element->itemCost;
      $itemEntity->itemPrice = $element->itemPrice;

      if ($itemEntity->hasChanged("item_cost") || $itemEntity->hasChanged("item_price"))
      {
        $itemModel->updateSingle($itemEntity);
      }

      $this->createSingle($itemHistoryEntity);
    }

    return $batch;
  }

  public function readStockByItemId(int $itemId): int
  {
    $this->select("IFNULL(SUM(item_history_stock_on_move), 0) AS itemStock");
    $this->where("item_id", $itemId);
    $result = $this->get()->getRow()->itemStock;
    $errors = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    return $result;
  }

  public function readLastIngressByItemId(int $itemId): string
  {
    $this->select("item_history.item_history_created_at");
    $this->join("item_history_event", "item_history.item_history_event_id = item_history_event.item_history_event_id");
    $this->where("item_history.item_id", $itemId);
    $this->where("item_history_event.item_history_event_type", "ingress");
    $this->where("item_history_event.item_history_event_system", 'y');
    $this->orderBy("item_history.item_history_created_at", "DESC");
    $itemHistoryEntity = $this->first();

    $errors = $this->errors();
    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($itemHistoryEntity, ItemHistoryEntity::class))
    {
      return '';
    }

    return $itemHistoryEntity->itemHistoryCreatedAt;
  }

  public function readLastEgressByItemId(int $itemId): string
  {
    $this->select("item_history.item_history_created_at");
    $this->join("item_history_event", "item_history.item_history_event_id = item_history_event.item_history_event_id");
    $this->where("item_history.item_id", $itemId);
    $this->where("item_history_event.item_history_event_type", "egress");
    $this->where("item_history_event.item_history_event_system", 'n');
    $this->orderBy("item_history.item_history_created_at", "DESC");
    $itemHistoryEntity = $this->first();

    $errors = $this->errors();
    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($itemHistoryEntity, ItemHistoryEntity::class))
    {
      return '';
    }

    return $itemHistoryEntity->itemHistoryCreatedAt;
  }

  public function listByItemId(int $itemId, array $config): object
  {
    $this->validateListPagination($config);

    $this->buildSelectQuery($config);
    $this->where("item_history.item_id", $itemId);
    $total    = $this->countAllResults();
    $errors[] = $this->errors();

    $this->buildSelectQuery($config);
    $this->buildFilterQuery($config);
    $this->where("item_history.item_id", $itemId);
    $filtered = $this->countAllResults();
    $errors[] = $this->errors();

    $this->buildSelectQuery($config);
    $this->buildFilterQuery($config);
    $this->where("item_history.item_id", $itemId);
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
    $this->select("item_history.*");
    $this->select("item_history_event.item_history_event_type AS itemHistoryEventType");
    $this->select("item_history_event.item_history_event_name AS itemHistoryEventName");
    $this->join("item_history_event", "item_history.item_history_event_id = item_history_event.item_history_event_id");

    foreach ($config["ordering"] as $ordering)
    {
      $this->orderBy($ordering["column"], mb_strtoupper($ordering["order"]));
    }
  }

  private function buildFilterQuery(array $config): void
  {
    $this->groupStart()
      ->like("item_history.item_history_cost", $config["needle"])
      ->orLike("item_history.item_history_price", $config["needle"])
      ->orLike("item_history_event.item_history_event_type", $config["needle"])
      ->orLike("item_history_event.item_history_event_name", $config["needle"]);
    $this->groupEnd();
  }

  private function validateListPagination(array &$config): array
  {
    $validation = Services::validation();
    $config     = [
      "offset" => $config["offset"] ?? 0,
      "limit"  => $config["limit"] ?? 10,
      "column" => $config["column"] ?? "itemHistoryCreatedAt",
      "order"  => $config["order"] ?? "ASC",
      "needle" => $config["needle"] ?? ''
    ];

    $validation->setRules([
      "offset" => ["label" => "inicio de paginación", "rules" => "greater_than_equal_to[0]"],
      "limit"  => ["label" => "tamaño de paginación", "rules" => "greater_than[0]|less_than_equal_to[100]"]
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
      $itemHistoryEntity      = new ItemHistoryEntity();
      $itemHistoryEventEntity = new ItemHistoryEventEntity();

      $itemHistoryColumn      = $itemHistoryEntity->getDatamapValue($columns[$i]);
      $itemHistoryEventColumn = $itemHistoryEventEntity->getDatamapValue($columns[$i]);

      if (!$itemHistoryColumn && !$itemHistoryEventColumn)
      {
        throw new Exception("La columna {$columns[$i]} no existe");
      }

      if (!$validation->check($orders[$i], "permit_empty|in_list[asc,desc,ASC,DESC]"))
      {
        throw new Exception("El valor de ordenamiento '{$orders[$i]}' no es válido");
      }

      $itemHistoryColumn && $column = "item_history." . $itemHistoryColumn;
      $itemHistoryEventColumn && $column = "item_history_event." . $itemHistoryEventColumn;

      $config["ordering"][] = [
        "column" => $column,
        "order"  => $orders[$i],
      ];
    }

    return $config;
  }
}