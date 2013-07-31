<?php

require_once('fusionticket/includes/classes/model.placemapcategory.php');

class CRM_BoxOffice_FusionTicket_Category
{
  static function find_all_for_civicrm_event_id($civicrm_event_id)
  {
    $categories = array();
    $query = <<<EOS
    SELECT
      category_id,
      category_event_id,
      category_pmp_id,
      category_ident,
      category_numbering
    FROM
      Category
    WHERE
      civicrm_event_id = ?
EOS;
    $result = ShopDB::query($query, $civicrm_event_id);
    while ($row = ShopDB::fetch_assoc($result))
    {
      $category = new PlaceMapCategory();
      $category->_fill($row);
      $categories[] = $category;
    }
    return $categories;
  } 
}
