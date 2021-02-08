<?php

namespace App\Entities;

class ConfigEntity extends BaseEntity
{
  protected $attributes = [
    "config_id"               => null,
    "config_business_name"    => null,
    "config_business_name_uc" => null,
    "config_business_logo"    => null,
    "config_business_icon"    => null,
    "config_created_at"       => null,
    "config_updated_at"       => null
  ];
  protected $datamap    = [
    "configId"             => "config_id",
    "configBusinessName"   => "config_business_name",
    "configBusinessNameUc" => "config_business_name_uc",
    "configBusinessLogo"   => "config_business_logo",
    "configBusinessIcon"   => "config_business_icon",
    "configcreatedAt"      => "config_created_at",
    "configUpdatedAt"      => "config_updated_at"
  ];

  public function setConfigBusinessName(string $value): void
  {
    $this->attributes["config_business_name"] = esc($value);
  }
}