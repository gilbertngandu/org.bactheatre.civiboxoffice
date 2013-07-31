<?php

class CRM_BoxOffice_Utils
{
  public static function escape($data, $type)
  {
    if ($data === NULL)
    {
      return 'NULL';
    }
    else
    {
      return CRM_Utils_Type::escape($data, $type);
    }
  }
}
