<?php

class CRM_BoxOffice_BAO_PriceSetAssociation extends CRM_BoxOffice_DAO_PriceSetAssociation
{
  public $subscription_price_field;

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

  static function find_or_create_for_event_id($event_id) {
    $price_set_associations = array();
    $existing_price_set_associations = static::find_all_for_event_id($event_id);
    $allowed_subscriptions = CRM_BoxOffice_BAO_SubscriptionAllowance::find_all_by_allowed_event_id($event->id);
    foreach ($allowed_subscriptions as $allowed_subscription) {
      $allowed_subscription->load_subscription_price_fields();
      foreach ($allowed_subscription->price_fields as $price_field) {
        $price_set_association = static::select_by_price_field_id($exisiting_price_set_associations, $price_field->id);
        if ($price_set_association == NULL) {
          $price_set_association = new CRM_BoxOffice_BAO_PriceSetAssociation();
          $price_set_association->subscription_event_id = $event_id;
          $price_set_association->subscription_price_field_id = $price_field->id;
        }
        $price_set_association->subscription_price_field = $price_field;
        $price_set_associations[] = $price_set_assocation;
      }
    }
    return $price_set_associations;
  }

  static function get_all_price_fields_for_price_set_id($price_set_id) {
    $price_set = new CRM_Price_BAO_Field();
    $price_set->price_set_id = $price_set_id;
    $price_set->find();
    $price_fields = array();
    while ($price_set->fetch()) {
      $price_fields[] = clone($price_set);
    }
    return $price_fields;
  }

  static function select_by_subscription_price_field_id($price_set_associations, $allowed_price_field_id) {
    foreach ($price_set_associations as $price_set_association) {
      if ($price_set_association->subscription_price_field_id == $allowed_price_field_id) {
        return $price_set_association;
      }
    }
    return NULL;
  }

  function update_from_submitted_list($submitted_list) {
    $this->allowed_event_price_field_id = NULL;
    foreach ($submitted_list as $submitted_item) {
      if ($this->subscription_price_field_id == $submitted_item['subscription_price_field_id']) {
        $this->allowed_event_price_field_id = $submitted_item['allowed_event_price_field_id'];
      }
    }
    if ($this->allowed_event_price_field_id) {
      $this->save();
    } else {
      if ($this->id != NULL) {
        $this->delete();
      }
    }
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
