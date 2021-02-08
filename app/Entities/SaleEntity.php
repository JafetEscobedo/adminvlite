<?php

namespace App\Entities;

use App\Models\SaleDetailModel;
use Exception;

class SaleEntity extends BaseEntity
{
  protected $attributes         = [
    "sale_id"          => null,
    "sale_serial"      => null,
    "sale_canceled"    => null,
    "sale_cancel_note" => null,
    "sale_created_at"  => null,
    "sale_canceled_at" => null,
    "user_id"          => null
  ];
  protected $datamap            = [
    "saleId"         => "sale_id",
    "saleSerial"     => "sale_serial",
    "saleCanceled"   => "sale_canceled",
    "saleCancelNote" => "sale_cancel_note",
    "saleCreatedAt"  => "sale_created_at",
    "saleCanceledAt" => "sale_canceled_at",
    "userId"         => "userId",
  ];
  protected $onlyReadAttributes = [
    "saleTotalCost",
    "saleTotalPrice",
    "saleTotalEarning"
  ];

  public function setSaleCancelNote(string $value): void
  {
    $this->attributes["sale_cancel_note"] = esc(trim($value));
  }

  public function getSaleDetails(): array
  {
    $saleDetailModel = new SaleDetailModel();
    return $saleDetailModel->listSaleDetailsBySaleSerial($this->attributes["sale_serial"]);
  }

  public function isCanceled(): bool
  {
    return $this->attributes["sale_canceled"] == 'y';
  }

  public function convertJsonStringToArray(string $string): array
  {
    if (!$decoded = json_decode($string)) {
      throw new Exception("No se pudo decodificar los datos");
    }

    if (count($decoded) > 100) {
      throw new Exception("No se puede hacer más de 100 operaciones a la vez");
    }

    foreach ($decoded as $record) {
      $invalid = false;
      property_exists($record, "itemId") or $invalid = true;
      property_exists($record, "itemHistoryStockOnMove") or $invalid = true;
      property_exists($record, "saleCancelNote") or $invalid = true;
      property_exists($record, "saleSerial") or $invalid = true;

      if ($invalid) {
        throw new Exception("El string json no tiene un formato válido");
      }
    }

    return $decoded;
  }
}