<?php

namespace App\Models;

use App\Entities\ItemHistoryEntity;
use App\Entities\SaleDetailEntity;
use App\Entities\SaleEntity;
use App\Entities\UserEntity;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use Config\Services;
use Exception;

class SaleModel extends Model
{
  protected $table              = "sale";
  protected $primaryKey         = "sale_id";
  protected $returnType         = SaleEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = true;
  protected $skipValidation     = false;
  protected $createdField       = "sale_created_at";
  protected $updatedField       = '';
  protected $deletedField       = '';
  protected $validationMessages = [];
  protected $allowedFields      = [
    "sale_serial",
    "sale_canceled",
    "sale_cancel_note",
    "sale_created_at",
    "sale_canceled_at",
    "user_id"
  ];
  protected $validationRules    = [
    "sale_serial"      => ["label" => "número de venta", "rules" => "required|alpha_numeric"],
    "sale_canceled"    => ["label" => "estatus de venta", "rules" => "in_list[n,y]"],
    "sale_cancel_note" => ["label" => "nota de cancelación de venta", "rules" => "permit_empty|max_length[65000]"],
    "sale_canceled_at" => ["label" => "cancelación de venta", "rules" => "permit_empty|valid_date[Y-m-d H:i:s]"],
    "user_id"          => ["label" => "usuario de venta", "rules" => "required|is_natural_no_zero"]
  ];

  public function listSales(array $config): object
  {
    $this->validateSalesListPagination($config);

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
    $this->like("sale.sale_serial", $config["needle"]);
    $this->orLike("sale.sale_cancel_note", $config["needle"]);
    $this->groupEnd();

    $config["status"] === "canceled" && $this->where("sale.sale_canceled", 'y');
    $config["status"] === "not_canceled" && $this->where("sale.sale_canceled", 'n');

    if (!empty($sdate) && !empty($fdate))
    {
      if (Time::parse($sdate)->isAfter($fdate))
      {
        throw new Exception("La fecha inicial no puede ser mayor a la fecha final");
      }
    }

    empty($sdate) || $this->where("sale.sale_created_at >=", Time::parse($sdate)->toDateTimeString());
    empty($fdate) || $this->where("sale.sale_created_at <", Time::parse($fdate)->addDays(1)->toDateTimeString());
  }

  private function buildSelectQuery(array $config): void
  {
    $saleTotalPriceQuery = ''
      . "SELECT SUM(sale_detail.sale_detail_item_price * sale_detail.sale_detail_stock_on_move) "
      . "FROM sale_detail "
      . "WHERE sale_detail.sale_id = sale.sale_id";

    $saleTotalCostQuery = ''
      . "SELECT SUM(sale_detail.sale_detail_item_cost * sale_detail.sale_detail_stock_on_move) "
      . "FROM sale_detail "
      . "WHERE sale_detail.sale_id = sale.sale_id";

    $this->select("sale.*");
    $this->select("user.user_nickname AS userNickname");
    $this->select("(CONCAT_WS(' ', user.user_name, user.user_surname)) AS userFullName");
    $this->select("({$saleTotalPriceQuery}) AS saleTotalPrice");
    $this->select("({$saleTotalCostQuery}) AS saleTotalCost");
    $this->select("(({$saleTotalPriceQuery}) - ({$saleTotalCostQuery})) AS saleTotalEarning");
    $this->join("user", "user.user_id = sale.user_id");

    foreach ($config["ordering"] as $ordering)
    {
      $this->orderBy($ordering["column"], mb_strtoupper($ordering["order"]));
    }
  }

