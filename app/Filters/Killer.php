<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

class Killer implements FilterInterface
{

  public function before(RequestInterface $request, $arguments = null): void
  {
    // Para que el IDE no se queje xD
    $request   = $request;
    $response  = $response;
    $arguments = $arguments;
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
  {
    // Para que el IDE no se queje xD
    $request   = $request;
    $response  = $response;
    $arguments = $arguments;

    // Cerrar cualquier conexión no cerrada automáticamente
    Database::connect()->close();
  }
}