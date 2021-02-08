<?php

namespace App\Entities;

class ItemEntity extends BaseEntity
{
  protected $attributes         = [
    "item_id"             => null,
    "item_code"           => null,
    "item_name"           => null,
    "item_description"    => null,
    "item_cost"           => null,
    "item_price"          => null,
    "item_brand"          => null,
    "item_category"       => null,
    "item_active"         => null,
    "item_created_at"     => null,
    "item_updated_at"     => null,
    "item_inactivated_at" => null,
    "unit_id"             => null,
  ];
  protected $onlyReadAttributes = [
    "itemStock",
    "itemLowStock",
    "itemLastEntry",
    "itemLastEgress",
  ];
  protected $datamap            = [
    "itemId"            => "item_id",
    "itemCode"          => "item_code",
    "itemName"          => "item_name",
    "itemDescription"   => "item_description",
    "itemCost"          => "item_cost",
    "itemPrice"         => "item_price",
    "itemBrand"         => "item_brand",
    "itemCategory"      => "item_category",
    "itemActive"        => "item_active",
    "itemCreatedAt"     => "item_created_at",
    "itemUpdatedAt"     => "item_updated_at",
    "itemInactivatedAt" => "item_inactivated_at",
    "unitId"            => "unit_id",
  ];

  public function isActive(): bool
  {
    return $this->attributes["item_active"] == 'y';
  }

  public function setItemName(string $value): void
  {
    $this->attributes["item_name"] = esc(trim($value));
  }

  public function setItemDescription(string $value): void
  {
    $this->attributes["item_description"] = esc(trim($value));
  }

  public function setItemBrand(string $value): void
  {
    $this->attributes["item_brand"] = esc(trim($value));
  }

  public function setItemCategory(string $value): void
  {
    $this->attributes["item_category"] = esc(trim($value));
  }
}