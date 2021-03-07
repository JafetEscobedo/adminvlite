<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Logged implements FilterInterface
{

  public function before(RequestInterface $request, $arguments = null) // : ResponseInterface | void
  {
    $arguments = $arguments;
    $session   = Services::session();
    $response  = Services::response();

    if ($session->has("user_id"))
    {
      if ($request->isAJAX())
      {
        return $response->setStatusCode(401)->setJSON([
            "ok"      => false,
            "message" => "Acción no permitida"
        ]);
      }

      return redirect("/");
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
  {
    // Para que el IDE no se queje xD
    $request   = $request;
    $response  = $response;
    $arguments = $arguments;

    // Hacer algo más aquí si fuera necesario
  }
}