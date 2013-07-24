<?php

class CRM_BoxOffice_Page_SubscriptionLookup {
  static function check_subscription_uses($subscription_event_id)
  {
    $participants = CRM_BoxOffice_BAO_Participant::find_all_for_subscription_event_id($subscription_event_id);
    $subscription_max_uses = CRM_BoxOffice_BAO_Event::get_subscription_max_uses($subscription_event_id);
    $subscription_uses = count($participants);
    if ($subscription_uses >= $subscription_max_uses)
    {
      $usage_info = "$subscription_uses times";
      if ($subscription_uses == 1)
      {
	$usage_info = "$subscription_uses time";
      }
      return array($subscription_uses, $subscription_max_uses, "You have already used your Flex Pass $usage_info which is the maximum for that Flex Pass.");
    }
    return array($subscription_uses, $subscription_max_uses, NULL);
  }

  static function generate_associated_price_objects($price_set_associations, $subscription_line_items)
  {
    $error_messages = array();
    $quantity_for_price_field_ids = array();
    $associated_price_objects = array();
    foreach ($price_set_associations as $price_set_association)
    {
      $associated_price_objects[] = array($price_set_association, NULL);
    }
    foreach ($subscription_line_items as $subscription_line_item) 
    {
      $found_line_item = FALSE;
      foreach ($associated_price_objects as &$associated_price_pair)
      {
	$price_set_association = $associated_price_pair[0];
	if ($subscription_line_item->price_field_id == $price_set_association->subscription_price_field_id)
	{
	  $associated_price_pair[1] = $subscription_line_item;
	  $found_line_item = TRUE;
	  break;
	}
      }
      if (!$found_line_item) 
      {
	$error_messages[] = "Couldn't find a price field in this event that has been associated with the subscription event price field with ID {$subscription_line_item->price_field_id}.";
      }
    }
    return array($associated_price_objects, $error_messages);
  }

  static function generate_subscription_price_fields($associated_price_objects)
  {
    $subscription_price_fields = array();
    foreach ($associated_price_objects as &$associated_price_pair)
    {
      $price_set_association = $associated_price_pair[0];
      $subscription_line_item = $associated_price_pair[1];
      $subscription_price_field = array
      (
	'id' => $price_set_association->allowed_event_price_field_id,
      );
      if ($subscription_line_item != NULL)
      {
	$subscription_price_field['quantity'] = $subscription_line_item->qty;
      }
      $subscription_price_fields[] = $subscription_price_field;
    }
    return $subscription_price_fields;
  }

  static function lookup() {
    $result = array();
    $error_messages = array();
    $subscription_email_address = $_REQUEST['subscription_email_address'];
    $allowed_event_id = $_REQUEST['allowed_event_id'];
    $price_set_associations = CRM_BoxOffice_BAO_PriceSetAssociation::find_all_for_event_id($allowed_event_id);
    if (count($price_set_associations) == 0)
    {
      $error_messages[] = "It looks like this event wasn't configured correctly because I can't find any matching price fields for your subscription for this event.";
    }
    if (empty($error_messages))
    {
      list($subscription_participant, $subscription_line_items, $error_message) = CRM_BoxOffice_BAO_SubscriptionAllowance::find_line_items_by_email_address_and_allowed_event_id($subscription_email_address, $allowed_event_id);
      if ($subscription_participant == NULL)
      {
	$error_messages[] = $error_message;
      }
      else
      {
	$result['subscription_participant_id'] = $subscription_participant->id;
      }
    }
    if (empty($error_messages) && $subscription_participant != NULL)
    {
      list($subscription_uses, $subscription_max_uses, $error_message) = static::check_subscription_uses($subscription_participant->event_id);
      if ($error_message != NULL)
      {
	$error_messages[] = $error_message;
      }
      $result['subscription_uses'] = $subscription_uses;
      $result['subscription_max_uses'] = $subscription_max_uses;
    }
    if (empty($error_messages) && $subscription_line_items != NULL)
    {
      list($associated_price_objects, $error_messages) = static::generate_associated_price_objects($price_set_associations, $subscription_line_items);
      $subscription_price_fields = static::generate_subscription_price_fields($associated_price_objects);
      if (empty($subscription_price_fields)) 
      {
	$error_messages[] = "Couldn't find any matching price fields in the subscription event.";
      }
      if (empty($error_messages))
      {
	$result['subscription_price_fields'] = $subscription_price_fields;
      }
    }
    if (!empty($error_messages))
    {
      $result['error'] = TRUE;
      $result['error_message'] = implode("\n<br>\n", $error_messages);
    }
    print(json_encode($result));
    CRM_Utils_System::civiExit();
  }
}
