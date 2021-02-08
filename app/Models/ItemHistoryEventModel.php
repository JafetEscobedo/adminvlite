<?php

namespace App\Models;

use App\Entities\ItemHistoryEventEntity;
use Config\Services;
use CodeIgniter\Model;

class ItemHistoryEventModel extends Model
{
  protected $table              = "item_history_event";
  protected $primaryKey         = "item_history_event_id";
  protected $returnType         = ItemHistoryEventEntity::class;
  protected $useSoftDeletes     = false;
  protected $useTimestamps      = false;
  protected $skipValidation     = false;
  protected $createdField       = '';
  protected $updatedField       = '';
  protected $deletedField       = '';
  protected $validationMessages = [];
  protected $allowedFields      = [];
  protected $validationRules    = [];

  public function readSingle(int $itemHistoryEventId): ItemHistoryEventEntity
  {
    $itemHistoryEventEntity = $this->find($itemHistoryEventId);
    $errors                 = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    if (!is_a($itemHistoryEventEntity, ItemHistoryEventEntity::class))
    {
      throw new Exception("El ID del evento proporcionado no es válido");
    }

    return $itemHistoryEventEntity;
  }

  public function listItemHistoryEvents(array $config): object
  {
    $this->validateListPagination($config);

    $config["system"] && $this->where("item_history_event_system", $config["system"]);
    $config["type"] && $this->where("item_history_event_type", $config["type"]);
    $this->orderBy("item_history_event_name", "ASC");
    $result = $this->findAll();
    $errors = $this->errors();

    if (!empty($errors))
    {
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    return (object) [
        "total"    => count($result),
        "filtered" => count($result),
        "data"     => $result
    ];
  }

  // Actualmente este modelo no necesita una paginación pero se considera para el futuro
  private function validateListPagination(array &$config): array
  {
    $validation = Services::validation();
    $config     = [
      "offset" => $config["offset"] ?? 0,
      "limit"  => $config["limit"] ?? 100,
      "column" => $config["column"] ?? "itemHistoryEventName",
      "order"  => $config["order"] ?? "ASC",
      "needle" => $config["needle"] ?? '',
      "type"   => $config["type"] ?? '',
      "system" => $config["system"] ?? ''
    ];

    $validation->setRules([
      "offset" => ["label" => "inicio de paginación", "rules" => "greater_than_equal_to[0]"],
      "limit"  => ["label" => "tamaño de paginación", "rules" => "greater_than[0]|less_than_equal_to[100]"],
      "type"   => ["label" => "tipo de evento", "rules" => "permit_empty|in_list[ingress,egress]"],
      "system" => ["label" => "evento de sistema", "rules" => "permit_empty|in_list[y,n]"]
    ]);

    if (!$validation->run($config))
    {
      $errors = $validation->getErrors();
      throw new Exception(json_encode([
          "type" => gettype($errors),
          "data" => $errors,
      ]));
    }

    return $config;
  }
}