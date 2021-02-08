<?php

namespace App\Entities;

class SaleDetailEntity extends BaseEntity
{
  protected $attributes = [
    "sale_detail_id"            => null,
    "sale_detail_item_cost"     => null,
    "sale_detail_item_price"    => null,
    "sale_detail_stock_on_move" => null,
    "sale_id"                   => null,
    "item_id"                   => null,
  ];
  protected $datamap    = [
    "saleDetailId"          => "sale_detail_id",
    "saleDetailItemCost"    => "sale_detail_item_cost",
    "saleDetailItemPrice"   => "sale_detail_item_price",
    "saleDetailStockOnMove" => "sale_detail_stock_on_move",
    "saleId"                => "sale_id",
    "itemId"                => "item_id",
  ];
}