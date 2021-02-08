<?php

namespace App\Entities;

use App\Models\UserRoleModel;
use App\Models\UserAccessModel;
use CodeIgniter\I18n\Time;
use Config\Services;
use Exception;
use Throwable;

class UserEntity extends BaseEntity
{
  protected $attributes = [
    "user_id"             => null,
    "user_nickname"       => null,
    "user_password"       => null,
    "user_name"           => null,
    "user_surname"        => null,
    "user_active"         => null,
    "user_created_at"     => null,
    "user_updated_at"     => null,
    "user_inactivated_at" => null,
    "user_role_id"        => null,
  ];
  protected $datamap    = [
    "userId"            => "user_id",
    "userNickname"      => "user_nickname",
    "userPassword"      => "user_password",
    "userName"          => "user_name",
    "userSurname"       => "user_surname",
    "userActive"        => "user_active",
    "userCreatedAt"     => "user_created_at",
    "userUpdatedAt"     => "user_updated_at",
    "userInactivatedAt" => "user_inactivated_at",
    "userRoleId"        => "user_role_id",
  ];

  public function isActive(): bool
  {
    return $this->attributes["user_active"] == 'y';
  }

  public function setUserName(string $value): void
  {
    $this->attributes["user_name"] = esc($value);
  }

  public function setUserSurname(string $value): void
  {
    $this->attributes["user_surname"] = esc($value);
  }

  public function setUserPassword(string $plainPassword): void
  {
    if (empty(trim($plainPassword)))
    {
      throw new Exception("La contraseña no puede estar vacía");
    }

    $this->attributes["user_password"] = password_hash($plainPassword, PASSWORD_DEFAULT);
  }

  public function isCorrectPassword($plainPassword): bool
  {
    return password_verify($plainPassword, $this->attributes["user_password"]);
  }

  public function sessionEnd(): void
  {
    try
    {
      $session          = Services::session();
      $userAccessModel  = new UserAccessModel();
      $userAccessEntity = $userAccessModel->readSingleByDate(Time::now()->toDateTimeString());
      $userAccessModel->updateSingle($userAccessEntity);
    }
    catch (Throwable $th)
    {
      echo $th->getMessage();
    }
    finally
    {
      $session->destroy();
    }
  }

  public function sessionStart(): void
  {
    $session  = Services::session();
    $userRole = $this->getUserRole();
    $session->set($this->attributes);
    $session->set("user_role_access", $userRole->userRoleAccess);
    $session->set("user_role_name", $userRole->userRoleName);

    if (!$this->isLoggedToday())
    {
      $userAccessModel  = new UserAccessModel();
      $userAccessEntity = new UserAccessEntity();

      $userAccessEntity->userId = $this->attributes["user_id"];
      $userAccessModel->createSingle($userAccessEntity);
    }

    // Eliminar información innecesaria
    $session->remove("user_password");
    $session->remove("user_active");
    $session->remove("user_inactivated_at");
    $session->remove("user_role_id");
  }

  public function isLoggedToday(): bool
  {
    $userAccessModel = new UserAccessModel();
    return $userAccessModel->isLoggedToday($this->attributes["user_id"]);
  }

  public function getUserRole(): UserRoleEntity
  {
    $userRoleModel = new UserRoleModel();
    return $userRoleModel->readSingle($this->attributes["user_role_id"]);
  }
}