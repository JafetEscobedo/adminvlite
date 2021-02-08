<?php

namespace App\Entities;

use CodeIgniter\HTTP\RequestInterface;

class UserRoleEntity extends BaseEntity
{
  protected $attributes = [
    "user_role_id"             => null,
    "user_role_name"           => null,
    "user_role_access"         => null,
    "user_role_active"         => null,
    "user_role_created_at"     => null,
    "user_role_updated_at"     => null,
    "user_role_inactivated_at" => null
  ];
  protected $datamap    = [
    "userRoleId"            => "user_role_id",
    "userRoleName"          => "user_role_name",
    "userRoleAccess"        => "user_role_access",
    "userRoleActive"        => "user_role_active",
    "userRoleCreatedAt"     => "user_role_created_at",
    "userRoleUpdatedAt"     => "user_role_updated_at",
    "userRoleInactivatedAt" => "user_role_inactivated_at"
  ];

  public function setUserRoleName(string $value): void
  {
    $this->attributes["user_role_name"] = esc($value);
  }

  public function setUserRoleAccess(string $value): void
  {
    $this->attributes["user_role_access"] = esc($value);
  }

  public function getUserRoleAccessAsArray(): array
  {
    return json_decode($this->attributes["user_role_access"]);
  }

  public function isActive(): bool
  {
    return $this->attributes["user_role_active"] == 'y';
  }

  public function setUserRoleAccessFromRequest(RequestInterface $request, array $menus): UserRoleEntity
  {
    $menus  = array_merge_column($menus, "menu");
    $access = [];

    foreach ($menus as $menu)
    {
      $path = $request->getPost(md5($menu["path"]));

      if ($path)
      {
        $access[] = $path;
        continue;
      }

      foreach ($menu["menu"] ?? [] as $submenu)
      {
        $path = $request->getPost(md5($submenu["path"]));

        if (!in_array($menu["path"], $access))
        {
          $access[] = $menu["path"];
        }

        if ($path)
        {
          $access[] = $path;
        }
      }
    }

    $this->attributes["user_role_access"] = empty($access) ? '' : json_encode($access);
    return $this;
  }
}