<?php

namespace App\Entities;

use CodeIgniter\Entity;

class BaseEntity extends Entity
{

  public function getDatamapValue(string $key)
  {
    if (array_key_exists($key, $this->datamap))
    {
      return $this->datamap[$key];
    }

    return null;
  }

  public function isOnlyReadAttribute(string $attribute)
  {
    return in_array($attribute, $this->onlyReadAttributes);
  }
}