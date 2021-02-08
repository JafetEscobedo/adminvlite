<?php

namespace App\Libraries;

use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;

if (!class_exists("Template")) {
  final class Template
  {
    private $name; // string
    private $data; // array

    public function __construct()
    {
      $this->data["templateFluid"] = false;
      $this->data["templateMenu"]  = [];
    }

    public function dataset(string $key, $value): Template
    {
      $this->data[$key] = $value;
      return $this;
    }

    public function select(string $name): Template
    {
      $this->name = $name;
      return $this;
    }

    public function render(string $viewPath = '', array $data = []): string
    {
      $session    = Services::session();
      $userAccess = json_decode($session->get("user_role_access"));

      foreach ($userAccess as $uri) {
        if (preg_match('/' . preg_quote($uri, '/') . '/', uri_string())) {
          $this->data["templateContent"] = $this->view($viewPath, $data);
          return view("_shared/template/{$this->name}", $this->data);
        }
      }

      throw PageNotFoundException::forPageNotFound();
    }

    private function view(string $path = '', array $data = []): string
    {
      if (!empty($path)) {
        return view($path, array_merge($this->data, $data));
      } else {
        return '';
      }
    }
  }
}