<?php
defined('BASEPATH') or exit('No direct script access allowed');

function ReplaceBatch($table, $array)
{

  if (!function_exists('array_key_first')) {
    function array_key_first(array $arr)
    {
      foreach ($arr as $key => $unused) {
        return $key;
      }
      return NULL;
    }
  }


  if (count($array) > 0) {
    $keys = array_keys($array[array_key_first($array)]);
    $insertKeys = implode(', ', $keys);

    $values = array_map(function ($item) {
      return '(' . implode(', ', array_values($item)) . ')';
    }, $array);
    $insertValues = implode(', ', $values);

    return "REPLACE INTO {$table} ({$insertKeys}) VALUES {$insertValues}";
  }

  return '';
}
