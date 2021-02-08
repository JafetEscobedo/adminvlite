<?php

namespace App\Entities;

class ItemHistoryEventEntity extends BaseEntity
{
  protected $attributes = [
    "item_history_event_id"     => null,
    "item_history_event_system" => null,
    "item_history_event_type"   => null,
    "item_history_event_name"   => null,
  ];
  protected $datamap    = [
    "itemHistoryEventId"     => "item_history_event_id",
    "itemHistoryEventSystem" => "item_history_event_system",
    "itemHistoryEventType"   => "item_history_event_type",
    "itemHistoryEventName"   => "item_history_event_name",
  ];
}