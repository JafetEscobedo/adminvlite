<?php

namespace App\Controllers\Sale;

use App\Controllers\BaseController;

class ViewController extends BaseController
{

  public function create(): string
  {
    return $this->template->render("sale/create");
  }

  public function cancel(): string
  {
    $data["saleSerial"] = $this->request->getGet("saleSerial");
    return $this->template->render("sale/cancel", $data);
  }

  public function salesList(): string
  {
    return $this->template->render("sale/sales_list");
  }
}