  private function validateSalesListPagination(array &$config): array
  {
    $validation = Services::validation();

    $config = [
      "offset" => $config["offset"] ?? 0,
      "limit"  => $config["limit"] ?? 10,
      "column" => $config["column"] ?? "saleCreatedAt",
      "order"  => $config["order"] ?? "DESC",
      "needle" => $config["needle"] ?? '',
      "status" => $config["status"] ?? '',
      "sdate"  => $config["sdate"] ?? '',
      "fdate"  => $config["fdate"] ?? ''
    ];

    $validation->setRules([
      "offset" => ["label" => "inicio de paginación", "rules" => "greater_than_equal_to[0]"],
      "limit"  => ["label" => "tamaño de paginación", "rules" => "greater_than[0]|less_than_equal_to[100]"],
      "status" => ["label" => "tamaño de paginación", "rules" => "permit_empty|in_list[canceled,not_canceled]"],
      "sdate"  => ["label" => "fecha inicial", "rules" => "permit_empty|valid_date[Y-m-d]"],
      "fdate"  => ["label" => "fecha final", "rules" => "permit_empty|valid_date[Y-m-d]"]
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

    for ($i = 0; $i < count($columns); $i++)
    {
      $saleEntity       = new SaleEntity();
      $userEntity       = new UserEntity();
      $saleColumn       = $saleEntity->getDatamapValue($columns[$i]);
      $userColumn       = $userEntity->getDatamapValue($columns[$i]);
      $isTemporalColumn = $saleEntity->isOnlyReadAttribute($columns[$i]);

      if (!$saleColumn && !$userColumn && !$isTemporalColumn)
      {
        throw new Exception("La columna {$columns[$i]} no es válida");
      }

      if (!$validation->check($orders[$i], "permit_empty|in_list[asc,desc,ASC,DESC]"))
      {
        throw new Exception("El valor de ordenamiento '{$orders[$i]}' no es válido");
      }

      $saleColumn && $column = "sale." . $saleColumn;
      $userColumn && $column = "user." . $userColumn;
      $isTemporalColumn && $column = $columns[$i];

      $config["ordering"][] = [
        "column" => $column,
        "order"  => $orders[$i],
      ];
    }

    return $config;
  }

  private function createSingle(SaleEntity &$saleEntity): SaleEntity
  {
    $this->set("user_id", 1);
    $this->set("sale_serial", $saleEntity->saleSerial);
    $this->set("sale_canceled", 'n');

    if (!$this->insert())
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    $saleEntity = $this->readSingle($this->db->insertId());
    return $saleEntity;
  }

  public function readSingle(int $saleId): SaleEntity
  {
    $saleEntity = $this->find($saleId);
    $errors     = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    if (!is_a($saleEntity, SaleEntity::class))
    {
      throw new Exception("El ID de venta proporcionado no es válido");
    }

    return $saleEntity;
  }

  public function readSingleBySaleSerial(string $saleSerial): SaleEntity
  {
    $this->where("sale_serial", $saleSerial);
    $saleEntity = $this->first();
    $errors     = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    if (!is_a($saleEntity, SaleEntity::class))
    {
      throw new Exception("El número de venta proporcionado no es válido");
    }

    return $saleEntity;
  }

  private function updateSingle(SaleEntity &$saleEntity): SaleEntity
  {
    // Solo se actualiza un venta en una cancelación
    $baseEntity               = $this->readSingle($saleEntity->saleId);
    $baseEntity->saleCanceled = $saleEntity->saleCanceled;

    $this->set("sale_cancel_note", $saleEntity->saleCancelNote ?? $baseEntity->saleCancelNote);
    $this->set("sale_canceled", $saleEntity->saleCanceled ?? $baseEntity->saleCanceled);

    if ($baseEntity->hasChanged("sale_canceled") && $saleEntity->isCanceled())
    {
      $this->set("sale_canceled_at", Time::now()->toDateTimeString());
    }

    if ($baseEntity->hasChanged("sale_canceled") && !$saleEntity->isCanceled())
    {
      $this->set("sale_canceled_at", null);
    }

    if (!$this->update($saleEntity->saleId))
    {
      $errors = $this->errors();
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    $saleEntity = $this->readSingle($saleEntity->saleId);
    return $saleEntity;
  }

  public function createSingleAndDetails(array $data): SaleEntity
  {
    $saleEntity             = new SaleEntity();
    $saleEntity->saleSerial = $this->createNextSaleSerial();
    $this->createSingle($saleEntity);

    // Crear detalles de venta
    foreach ($data as $element)
    {
      $saleDetailModel  = new SaleDetailModel();
      $itemModel        = new ItemModel();
      $itemHistoryModel = new ItemHistoryModel();

      $saleDetailEntity  = new SaleDetailEntity();
      $itemHistoryEntity = new ItemHistoryEntity();

      $itemEntity      = $itemModel->readSingle($element->itemId);
      $itemStock       = $itemHistoryModel->readStockByItemId($itemEntity->itemId);
      $itemStockOnMove = abs((int) $element->itemHistoryStockOnMove);

      if ($itemStockOnMove === 0)
      {
        throw new Exception("Cantidad de venta para {$itemEntity->itemCode} - {$itemEntity->itemName} no puede ser 0");
      }

      if ($itemStockOnMove > $itemStock)
      {
        throw new Exception("{$itemEntity->itemCode} - {$itemEntity->itemName} sin existencias suficientes");
      }

      //Crear objeto detalles de venta
      $saleDetailEntity->saleDetailItemCost    = $itemEntity->itemCost;
      $saleDetailEntity->saleDetailItemPrice   = $itemEntity->itemPrice;
      $saleDetailEntity->saleDetailStockOnMove = $itemStockOnMove;
      $saleDetailEntity->saleId                = $saleEntity->saleId;
      $saleDetailEntity->itemId                = $itemEntity->itemId;
      $saleDetailModel->createSingle($saleDetailEntity);

      //Crear Objeto del historial de artículos
      $itemHistoryEntity->itemId                 = $itemEntity->itemId;
      $itemHistoryEntity->itemHistoryEventId     = 3; // Venta de artículo
      $itemHistoryEntity->itemHistoryCost        = $itemEntity->itemCost * $itemStockOnMove;
      $itemHistoryEntity->itemHistoryPrice       = $itemEntity->itemPrice * $itemStockOnMove;
      $itemHistoryEntity->itemHistoryStockOnMove = $itemStockOnMove * -1;
      $itemHistoryEntity->itemHistoryNewStock    = $itemStock - $itemStockOnMove;
      $itemHistoryEntity->itemHistoryNote        = "Venta de artículo realizada";
      $itemHistoryModel->createSingle($itemHistoryEntity);
    }

    return $saleEntity;
  }

  public function cancelSingle(SaleEntity &$saleEntity): SaleEntity
  {
    //La venta se cancela usando el número de venta (sale_serial)
    $baseEntity = $this->readSingleBySaleSerial($saleEntity->saleSerial);

    if ($baseEntity->isCanceled())
    {
      throw new Exception("La venta ya se encuentra cancelada");
    }

    if (empty($saleEntity->saleCancelNote))
    {
      throw new Exception("La nota de cancelación es obligatoria");
    }

    foreach ($baseEntity->getSaleDetails() as $saleDetail)
    {
      $itemHistoryModel  = new ItemHistoryModel();
      $itemHistoryEntity = new ItemHistoryEntity();
      $itemStock         = $itemHistoryModel->readStockByItemId($saleDetail->itemId);

      //Crear Objeto del historial de artículos
      $itemHistoryEntity->itemId                 = $saleDetail->itemId;
      $itemHistoryEntity->itemHistoryEventId     = 2; // Cancelación de venta
      $itemHistoryEntity->itemHistoryCost        = $saleDetail->saleDetailItemCost * $saleDetail->saleDetailStockOnMove;
      $itemHistoryEntity->itemHistoryPrice       = $saleDetail->saleDetailItemPrice * $saleDetail->saleDetailStockOnMove;
      $itemHistoryEntity->itemHistoryStockOnMove = $saleDetail->saleDetailStockOnMove;
      $itemHistoryEntity->itemHistoryNewStock    = $itemStock + $saleDetail->saleDetailStockOnMove;
      $itemHistoryEntity->itemHistoryNote        = $saleEntity->saleCancelNote;
      $itemHistoryModel->createSingle($itemHistoryEntity);
    }

    $saleEntity->saleId       = $baseEntity->saleId;
    $saleEntity->saleCanceled = 'y';
    $this->updateSingle($saleEntity);
    return $saleEntity;
  }

  private function createNextSaleSerial(): string
  {
    $result = $this->countAllResults();
    $errors = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
            "type" => gettype($errors),
            "data" => $errors,
      ]));
    }

    return str_pad($result + 1, 5, 0, STR_PAD_LEFT);
  }
}