<?php
if (!function_exists("array_merge_column")) {

  function array_merge_column(array $array, string $key): array
  {
    return array_reduce($array, function (array $carry, array $item) use ($key): array {
      return array_merge($carry, $item[$key]);
    }, []);
  }
}