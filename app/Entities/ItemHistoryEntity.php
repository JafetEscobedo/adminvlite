<?php

namespace App\Entities;

use Exception;

class ItemHistoryEntity extends BaseEntity
{
  protected $attributes = [
    "item_history_id"            => null,
    "item_history_cost"          => null,
    "item_history_price"         => null,
    "item_history_created_at"    => null,
    "item_history_stock_on_move" => null,
    "item_history_note"          => null,
    "item_history_new_stock"     => null,
    "event_inventory_id"         => null,
    "item_id"                    => null,
  ];
  protected $datamap    = [
    "itemHistoryId"          => "item_history_id",
    "itemHistoryCost"        => "item_history_cost",
    "itemHistoryPrice"       => "item_history_price",
    "itemHistoryCreatedAt"   => "item_history_created_at",
    "itemHistoryStockOnMove" => "item_history_stock_on_move",
    "itemHistoryNote"        => "item_history_note",
    "itemHistoryNewStock"    => "item_history_new_stock",
    "eventInventoryId"       => "event_inventory_id",
    "itemId"                 => "item_id",
  ];

  public function setItemHistoryNote(string $value): void
  {
    $this->attributes["item_history_note"] = strtolower(esc(trim($value)));
  }

  public function convertJsonStringToArray(string $string): array
  {
    if (!$decoded = json_decode($string))
    {
      throw new Exception("No se pudo decodificar los datos");
    }

    if (count($decoded) > 100)
    {
      throw new Exception("No se puede hacer más de 100 operaciones a la vez");
    }

    foreach ($decoded as $record)
    {
      $invalid = false;
      property_exists($record, "itemId") or $invalid = true;
      property_exists($record, "itemCost") or $invalid = true;
      property_exists($record, "itemPrice") or $invalid = true;
      property_exists($record, "itemHistoryStockOnMove") or $invalid = true;
      property_exists($record, "itemHistoryNote") or $invalid = true;
      property_exists($record, "itemHistoryEventId") or $invalid = true;

      if ($invalid)
      {
        throw new Exception("El string json no tiene un formato válido");
      }
    }

    return $decoded;
  }
}