<?php

require_once('fusionticket/includes/classes/class.router.php');
require_once('fusionticket/includes/classes/model.placemapcategory.php');
require_once('fusionticket/includes/shop_plugins/function.placemap-civicrm.php');

class CRM_BoxOffice_FusionTicket_PlaceMapCategory
{
  static function build_name($category_id)
  {
    $query = <<<EOS
      SELECT
	Ort.ort_name,
	PlaceMap2.pm_name,
	Category.category_name
      FROM
	Category
      LEFT JOIN
	PlaceMap2 ON (Category.category_pm_id = PlaceMap2.pm_id)
      LEFT JOIN 
	Ort ON (PlaceMap2.pm_ort_id = Ort.ort_id)
      WHERE
	Category.category_id = ?
EOS;
    $result = ShopDB::query($query, $category_id);
    if ($result === FALSE)
    {
      throw new Exception("Error in query trying to build name for category $category_id '$query': " . print_r(ShopDB::error(), TRUE));
    }
    $row = ShopDB::fetch_row($result);
    return implode(' -- ', $row);
  }


  static function categories_for_select()
  {
    $categories = array();
    $categories[] = array
    (
      'ort_name' => 'None',
      'pm_name' => 'None',
      'category_id' => 0,
      'category_name' => 'None',
    );
    $query = <<<EOS
      SELECT
	Category.category_id,
	Category.category_name,
	PlaceMap2.pm_id,
	Ort.ort_id,
	PlaceMap2.pm_ort_id,
	PlaceMap2.pm_name,
	Ort.ort_name
      FROM 
	Category
      LEFT JOIN
	PlaceMap2 ON Category.category_pm_id = PlaceMap2.pm_id
      LEFT JOIN
	Ort ON PlaceMap2.pm_ort_id = Ort.ort_id
      WHERE
	pm_event_id IS NULL
      ORDER
	BY ort_name
EOS;
    $result = ShopDB::query($query);
    if ($result === FALSE)
    {
      throw new Exception("Error in query trying to get seat map categories for select '$query': " . print_r(ShopDB::error(), TRUE));
    }
    while ($row = ShopDB::fetch_assoc($result))
    {
      $categories[] = array
      (
	'ort_name' => $row['ort_name'],
        'pm_name' => $row['pm_name'],
        'category_id' => $row['category_id'],
        'category_name' => $row['category_name'],
      );
    }
    return $categories;
  }

  static function draw($place_map_category)
  {
    $form_fields = <<<EOS
      <input id="ft_category_pmp_id" name="ft_category_pmp_id" value="{$place_map_category->category_pmp_id}" type="hidden">
      <input id="ft_category_ident" name="ft_category_ident" value="{$place_map_category->category_ident}" type="hidden">
      <input id="ft_category_numbering" name="ft_category_numbering" value="{$place_map_category->category_numbering}" type="hidden">
      <input id="ft_category_event_id" name="ft_category_event_id" value="{$place_map_category->category_event_id}" type="hidden">
      <input id="ft_category_id" name="ft_category_id" value="{$place_map_category->category_id}" type="hidden">
EOS;
    return $form_fields . placeMapDraw((array) $place_map_category);
  }

  static function find_by_category_and_event($category, $event)
  {
    $query = <<<EOS
      SELECT
	Category.*
      FROM
	Category
      WHERE
	category_event_id = ?
      AND
	category_ident = ?
EOS;
    $result = ShopDB::query($query, $event->id, $category->category_ident);
    if ($result === FALSE)
    {
      throw new Exception("Error finding place map category for category_event_id {$event->id} and category_ident {$category->category_ident}.");
    }
    if (ShopDB::num_rows($result) != 1)
    {
      throw new Exception("There should be exactly one place map category for category_event_id {$event->id} and category_ident {$category->category_ident}.");
    }
    $row = ShopDB::fetch_assoc($result);
    $place_map_category = new PlaceMapCategory();
    $place_map_category->_fill($row);
    return $place_map_category;
  }

  static function find_for_civicrm_event_and_category_type($event, $category_type)
  {
    $place_map_category = NULL;
    if ($category_type == 'general_admission' && $event->fusionticket_general_admission_category_id != NULL) 
    {
      $place_map_category = PlaceMapCategory::load($event->fusionticket_general_admission_category_id);
    } 
    elseif ($category_type == 'subscription' && $event->fusionticket_subscription_category_id != NULL)
    {
      $place_map_category = PlaceMapCategory::load($event->fusionticket_subscription_category_id);
    }
    return $place_map_category;
  } 

  static function find_for_civicrm_event_id_and_category_type($event_id, $category_type)
  {
    $event = new CRM_Event_BAO_Event();
    $event->id = $event_id;
    if (!$event->find(TRUE))
    {
      throw new Exception("Couldn't find event with id {$event_id} in order to find place map category.");
    }
    return static::find_for_civicrm_event_and_category_type($event, $category_type);
  }


  static function find_and_include_pm_ort_id($category_id)
  {
    $query =<<<EOS
      SELECT 
	Category.*,
	PlaceMap2.pm_ort_id
      FROM
	Category
      LEFT JOIN
	PlaceMap2 ON (Category.category_pm_id = PlaceMap2.pm_id)
      WHERE
	category_id=?
EOS;
    $result = ShopDB::query($query, $category_id);
    if ($result === FALSE)
    {
      throw new Exception("Error in query trying to get find category $category_id including pm_ort_id '$query': " . print_r(ShopDB::error(), TRUE));
    }
    if (ShopDB::num_rows($result) == 0)
    {
      throw new Exception("Didn't find any Category with id $category_id when trying to find category including pm_ort_id.");
    }
    $row = ShopDB::fetch_assoc($result);
    $place_map_category = new PlaceMapCategory();
    $place_map_category->_fill($row);
    return $place_map_category;
  }


}
