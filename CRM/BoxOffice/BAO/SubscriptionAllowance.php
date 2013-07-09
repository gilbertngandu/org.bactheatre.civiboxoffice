<?php

class CRM_BoxOffice_BAO_SubscriptionAllowance extends CRM_BoxOffice_DAO_SubscriptionAllowance
{
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
