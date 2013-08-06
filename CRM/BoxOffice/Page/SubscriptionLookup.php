<?php

class CRM_BoxOffice_Page_SubscriptionLookup {
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
      foreach ($associated_price_objects as &$associated_price_pair)
      {
	$price_set_association = $associated_price_pair[0];
	if ($subscription_line_item->price_field_id == $price_set_association->subscription_price_field_id)
	{
	  $associated_price_pair[1] = $subscription_line_item;
	  break;
	}
      }
    }
    return array($associated_price_objects, $error_messages);
  }

  static function generate_seat_map($allowed_event_id, $qfkey, $category_type)
  {
    $allowed_event = new CRM_Event_BAO_Event();
    $allowed_event->id = $allowed_event_id;
    if (!$allowed_event->find(TRUE))
    {
      throw new Exception("Error finding allowed event $allowed_event_id.");
    }
    $place_map_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_for_civicrm_event_and_category_type($allowed_event, $category_type); 
    $sid = CRM_BoxOffice_FusionTicket_Seat::sid_from_qf($qfkey);
    CRM_BoxOffice_FusionTicket_Seat::cancel_for_sid($sid, $place_map_category);
    return CRM_BoxOffice_FusionTicket_PlaceMapCategory::draw($place_map_category);
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
	$subscription_price_field['subscription_participant_id'] = $subscription_line_item->entity_id;
      }
      $subscription_price_fields[] = $subscription_price_field;
    }
    return $subscription_price_fields;
  }

  static function get_subscription_uses($subscription_participant)
  {
    $participants = CRM_BoxOffice_BAO_Participant::find_all_for_subscription_participant($subscription_participant);
    $subscription_max_uses = CRM_BoxOffice_BAO_Event::get_subscription_max_uses($subscription_participant->event_id);
    $subscription_uses = count($participants);
    return array($subscription_uses, $subscription_max_uses, ($subscription_uses >= $subscription_max_uses));
  }

  static function lookup() {
    ft_security();
    $result = array();
    $error_messages = array();
    $subscriptions = array();
    $subscription_email_address = $_REQUEST['subscription_email_address'];
    $allowed_event_id = $_REQUEST['allowed_event_id'];
    $price_set_associations = CRM_BoxOffice_BAO_PriceSetAssociation::find_all_for_event_id($allowed_event_id);
    if (count($price_set_associations) == 0)
    {
      $error_messages[] = "It looks like this event wasn't configured correctly because I can't find any matching price fields for your subscription for this event.";
    }
    if (empty($error_messages))
    {
      $subscription_participants = CRM_BoxOffice_BAO_Participant::find_all_for_subscription_email_address_and_allowed_event_id($subscription_email_address, $allowed_event_id);
      if (empty($subscription_participants))
      {
	$error_messages[] = "We are sorry. That email address does not match any Flex Passes. If you think you are getting this message in error, please email tickets@bactheatre.org or call 510-296-4433.";
      }
    }
    if (empty($error_messages))
    {
      foreach ($subscription_participants as $subscription_participant)
      {
	$subscription_line_items = CRM_BoxOffice_BAO_SubscriptionAllowance::find_line_items_for_subscription_id($subscription_participant->id);
	list($subscription_uses, $subscription_max_uses, $exceeded_max) = static::get_subscription_uses($subscription_participant);
	list($associated_price_objects, $error_messages) = static::generate_associated_price_objects($price_set_associations, $subscription_line_items);
	if (empty($error_messages))
	{
	  $subscription_price_fields = static::generate_subscription_price_fields($associated_price_objects);
	  $pared_down_line_items = static::pare_down_subscription_line_items($subscription_line_items);
	  $subscription = array
	  (
	    'event_title' => $subscription_participant->event_title,
	    'line_items' => $pared_down_line_items,
	    'max_uses' => $subscription_max_uses,
	    'participant_id' => $subscription_participant->id,
	    'price_fields' => $subscription_price_fields,
	    'uses' => $subscription_uses,
	  );
	  $subscriptions[] = $subscription;
	}
	else
	{
	  $error_messages[] = "Couldn't find any matching price fields in the subscription event.";
	}
      }
    }
    if (empty($error_messages))
    {
      if (count($subscriptions) > 1)
      {
	$category_type = 'general_admission';
      }
      else
      {
	$category_type = 'subscription';
      }
      $result['seatmap'] =  static::generate_seat_map($allowed_event_id, $_REQUST['qfKey'], $category_type);
      $result['subscriptions'] = $subscriptions;
    }
    else
    {
      $result['error'] = TRUE;
      $result['error_message'] = implode("\n<br>\n", $error_messages);
    }
    print(json_encode($result));
    CRM_Utils_System::civiExit();
  }

  static function pare_down_subscription_line_items($subscription_line_items)
  {
    $pared_down_line_items = array();
    foreach ($subscription_line_items as $subscription_line_item)
    {
      $pared_down_line_items[] = array
      (
	'label' => $subscription_line_item->label,
	'qty' => $subscription_line_item->qty,
      );
    }
    return $pared_down_line_items;
  }

  static function seat_map()
  {
    ft_security();
    $seatmap = static::generate_seat_map($_REQUEST['allowed_event_id'], $_REQUST['qfKey'], $_REQUEST['category_type']);
    $result = array('error' => FALSE, 'seatmap' => $seatmap);
    print(json_encode($result));
    CRM_Utils_System::civiExit();
  }
}
