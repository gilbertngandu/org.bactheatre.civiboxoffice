<?php

class CRM_BoxOffice_Page_PriceFields
{
  static function price_fields()
  {
    $price_set_id = $_REQUEST['price_set_id'];
    $price_fields = CRM_BoxOffice_BAO_PriceSetAssociation::get_all_price_fields_for_price_set_id($price_set_id);
    $trimmed_price_fields = array();
    foreach ($price_fields as $price_field) {
      CRM_Core_DAO::storeValues($price_field, $trimmed_price_field);
      $trimmed_price_fields[] = $trimmed_price_field;
    }
    print(json_encode($trimmed_price_fields));
    CRM_Utils_System::civiExit();
  }
}
