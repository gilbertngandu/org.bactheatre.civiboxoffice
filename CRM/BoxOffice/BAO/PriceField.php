<?php

class CRM_BoxOffice_BAO_PriceField
{
  static function find_by_event_id($event_id) {
    $price_fields = array();
    $params = array( 1 => array($event_id, 'Integer') );
    $sql = <<<EOF
      SELECT
        civicrm_price_field.*
      FROM
        civicrm_price_set_entity
      JOIN
        civicrm_price_field
      ON
       civicrm_price_field.price_set_id = civicrm_price_set_entity.price_set_id
      JOIN
        civicrm_event
      ON
        civicrm_event.id = civicrm_price_set_entity.entity_id
      WHERE
        entity_table = "civicrm_event" and entity_id = %1
EOF;
    $price_field = CRM_Core_DAO::executeQuery($sql, $params);
    while ($price_field->fetch()) {
      $price_fields[] = clone($price_field);
    }
    return $price_fields;
  }

  static function select_by_id($price_fields, $id) {
    foreach ($price_fields as $price_field) {
      if ($price_field->id == $id) {
        return $price_field;
      }
    }
    return NULL;
  }
}
