<?php

class CRM_BoxOffice_Page_SubscriptionLookup {
  static function lookup() {
    $error_messages = array();
    $subscription_email_address = $_REQUEST['subscription_email_address'];
    $allowed_event_id = $_REQUEST['allowed_event_id'];
    list($subscription_line_items, $error_message) = CRM_BoxOffice_BAO_SubscriptionAllowance::find_line_items_by_email_address_and_allowed_event_id($subscription_email_address, $allowed_event_id);
    if ($subscription_line_items != NULL)
    {
      $quantity_for_price_field_ids = array();
      $price_set_associations = CRM_BoxOffice_BAO_PriceSetAssociation::find_all_for_event_id($allowed_event_id);
      if (count($price_set_associations) == 0)
      {
	$error_messages[] = "It looks like this event wasn't configured correctly because I can't find any matching price fields for your subscription for this event.";
      }
      else
      {
	foreach ($subscription_line_items as $subscription_line_item) 
	{
	  $assocated_line_item = NULL;
	  foreach ($price_set_associations as $price_set_association)
	  {
	    if ($subscription_line_item->price_field_id == $price_set_association->subscription_price_field_id)
	    {
	      $associated_line_item = $subscription_line_item;
	    }
	  }
	  if ($associated_line_item == NULL)
	  {
	    $error_messages[] = "Unable to find a price field in the subscription event with the ID {$price_set_association->subscription_price_field_id}.";
	  }
	  else
	  {
	    $quantity_for_price_field_ids[$price_set_association->allowed_event_price_field_id] = $associated_line_item->qty;
	  }
	}
	if (empty($quantity_for_price_field_ids)) 
	{
	  $error_messages[] = "Couldn't find any matching price fields in the subscription event.";
	}
	if (empty($errors))
	{
	  $result['quantity_for_price_field_ids'] = $quantity_for_price_field_ids;
	}
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
