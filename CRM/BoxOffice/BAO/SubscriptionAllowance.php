<?php

class CRM_BoxOffice_BAO_SubscriptionAllowance extends CRM_BoxOffice_DAO_SubscriptionAllowance
{
  public $allowed_price_fields = NULL;
  public $price_set_associations = NULL;
  public $subscription_event = NULL;
  public $subscription_price_fields = NULL;

  public static function find_all_by_allowed_event_id($allowed_event_id)
  {
    $subscription_allowance = new CRM_BoxOffice_BAO_SubscriptionAllowance();
    $subscription_allowance->allowed_event_id = $allowed_event_id;
    $subscription_allowance->find();
    $subscription_allowances = array();
    while ($subscription_allowance->fetch())
    {
      $subscription_allowances[] = clone($subscription_allowance);
    }
    return $subscription_allowances;
  }

  public static function find_line_items_for_subscription_id($subscription_id)
  {
    $subscription_line_items = array();
    $line_item = new CRM_Price_BAO_LineItem();
    $line_item->entity_table = 'civicrm_participant';
    $line_item->entity_id = $subscription_id;
    $line_item->find(FALSE);
    while ($line_item->fetch())
      {
      $subscription_line_items[] = clone($line_item);
    }
    return $subscription_line_items;
  }

  public static function find_participant_by_participant_id_and_allowed_event_id($participant_id, $allowed_event_id)
  {
    $params = array
    (
      1 => array(self::counted_particpant_status_ids(), 'String'),
      2 => array($participant_id, 'Integer'),
    );
    $sql = <<<EOS
      SELECT
	subscription_participant.*
      FROM
	civicrm_participant AS allowed_event_participant
      JOIN
	civiboxoffice_subscription_allowances ON (allowed_event_participant.event_id = civiboxoffice_subscription_allowances.allowed_event_id)
      JOIN
	civicrm_participant AS subscription_participant ON (civiboxoffice_subscription_allowances.subscription_event_id = subscription_participant.event_id)
      WHERE
	subscription_participant.status_id IN (%1)
      AND
	subscription_participant.contact_id = allowed_event_participant.contact_id
      AND
	allowed_event_participant.id = %2
EOS;
    $participant = CRM_Core_DAO::executeQuery($sql, $params, 'CRM_Event_BAO_Participant');
    if ($participant->fetch())
    {
      return $participant;
    }
    return NULL;
  }

  function load_allowed_price_fields()
  {
    $this->allowed_price_fields = CRM_BoxOffice_BAO_PriceField::find_by_event_id($this->allowed_event_id);
  }

  function load_price_set_associations($submitted_values = array())
  {
    $this->load_subscription_price_fields();
    $existing_price_set_associations = CRM_BoxOffice_BAO_PriceSetAssociation::find_all_for_event_id($this->allowed_event_id);
    foreach ($this->subscription_price_fields as $price_field) {
      $price_set_association = CRM_BoxOffice_BAO_PriceSetAssociation::select_by_subscription_price_field_id($existing_price_set_associations, $price_field->id);
      if ($price_set_association == NULL) {
        $price_set_association = new CRM_BoxOffice_BAO_PriceSetAssociation();
        $price_set_association->event_id = $this->allowed_event_id;
        $price_set_association->subscription_price_field_id = $price_field->id;
      }
      $price_set_association->subscription_price_field = $price_field;
      if (!empty($submitted_values)) {
        $price_set_association->update_from_submitted_list($submitted_values[$this->id]);
      }
      $this->price_set_associations[] = $price_set_association;
    }
  }

  function load_subscription_event()
  {
    $this->subscription_event = new CRM_Event_BAO_Event();
    $this->subscription_event->id = $this->subscription_event_id;
    if (!$this->subscription_event->find(TRUE)) {
      throw new Exception("Unable to find event {$this->subscription_event->id} for subscription allowance {$this->id}.");
    }
  }

  function load_subscription_price_fields()
  {
    $this->subscription_price_fields = CRM_BoxOffice_BAO_PriceField::find_by_event_id($this->subscription_event_id);
  }

  public static function subscription_allowances_exist_for_allowed_event_id($allowed_event_id)
  {
    $subscription_allowance = new CRM_BoxOffice_BAO_SubscriptionAllowance();
    $subscription_allowance->allowed_event_id = $allowed_event_id;
    if ($subscription_allowance->find(TRUE))
    {
      return TRUE;
    }
    return FALSE;
  }

  public static function subscription_allowances_has_subscription_event_id($subscription_allowances, $subscription_event_id)
  {
    foreach ($subscription_allowances as $subscription_allowance)
    {
      if ($subscription_allowance->subscription_event_id == $subscription_event_id)
      {
	return TRUE;
      }
    }
    return FALSE;
  }

  public static function update_subscription_events_for_allowed_event_id($allowed_event_id, $subscription_event_ids)
  {
    $subscription_allowances = self::find_all_by_allowed_event_id($allowed_event_id);
    $allowances_to_remove = array();
    foreach ($subscription_allowances as $subscription_allowance)
    {
      if (!in_array($subscription_allowance->subscription_event_id, $subscription_event_ids))
      {
	if (!in_array($allowances_to_remove, $subscription_allowance))
	{
	  $allowances_to_remove[] = $subscription_allowance;
	}
      }
    }
    foreach($allowances_to_remove as $allowance_to_remove)
    {
      $allowance_to_remove->delete();
    }

    $missing_subscription_event_ids = array();
    foreach ($subscription_event_ids as $subscription_event_id)
    {
      if (!self::subscription_allowances_has_subscription_event_id($subscription_allowances, $subscription_event_id))
      {
	if (!in_array($subscription_event_id, $missing_subscription_event_ids))
	{
	  $missing_subscription_event_ids[] = $subscription_event_id;
	}
      }
    }
    foreach($missing_subscription_event_ids as $missing_subscription_event_id)
    {
      $subscription_allowance = new CRM_BoxOffice_BAO_SubscriptionAllowance();
      $subscription_allowance->subscription_event_id = $missing_subscription_event_id;
      $subscription_allowance->allowed_event_id = $allowed_event_id;
      $subscription_allowance->save();
    }
  }
}
