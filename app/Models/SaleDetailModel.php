<?php

namespace App\Models;

use App\Entities\SaleDetailEntity;
use CodeIgniter\Model;
use CodeIgniter\I18n\Time;
use Config\Services;
use Exception;

class SaleDetailModel extends Model
{
  protected $table              = "sale_detail";
  protected $primaryKey         = "sale_detail_id";
  protected $returnType         = SaleDetailEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = true;
  protected $skipValidation     = false;
  protected $createdField       = '';
  protected $updatedField       = '';
  protected $deletedField       = '';
  protected $validationMessages = [];
  protected $allowedFields      = [
    "sale_detail_item_cost",
    "sale_detail_item_price",
    "sale_detail_stock_on_move",
    "sale_id",
    "item_id"
  ];
  protected $validationRules    = [
    "sale_detail_item_cost"     => ["label" => "costo de artículo", "rules" => "required|decimal"],
    "sale_detail_item_price"    => ["label" => "price de artículo", "rules" => "required|decimal"],
    "sale_detail_stock_on_move" => ["label" => "artículos en movimiento", "rules" => "required|integer"],
    "sale_id"                   => ["label" => "venta en los detalles", "rules" => "required|is_natural_no_zero"],
    "item_id"                   => ["label" => "artículo en los detalles", "rules" => "required|is_natural_no_zero"]
  ];

  public function createSingle(SaleDetailEntity &$saleDetailEntity): SaleDetailEntity
  {
    $this->set("sale_detail_item_cost", $saleDetailEntity->saleDetailItemCost);
    $this->set("sale_detail_item_price", $saleDetailEntity->saleDetailItemPrice);
    $this->set("sale_detail_stock_on_move", $saleDetailEntity->saleDetailStockOnMove);
    $this->set("sale_id", $saleDetailEntity->saleId);
    $this->set("item_id", $saleDetailEntity->itemId);

    if (!$this->insert()) {
      $errors = $this->errors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }
    return $this->readSingle($this->db->insertId());
  }

  public function readSingle(int $saleDetailId): SaleDetailEntity
  {
    $saleDetailEntity = $this->find($saleDetailId);
    $errors           = $this->errors();

    if (!empty($errors)) {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($saleDetailEntity, SaleDetailEntity::class)) {
      throw new Exception("El ID para leer detalles de venta no es válido");
    }

    return $saleDetailEntity;
  }

  public function listSaleDetailsBySaleSerial(string $saleSerial): array
  {
    // No es necesario paginar esta lista porque el tamaño máximo es de 100
    $selectQuery = ''
      . "item.item_id, "
      . "sale_detail.sale_detail_item_cost, "
      . "sale_detail.sale_detail_item_price, "
      . "sale_detail.sale_detail_stock_on_move, "
      . "sale.sale_canceled AS saleCanceled, "
      . "sale.sale_created_at AS saleCreatedAt, "
      . "item.item_code AS itemCode, "
      . "item.item_name AS itemName, "
      . "item.item_description AS itemDescription, "
      . "unit.unit_singular_name AS unitSingularName, "
      . "unit.unit_plural_name AS unitPluralName";
    $this->select($selectQuery);
    $this->join("sale", "sale.sale_id = sale_detail.sale_id");
    $this->join("item", "item.item_id = sale_detail.item_id");
    $this->join("unit", "unit.unit_id = item.unit_id");
    $this->where("sale.sale_serial", $saleSerial);

    $result = $this->findAll();
    $errors = $this->errors();

    if (!empty($errors)) {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (empty($result)) {
      throw new Exception("No hay detalles de venta con el serial: {$saleSerial}");
    }

    return $result;
  }

  public function readSalesGlobalSummary(array $config): object
  {
    $salesTotalCostQuery    = "SUM(sale_detail.sale_detail_item_cost * sale_detail.sale_detail_stock_on_move)";
    $salesTotalPriceQuery   = "SUM(sale_detail.sale_detail_item_price * sale_detail.sale_detail_stock_on_move)";
    $salesTotalEarningQuery = "{$salesTotalPriceQuery} - {$salesTotalCostQuery}";

    $this->buildFilterQuery($config);
    $this->select("IFNULL({$salesTotalCostQuery}, 0) AS salesTotalCost");
    $this->select("IFNULL({$salesTotalPriceQuery}, 0) AS salesTotalPrice");
    $this->select("IFNULL({$salesTotalEarningQuery}, 0) AS salesTotalEarning");
    $this->join("sale", "sale.sale_id = sale_detail.sale_id");
    $this->where("sale.sale_canceled", 'n');

    $result = $this->first();
    $errors = $this->errors();

    if (!empty($errors)) {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    return $result;
  }

  private function buildFilterQuery(array $config): void
  {
    $sdate  = $config["sdate"] ?? '';
    $fdate  = $config["fdate"] ?? '';
    $status = $config["status"] ?? '';
    $needle = $config["needle"] ?? '';

    $validation = Services::validation();
    $validation->setRules([
      "status" => ["label" => "tamaño de paginación", "rules" => "permit_empty|in_list[canceled,not_canceled]"],
      "sdate"  => ["label" => "fecha inicial", "rules" => "permit_empty|valid_date[Y-m-d]"],
      "fdate"  => ["label" => "fecha final", "rules" => "permit_empty|valid_date[Y-m-d]"],
    ]);

    if (!$validation->run($config)) {
      $errors = $validation->getErrors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    $this->groupStart();
    $this->like("sale.sale_serial", $needle);
    $this->orLike("sale.sale_cancel_note", $needle);
    $this->groupEnd();

    $status == "canceled" && $this->where("sale.sale_canceled", 'y');
    $status == "not_canceled" && $this->where("sale.sale_canceled", 'n');

    if (!empty($sdate) && !empty($fdate)) {
      if (Time::parse($sdate)->isAfter($fdate)) {
        throw new Exception("La fecha inicial no puede ser mayor a la fecha final");
      }
    }

    empty($sdate) || $this->where("sale.sale_created_at >=", Time::parse($sdate)->toDateTimeString());
    empty($fdate) || $this->where("sale.sale_created_at <", Time::parse($fdate)->addDays(1)->toDateTimeString());
  }
}