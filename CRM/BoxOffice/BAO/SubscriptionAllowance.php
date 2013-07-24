<?php

class CRM_BoxOffice_BAO_SubscriptionAllowance extends CRM_BoxOffice_DAO_SubscriptionAllowance
{
  public static function counted_particpant_status_ids()
  {
    $counted_statuses = CRM_Event_PseudoConstant::participantStatus(NULL, 'is_counted = 1');
    return implode(', ', array_keys($counted_statuses));
  }

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

  public static function find_line_items_by_email_address_and_allowed_event_id($subscription_email_address, $allowed_event_id)
  {
    $params = array
    (
      1 => array(self::counted_particpant_status_ids(), 'String'),
      2 => array($allowed_event_id, 'Integer'),
      3 => array($subscription_email_address, 'String'),
    );
    $sql = <<<EOS
      SELECT
	civicrm_participant.id AS subscription_participant_id,
	civicrm_participant.event_id AS subscription_event_id
      FROM
	civiboxoffice_subscription_allowances
      JOIN
	civicrm_participant ON (civiboxoffice_subscription_allowances.subscription_event_id = civicrm_participant.event_id)
      JOIN
	civicrm_email ON (civicrm_participant.contact_id = civicrm_email.contact_id)
      WHERE
	civicrm_participant.status_id IN (%1)
      AND
	civiboxoffice_subscription_allowances.allowed_event_id = %2
      AND
	civicrm_email.email = %3
EOS;
    $dao = CRM_Core_DAO::executeQuery($sql, $params);
    $dao->fetch();
    if ($dao->N == 1) 
    {
      $subscription_event_id = $dao->subscription_event_id;
      $subscription_line_items = array();
      $line_item = new CRM_Price_BAO_LineItem();
      $line_item->entity_table = 'civicrm_participant';
      $line_item->entity_id = $dao->subscription_participant_id;
      $line_item->find(FALSE);
      while ($line_item->fetch())
      {
	$subscription_line_items[] = clone($line_item);
      }
      return array($subscription_event_id, $subscription_line_items, NULL);
    }
    elseif ($dao->N > 1)
    {
      return array(NULL, NULL, "Found more than one matching subscription. Please call BACT for assistance.");
    }
    else
    {
      return array(NULL, NULL, "Unable to find any subscriptions for '$subscription_email_address'");
    }
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
