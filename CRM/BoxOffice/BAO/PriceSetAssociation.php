<?php

class CRM_BoxOffice_BAO_PriceSetAssociation extends CRM_BoxOffice_DAO_PriceSetAssociation
{
  static function find_all_for_event_id($event_id)
  {
    $price_set_associations = array();
    $price_set_association = new CRM_BoxOffice_BAO_PriceSetAssociation();
    $price_set_association->event_id = $event_id;
    $price_set_association->find(FALSE);
    while ($price_set_association->fetch())
    {
      $price_set_associations[] = clone($price_set_association);
    }
    return $price_set_associations;
  }

  static function get_all_price_fields_for_price_set_id($price_set_id) {

    $params = array( 1 => array($price_set_id, 'Integer') );
    $sql = <<<EOF
      SELECT
        civicrm_price_set.title,
        civicrm_price_field.label,
        civicrm_price_field.id
      FROM
        civicrm_price_set
      JOIN
        civicrm_price_field
      ON
        civicrm_price_set.id = civicrm_price_field.price_set_id
      WHERE
         civicrm_price_set.id = %1
EOF;

      $result = CRM_Core_DAO::executeQuery($sql, $params);

      while ($result->fetch()) {
        $price_fields []= clone($result);
      }

      return $price_fields;
  }

  static function update_civiboxoffice_price_set_associations_table($event_id, $subscription_price_field_id, $allowed_event_price_field_id) {

    $params = array( 1 => array($event_id, 'Integer'),
                     2 => array($subscription_price_field_id, 'Integer'),
                     3 => array($allowed_event_price_field_id, 'Integer'),
                     );
    $sql = <<<EOF
      INSERT INTO
        civiboxoffice_price_set_associations
      (event_id, subscription_price_field_id, allowed_event_price_field_id)
      VALUES
      (%1, %2, %3)
EOF;

    $result = CRM_Core_DAO::executeQuery($sql, $params);
  }

}
