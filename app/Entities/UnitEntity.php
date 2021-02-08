<?php

namespace App\Entities;

class UnitEntity extends BaseEntity
{
  protected $attributes = [
    "unit_id"             => null,
    "unit_singular_name"  => null,
    "unit_plural_name"    => null,
    "unit_active"         => null,
    "unit_created_at"     => null,
    "unit_updated_at"     => null,
    "unit_inactivated_at" => null,
  ];
  protected $datamap    = [
    "unitId"            => "unit_id",
    "unitSingularName"  => "unit_singular_name",
    "unitPluralName"    => "unit_plural_name",
    "unitActive"        => "unit_active",
    "unitCreatedAt"     => "unit_created_at",
    "unitUpdatedAt"     => "unit_updated_at",
    "unitInactivatedAt" => "unit_inactivated_at",
  ];

  public function isActive(): bool
  {
    return $this->attributes["unit_active"] == 'y';
  }

  public function setUnitSingularName(string $value): void
  {
    $this->attributes["unit_singular_name"] = strtolower(esc(trim($value)));
  }

  public function setUnitPluralName(string $value): void
  {
    $this->attributes["unit_plural_name"] = strtolower(esc(trim($value)));
  }
}