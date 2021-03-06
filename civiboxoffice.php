<?php

define('CBO_EXTENSION_NAME', 'org.bactheatre.civiboxoffice');

require_once 'civiboxoffice.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function civiboxoffice_civicrm_config(&$config) {
  _civiboxoffice_civix_civicrm_config($config);
  // add FusionTicket css
  CRM_Core_Resources::singleton()->addStyleFile(CBO_EXTENSION_NAME, 'fusionticket/css/formatting-civicrm.css');
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function civiboxoffice_civicrm_xmlMenu(&$files) {
  _civiboxoffice_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function civiboxoffice_civicrm_install() {
  $result = _civiboxoffice_civix_civicrm_install();
  if (!$result) {
    return $result;
  }
  $sql_file_path = __DIR__ . DIRECTORY_SEPARATOR . 'civiboxoffice.sql';
  CRM_Utils_File::sourceSQLFile(CIVICRM_DSN, $sql_file_path);
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function civiboxoffice_civicrm_uninstall() {
  return _civiboxoffice_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function civiboxoffice_civicrm_enable() {
  return _civiboxoffice_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function civiboxoffice_civicrm_disable() {
  return _civiboxoffice_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function civiboxoffice_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civiboxoffice_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function civiboxoffice_civicrm_managed(&$entities) {
  return _civiboxoffice_civix_civicrm_managed($entities);
}

function ft_security() {
  if (! defined('ft_check')) {
    define('ft_check', 'civicrm');
  }
}

function civiboxoffice_civicrm_navigationMenu(&$params) {
  //  Add fusionticket admin link to civi Events menu
  foreach ($params as $index => $menuvals) {
    if ($menuvals['attributes']['name'] == 'Events') {
      $maxkey = max(array_keys($params[$index]['child']));
      $params[$index]['child'][$maxkey + 1] =
	array (
	  'attributes' => array (
	    'label'      => 'Manage Seat Maps',
	    'name'       => 'Manage Seat Maps',
	    'url'        => CRM_Core_Resources::singleton()->getUrl(CBO_EXTENSION_NAME, 'fusionticket/admin'),
	    'permission' => 'access CiviEvent,administer CiviCRM',
	    'operator'   => 'AND',
	    'separator'  => 1,
	    'parentID'   => $index,
	    'navID'      => $maxkey + 1,
	    'active'     => 1
	  ),
	  'child' => null
	);
    }
  }
}

function civiboxoffice_civicrm_buildForm($formName, &$form) {
  $hook_name = "civiboxoffice_civicrm_buildForm_$formName";
  if (function_exists($hook_name)) {
    $hook_name($formName, $form);
  }
}

function civiboxoffice_build_seat_selector($event_id, $form, $place_map_category) {
  $event = new CRM_Event_BAO_Event();
  $event->id = $event_id;
  if (!$event->find(TRUE)) {
    throw new Exception("Couldn't find event with ID {$event_id} to setup seat selection.");
  }
  $sid = CRM_BoxOffice_FusionTicket_Seat::sid_from_qf($form->controller->_key);
  CRM_BoxOffice_FusionTicket_Seat::cancel_for_sid($sid);
  $seatmap = CRM_BoxOffice_FusionTicket_PlaceMapCategory::draw($place_map_category);
  $form->assign('place_map_category', $place_map_category);
  $form->assign('seatMap', $seatmap);
  $form->assign('event_id', $event_id);
}

function civiboxoffice_event_allows_subscriptions($event_id) {
  return CRM_BoxOffice_BAO_SubscriptionAllowance::subscription_allowances_exist_for_allowed_event_id($event_id);
}

function civiboxoffice_subscription_covers_all_costs($form) {
  return TRUE;
}

function civiboxoffice_disable_payment_fields($form) {
  foreach ($form->_paymentFields as $payment_field_name => &$payment_field) {
    if ($payment_field['is_required']) {
      $payment_field['is_required'] = FALSE;
    }
  }
}

function civiboxoffice_disable_profile_fields($form) {
  foreach ($form->_fields as $field_name => &$field) {
    if ($field['groupTitle'] == 'Contact Information' && $field['is_required']) {
      $field['is_required'] = FALSE;
      foreach ($form->_required as $index => $field_name) {
	if ($field_name == $field['name']) {
	  unset($form->_required[$index]);
	}
      }
    }
  }
}

function civiboxoffice_add_subscription($form) {
  $event_id = $form->getVar('_eventId');
  if ($_POST) {
    if (civiboxoffice_event_allows_subscriptions($event_id) &&
        civiboxoffice_subscription_covers_all_costs($form))
    {
      civiboxoffice_disable_payment_fields($form);
    }
  } else {
    $snippet = CRM_Utils_Array::value('snippet', $_REQUEST);
    if (!$snippet) {
      return;
    }
  }
  if (civiboxoffice_event_allows_subscriptions($event_id))
  {
    $form->assign('event_id', $event_id);
    $form->assign('add_subscription_section', TRUE);
    $civiboxoffice_subscription_lookup_url = CRM_Core_Resources::singleton()->getUrl(CBO_EXTENSION_NAME, 'civiboxoffice/subscription_lookup');
    $form->assign('civiboxoffice_subscription_lookup_url', $civiboxoffice_subscription_lookup_url);
  }
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_Registration_Register($formName, &$form) {
  CRM_Core_Resources::singleton()->addScriptFile(CBO_EXTENSION_NAME, 'js/civiboxoffice.js');
  ft_security();
  civiboxoffice_add_subscription($form);
  $event_id = $form->getVar('_eventId');
  $snippet = CRM_Utils_Array::value('snippet', $_REQUEST);
  $place_map_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_for_civicrm_event_id_and_category_type($event_id, 'general_admission');
  $subscription_participant_id = $form->get('subscription_participant_id');
  $form->assign('subscription_email_address', $form->get('subscription_email_address'));
  if ($place_map_category != NULL) {
    if (!isset($snippet)) {
      $form->assign('hasSeatMap', TRUE);
      return;
    }
    civiboxoffice_build_seat_selector($event_id, $form, $place_map_category);
  }
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_Registration_Confirm($formName, &$form) {
  // get any seats on hold for this qfkey and assign them to form
  ft_security();
  require_once('fusionticket/includes/classes/class.router.php');
  require_once('fusionticket/includes/classes/model.seat.php');

  $seats = Seat::loadAllSid(substr($form->controller->_key, strlen($form->controller->_key) - 32));
  if ($seats) {
    $form->assign('show_seat_information', TRUE);
    $form->assign('seatInfo', CRM_BoxOffice_FusionTicket_Seat::to_info_array($seats));
  }
  $subscription_participant_id = $form->get('subscription_participant_id');
  if ($subscription_participant_id != NULL) {
    $subscription_participant = new CRM_Event_BAO_Participant();
    $subscription_participant->id = $subscription_participant_id;
    if (!$subscription_participant->find(TRUE))
    {
      throw new Exception("Unable to find subscription participant {$subscription_participant_id} to set contact id for flex pass usage.");
    }
    $form->_values['event']['is_monetary'] = FALSE;
    $form->assign('totalAmount', 0);
    $form->set('totalAmount', 0);
    $params = $form->get('params');
    $params[0]['amount'] = 0;
    $params[0]['participant_source'] = "Flex Pass $subscription_participant_id Used On {$form->_values['event']['title']} ({$form->_values['event']['id']})" ;
    $params[0]['contact_id'] = $subscription_participant->contact_id;
    $form->set('registerByID', $subscription_participant->contact_id);
    $form->set('params', $params);
    $form->_values['is_primary'] = FALSE;
  }
}

function civiboxoffice_assign_seat_information($formName, &$form)
{
  // get any seats for this participant and assign them to form
  ft_security();
  require_once('fusionticket/includes/classes/class.router.php');
  require_once('fusionticket/includes/classes/model.seat.php');
  $participantIDS = $form->getVar('_participantIDS');
  if ($participantIDS) {
    $partID = $participantIDS[0];
  }
  else {
    $partID = $_REQUEST['id'];
  }

  $seats = Seat::loadAllOrder($partID);
  if ($seats) {
    $form->assign('show_seat_information', TRUE);
    $form->assign('seatInfo', CRM_BoxOffice_FusionTicket_Seat::to_info_array($seats));
  }
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_Registration_ThankYou($formName, &$form) {
  civiboxoffice_assign_seat_information($formName, $form);
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_ParticipantView($formName, &$form) {
  civiboxoffice_assign_seat_information($formName, $form);
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_Participant($formName, &$form) {
  ft_security();
  $snippet = CRM_Utils_Array::value('snippet', $_REQUEST);
  if ($snippet != NULL) {
    return;
  }
  civiboxoffice_assign_seat_information($formName, $form);
  $participant_id = $form->_id;
  $participant = new CRM_Event_BAO_Participant();
  $participant->id = $participant_id;
  if (!$participant->find(TRUE)) {
    throw new Exception("Couldn't find partcipant with ID {$participant->id} to show seat map.");
  }
  $sid = CRM_BoxOffice_FusionTicket_Seat::sid_from_qf($form->controller->_key);
  $place_map_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_for_civicrm_event_id_and_category_type($participant->event_id, 'general_admission');
  $form->assign('place_map_category', $place_map_category);
  $subscription_place_map_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_for_civicrm_event_id_and_category_type($participant->event_id, 'subscription');
  $form->assign('subscription_place_map_category', $subscription_place_map_category);
  if ($place_map_category == NULL)
  {
    $place_map_category = $subscription_place_map_category;
  }
  if ($place_map_category != NULL)
  {
    CRM_Core_Resources::singleton()->addScriptFile(CBO_EXTENSION_NAME, 'js/civiboxoffice.js');
    $event = new CRM_Event_BAO_Event();
    $event->id = $participant->event_id;
    if (!$event->find(TRUE)) {
      throw new Exception("Couldn't find event with ID {$event_id} to setup seat selection.");
    }
    $seatmap = CRM_BoxOffice_FusionTicket_PlaceMapCategory::draw($place_map_category);
    $form->assign('place_map_category', $place_map_category);
    $form->assign('seatMap', $seatmap);
    $form->assign('event_id', $event->id);
  }
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_ManageEvent_EventInfo($formName, $form) {
  $snippet = CRM_Utils_Array::value('snippet', $_REQUEST);
  $component = CRM_Utils_Array::value('component', $_REQUEST);
  $form_event_id = $form->getVar('_entityId');
  $event_id = $form->_id;

  // add seatmap selector to event configuration page
  ft_security();
  require_once('fusionticket/includes/classes/class.router.php');

  if ($event_id != NULL)
  {
    $event = new CRM_Event_BAO_Event();
    $event->id = $event_id;
    if (!$event->find(TRUE))
    {
      throw new Exception("Couldn't find event {$event_id} in order to add fusionticket fields to event edit info page.");
    }

    if ($event->fusionticket_general_admission_category_id != NULL)
    {
      $general_admission_seat_map = CRM_BoxOffice_FusionTicket_PlaceMapCategory::build_name($event->fusionticket_general_admission_category_id);
      $form->assign('general_admission_seat_map', $general_admission_seat_map);
    }

    if ($event->fusionticket_subscription_category_id != NULL)
    {
      $subscription_seat_map = CRM_BoxOffice_FusionTicket_PlaceMapCategory::build_name($event->fusionticket_subscription_category_id);
      $form->assign('subscription_seat_map', $subscription_seat_map);
    }
  }

  if ($general_admission_seat_map == NULL || $subscription_seat_map == NULL)
  {
    $categories = CRM_BoxOffice_FusionTicket_PlaceMapCategory::categories_for_select();
    $form->assign('ft_categories', $categories);
  }

  if (isset($_POST['subscription_max_uses'])) {
    $form->assign('subscription_max_uses', $_POST['subscription_max_uses']);
  }

  if (($snippet == NULL && $form_event_id == NULL) || ($snippet && $component == 'event')) {
    $form->assign('show_civiboxoffice_staging_area', TRUE);
    if ($event_id == NULL)
      $subscription_allowances = array();
    else {
      $subscription_allowances = CRM_BoxOffice_BAO_SubscriptionAllowance::find_all_by_allowed_event_id($event_id);
    }
    $event = new CRM_Event_BAO_Event();
    $event->event_type_id = 8;
    $event->is_active = TRUE;
    $subscription_events = array();
    $event->find();
    while ($event->fetch()) {
      $subscription_event = clone($event);
      if (CRM_BoxOffice_BAO_SubscriptionAllowance::subscription_allowances_has_subscription_event_id($subscription_allowances, $subscription_event->id))
      {
	$subscription_event->civiboxoffice_allowed = true;
      }
      $subscription_events[] = $subscription_event;
    }
    $form->assign('subscription_events', $subscription_events);
    $event_id = $form_event_id;
    if ($event_id == NULL) {
      $event_id = $form->_id;
    }
    if ($event_id != NULL)
    {
      $form->assign('subscription_max_uses', CRM_BoxOffice_BAO_Event::get_subscription_max_uses($event_id));
    }
  }
}

function civiboxoffice_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  $hook_name = "civiboxoffice_civicrm_validateForm_$formName";
  if (function_exists($hook_name)) {
    $hook_name($formName, $fields, $files, $form, $errors);
  }
}

function civiboxoffice_civicrm_validateForm_CRM_Event_Form_Registration_Register($formName, &$fields, &$files, &$form, &$errors) {
  $form->set('subscription_email_address', $_POST['subscription_email_address']);
  $form->set('subscription_participant_id', $_POST['subscription_participant_id']);
  civiboxoffice_validate_seats($formName, $fields, $files, $form, $errors);
}

function civiboxoffice_civicrm_validateForm_CRM_Event_Form_Participant($formName, &$fields, &$files, &$form, &$errors) {
  $submitted_values = $form->getVar('_submitValues');
  if (array_key_exists('place', $submitted_values)) {
    $participant_id = $form->getVar('_id');
    $submitted_seats = array_values(array_filter($submitted_values['place']));
    $num_seats_selected = count($submitted_seats);
    if ($num_seats_selected > 0) {
      ft_security();
      $participant_id = $form->_id;
      $participant = new CRM_Event_BAO_Participant();
      $participant->id = $participant_id;
      if (!$participant->find(TRUE))
      {
        throw new Exception("Unable to find participant with id {$participant_id} in order to validate seat selection.");
      }
      $num_tickets_for_participant = CRM_Event_BAO_Event::eventTotalSeats($participant->event_id, "participant.id = {$participant->id}");

      if ($num_seats_selected != $num_tickets_for_participant) {
        $errors['_qf_default'] = ts("You have chosen {$num_seats_selected} seats, but this registration has {$num_tickets_for_participant} tickets.");
        return;
      }
      if (count($submitted_seats) > 0) {
        CRM_BoxOffice_FusionTicket_Seat::cancel_for_order($participant_id);
        $result = Seat::reservate($participant_id, $submitted_values['ft_category_event_id'], $submitted_values['ft_category_id'], $submitted_seats, $submitted_values['ft_category_numbering'], false, false);
        if (!$result) {
          throw new Exception("There was a problem reserving the seats selected. " . print_r($seats, TRUE));
        }
        $seats = Seat::loadAllSid($participant_id);
        $form->assign('seatInfo', CRM_BoxOffice_FusionTicket_Seat::to_info_array($seats));
      }
    }
  }
}

function civiboxoffice_validate_seats($formName, &$fields, &$files, &$form, &$errors) {
  ft_security();
  $submitvalues = $form->getVar('_submitValues');

  // check submitted form for seat assignments
  if (array_key_exists('place', $submitvalues)) {
    $seats = array_values(array_filter($submitvalues['place']));
    if (empty($seats)) {
      $errors['_qf_default'] = ts('You must select seats.');
    } else {
      require_once('fusionticket/includes/classes/class.router.php');
      require_once('fusionticket/includes/classes/model.seat.php');

      // mark selected seats as on-hold
      // we will move them to reserved in postProcess
      $sid = CRM_BoxOffice_FusionTicket_Seat::sid_from_qf($form->controller->_key);
      $result = Seat::reservate($sid, $submitvalues['ft_category_event_id'], $submitvalues['ft_category_id'], $seats, $submitvalues['ft_category_numbering'], false, false);

      if (!$result) {
	// if we couldn't reserve seats, set error on form
	$errors['_qf_default'] = ts('One or more of the seats you selected became unavailable.  Please make an alternate selection.');
      }
    }
  }
}

function civiboxoffice_civicrm_postProcess($formName, &$form) {
  $hook_name = "civiboxoffice_civicrm_postProcess_$formName";
  if (function_exists($hook_name)) {
    $hook_name($formName, $form);
  }
}

function civiboxoffice_reserve_seats($formName, &$form)
{
  ft_security();

  require_once('fusionticket/includes/classes/class.router.php');
  require_once('fusionticket/includes/classes/model.seat.php');
  $seats = Seat::loadAllSid(substr($form->controller->_key, strlen($form->controller->_key) - 32));

  if (is_a($form, 'CRM_Event_Form_Registration_Confirm')) {
    // if this is front-end registration, get new partID from _participantIDS array
    // only expecting one participant ID -- use the first one
    $participantIDS = $form->getVar('_participantIDS');
    $participant = $participantIDS[0];
  }
  else {
    // if back-end registration, get new partID from _id
    $participant = $form->getVar('_id');
  }

  // if participant ID assigned and seats on-hold for this qfkey, move seats to reserved
  if ($seats && $participant) {
    foreach ($seats as $seatid => $seat) {
      $seat->seat_order_id = $participant;

      if (! $seat->save()) {
	CRM_Core_Error::fatal('Could not confirm reserved seats for this participant.  Check seat database consistency.');
      }
    }
  }
}

function civiboxoffice_record_subscription_usage($subscription_participant_id, $allowed_event_id, $participant_id)
{
  $subscription_participants = CRM_BoxOffice_BAO_Participant::find_all_subscription_participants_for_allowed_particpant_id($participant_id);
  if (empty($subscription_participants))
  {
    throw new Exception("Unable to find subscription participant for allowed event $allowed_event_id and participant $participant_id.");
  }
  $subscription_participant = NULL;
  foreach ($subscription_participants as $subscription_participant_to_check)
  {
    if ($subscription_participant_to_check->id == $subscription_participant_id)
    {
      $subscription_participant = $subscription_participant_to_check;
      break;
    }
  }

  if ($subscription_participant == NULL)
  {
    throw new Exception("Couldn't find a subscription participant record for allowed participant {$participant_id} that matches subscription participant {$subscription_participant_id}.");
  }
  CRM_BoxOffice_BAO_Participant::set_subscription_participant_id($participant_id, $subscription_participant->id);
}

function civiboxoffice_civicrm_postProcess_CRM_Event_Form_Registration_Register($formName, &$form) {
  $subscription_participant_id = $form->get('subscription_participant_id');
  if ($subscription_participant_id != NULL) {
    $price_sets = $form->get('lineItem');
    foreach ($price_sets as &$line_items) {
      foreach ($line_items as &$line_item) {
	$line_item['unit_price'] = 0;
	$line_item['line_total'] = 0;
      }
    }
    $form->set('lineItem', $price_sets);
  }
}

function civiboxoffice_civicrm_postProcess_CRM_Event_Form_Registration_Confirm($formName, &$form) {
  civiboxoffice_reserve_seats($formName, $form);
  $participant_ids = $form->getVar('_participantIDS');
  if (count($participant_ids) > 1) {
    CRM_Core_Error::fatal("The current civiboxoffice implementation cannot handle registrations with multiple participants.");
  }
  $subscription_participant_id = $form->get('subscription_participant_id');
  if ($subscription_participant_id != NULL) {
    $participant_id = $participant_ids[0];
    $allowed_event_id = $form->_values['event']['id'];
    civiboxoffice_record_subscription_usage($subscription_participant_id, $allowed_event_id, $participant_id);
  }
}

function civiboxoffice_civicrm_postProcess_CRM_Event_Form_Participant($formName, &$form) {
  ft_security();
  $submitted_values = $form->getVar('_submitValues');
  if (array_key_exists('place', $submitted_values)) {
    $participant_id = $form->getVar('_id');
    $submitted_seats = array_values(array_filter($submitted_values['place']));
    if (count($submitted_seats) > 0) {
      $seats = Seat::loadAllSid($participant_id);
      foreach ($seats as $seat) {
        $seat->seat_order_id = $participant_id;
        if (!$seat->save()) {
          throw new Exception("There was a problem saving a seat to update order {$participant_id}. " . print_r($seat, TRUE));
        }
      }
    }
  }
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_ManageEvent_Fee($formName, $form) {
  $event = new CRM_Event_BAO_Event();
  $event->id = $form->get('id');
  if (!$event->find(TRUE))
  {
    throw new Exception("Couldn't find event $id when trying to update fusion ticket fields.");
  }
  if (array_key_exists('price_set_association', $form->_submitValues)) {
    $form->set('price_set_association', $form->_submitValues['price_set_association']);
  }
  $form->assign('manage_event_fees_js_url', CRM_Core_Resources::singleton()->getUrl(CBO_EXTENSION_NAME, 'js/manage_event_fees.js'));

  $allowed_subscriptions = CRM_BoxOffice_BAO_SubscriptionAllowance::find_all_by_allowed_event_id($event->id);
  foreach ($allowed_subscriptions as $allowed_subscription) {
    $allowed_subscription->load_subscription_event();
    $allowed_subscription->load_price_set_associations($form->get('price_set_association'));
  }
  $form->assign('allowed_subscriptions', $allowed_subscriptions);
  $price_set_id_element = $form->getElement('price_set_id');
  $price_set_id = $price_set_id_element->getValue();
  $price_set_id = $price_set_id[0];
  if ($price_set_id) {
    $allowed_event_price_fields = CRM_BoxOffice_BAO_PriceSetAssociation::get_all_price_fields_for_price_set_id($price_set_id);
    $form->assign('allowed_event_price_fields', $allowed_event_price_fields);
  }
}

function civiboxoffice_civicrm_postProcess_CRM_Event_Form_ManageEvent_Fee($formName, &$form) {
  $submitted_values = $form->getVar('_submitValues');
  // Look for price field assocations
  $event = new CRM_Event_BAO_Event();
  $event->id = $form->get('id');
  if (!$event->find(TRUE))
  {
    throw new Exception("Couldn't find event $id when trying to update fusion ticket fields.");
  }
  $allowed_subscriptions = CRM_BoxOffice_BAO_SubscriptionAllowance::find_all_by_allowed_event_id($event->id);
  foreach ($allowed_subscriptions as $allowed_subscription) {
    $allowed_subscription->load_price_set_associations($form->get('price_set_association'));
  }
}

function civiboxoffice_civicrm_postProcess_CRM_Event_Form_ManageEvent_EventInfo($formName, &$form) {
  ft_security();
  $event = new CRM_Event_BAO_Event();
  $event->id = $form->get('id');
  if (!$event->find(TRUE))
  {
    throw new Exception("Couldn't find event $id when trying to update fusion ticket fields.");
  }

  $allowed_subscription_ids = CRM_Utils_Array::value('allowed_subscription_ids', $form->_submitValues, array());
  CRM_BoxOffice_BAO_SubscriptionAllowance::update_subscription_events_for_allowed_event_id($event->id, $allowed_subscription_ids); // jliu
  $subscription_max_uses = CRM_Utils_Array::value('subscription_max_uses', $form->_submitValues);
  CRM_BoxOffice_BAO_Event::set_subscription_max_uses($event->id, $subscription_max_uses);
  $general_admission_category_id = CRM_Utils_Array::value('general_admission_category_id', $form->_submitValues);
  $subscription_category_id = CRM_Utils_Array::value('subscription_category_id', $form->_submitValues);
  $save_event = FALSE;
  if ($general_admission_category_id != NULL && $general_admission_category_id != 0)
  {
    $general_admission_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_and_include_pm_ort_id($general_admission_category_id);
    $ft_event = CRM_BoxOffice_FusionTicket_Event::create_from_category_and_civicrm_event($general_admission_category, $event);
    $event_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_by_category_and_event($general_admission_category, $ft_event);
    $event->fusionticket_general_admission_category_id = $event_category->id;
    $save_event = TRUE;
  }

  if ($subscription_category_id != NULL && $subscription_category_id != 0)
  {
    $subscription_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_and_include_pm_ort_id($subscription_category_id);
    $ft_event = CRM_BoxOffice_FusionTicket_Event::create_from_category_and_civicrm_event($subscription_category, $event);
    $event_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_by_category_and_event($subscription_category, $ft_event);
    $event->fusionticket_subscription_category_id = $event_category->id;
    $save_event = TRUE;
  }
  if ($save_event)
  {
    CRM_BoxOffice_BAO_Event::save_extended_fields($event);
  }
}

function civiboxoffice_civicrm_pre($op, $objectName, $id, &$params) {
  $hook_name = "civiboxoffice_civicrm_pre_{$objectName}_{$op}";
  if (function_exists($hook_name)) {
    $hook_name($op, $objectName, $id, $params);
  }
}

function civiboxoffice_civicrm_pre_Participant_delete($op, $objectName, $id, &$params) {
  ft_security();
  require_once('fusionticket/includes/classes/class.router.php');
  require_once('fusionticket/includes/classes/model.seat.php');
  $seats = Seat::loadAllOrder($id);
  if ($seats) {
    foreach ($seats as $seatid => $seat) {
      $seatarr[] = Array('seat_id' => $seat->seat_id,
	'category_id' => $seat->seat_category_id,
	'event_id' => $seat->seat_event_id,
	'pmp_id' => $seat->seat_pmp_id);
    }

    if (!Seat::cancel($seatarr, 0)) {
      CRM_Core_Session::setStatus('Error freeing seats associated with participant.  Check Seat database consistency.');
    }
  }
}

function civiboxoffice_civicrm_pre_Event_delete($op, $objectName, $id, &$params) {
  ft_security();
  require_once('fusionticket/includes/classes/class.router.php');

  // find ft event associated with this civi event
  $query = 'SELECT category_event_id
    FROM Category
    WHERE category_data LIKE "%civicrm_event_id=' . $id . '%"';

  if ($res=ShopDB::query($query)) {

    // only supporting one category per event for now
    // so $res should be only one row
    while ($data=shopDB::fetch_assoc($res)) {
      $ft_event_id = $data['category_event_id'];
    }

    // need error checking here
    if (isset($ft_event_id)) {
      $ft_event = Event::load($ft_event_id);
      $ft_event->_change_state('pub', 'unpub');
      $ft_event->delete();
    }
  }
}

function civiboxoffice_civicrm_post($op, $objectName, $id, &$params) {
  $hook_name = "civiboxoffice_civicrm_post_{$objectName}_{$op}";
  if (function_exists($hook_name)) {
    $hook_name($op, $objectName, $id, $params);
  }
}

function civiboxoffice_civicrm_post_Participant_edit($op, $objectName, $id, &$params) {
  // Cancel tickets associated with this particiant record if necessary.
  $query = "SELECT civicrm_participant.*, civicrm_participant_status_type.name
           FROM
             civicrm_participant
           LEFT JOIN
             civicrm_participant_status_type
           ON
             civicrm_participant.status_id = civicrm_participant_status_type.id
           WHERE
             lower(civicrm_participant_status_type.name) = 'cancelled'
           AND
             civicrm_participant.id = " . $id;

  if ($res=ShopDB::query($query)) {
    if ($data=shopDB::fetch_assoc($res)) {
      CRM_BoxOffice_FusionTicket_Seat::cancel_for_order($id);
    }
  }
}

function civiboxoffice_create_fusionticket_event($id, &$params) {
}
