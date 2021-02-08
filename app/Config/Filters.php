<?php

namespace Config;

use App\Filters\Login;
use App\Filters\Logged;
use App\Filters\Killer;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
  public $aliases = [
    "csrf"     => CSRF::class,
    "toolbar"  => DebugToolbar::class,
    "honeypot" => Honeypot::class,
    "login"    => Login::class,
    "logged"   => Logged::class,
    "killer"   => Killer::class
  ];
  public $globals = [
    "after"  => ["toolbar", "killer"],
    "before" => ["csrf", "login" => ["except" => ["offsite/view/login", "offsite/action/login"]]]
  ];
  public $methods = [];
  public $filters = ["logged" => ["before" => ["offsite/view/login", "offsite/action/login"]]];
}