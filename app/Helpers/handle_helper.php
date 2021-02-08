<?php
if (!function_exists("handle_response")) {

  function handle_response(string $input): string
  {
    $decoded = json_decode($input);

    if (!$decoded) {
      return $input;
    }

    return implode("<br>", array_map(function ($element) {
        return trim($element, '.');
      }, (array) $decoded->data));
  }
}