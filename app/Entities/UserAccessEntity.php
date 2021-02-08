<?php

namespace App\Entities;

class UserAccessEntity extends BaseEntity
{
  protected $attributes = [
    "user_access_id"    => null,
    "user_access_first" => null,
    "user_access_last"  => null,
    "user_id"           => null
  ];
  protected $datamap    = [
    "userAccessId"    => "user_access_id",
    "userAccessFirst" => "user_access_first",
    "userAccessLast"  => "user_access_last",
    "userId"          => "user_id"
  ];
}