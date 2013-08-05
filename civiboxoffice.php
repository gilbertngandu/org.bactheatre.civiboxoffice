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
  if (preg_match('/CRM_Event_Form_ManageEvent.*/', $formName)) {
    CRM_Core_Resources::singleton()->addScriptFile(CBO_EXTENSION_NAME, 'js/manage_event.js');
  }
}

function civiboxoffice_build_seat_selector($formName, &$form) {
  if ($_POST) {
    return;
  }
  ft_security();

  CRM_Core_Resources::singleton()->addScriptFile(CBO_EXTENSION_NAME, 'js/civiboxoffice.js');

  $event_id = $form->getVar('_eventId');
  $snippet = CRM_Utils_Array::value('snippet', $_REQUEST);
  if (!isset($event_id) || !isset($snippet)) {
    return;
  }

  $event = new CRM_Event_BAO_Event();
  $event->id = $event_id;
  if (!$event->find(TRUE)) {
    throw new Exception("Couldn't find event with ID {$event_id} to setup seat selection.");
  }

  $place_map_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_for_civicrm_event_and_category_type($event, 'general_admission');
  if ($place_map_category != NULL) {
    $sid = CRM_BoxOffice_FusionTicket_Seat::sid_from_qf($form->controller->_key);
    CRM_BoxOffice_FusionTicket_Seat::cancel_for_sid($sid, $place_map_category);
    $subscription_place_map_category = CRM_BoxOffice_FusionTicket_PlaceMapCategory::find_for_civicrm_event_and_category_type($event, 'subscription');
    if ($subscription_place_map_category != NULL)
    {
      CRM_BoxOffice_FusionTicket_Seat::cancel_for_sid($sid, $subscription_place_map_category);
    }
    $subscription_participant_id = $form->get('subscription_participant_id');
    $seatmap = CRM_BoxOffice_FusionTicket_PlaceMapCategory::draw($place_map_category_to_draw);
    $form->assign('subscription_email_address', $form->get('subscription_email_address'));
    $form->assign('place_map_category', $place_map_category);
    $form->assign('seatMap', $seatmap);
  }
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
  civiboxoffice_build_seat_selector($formName, $form);
  civiboxoffice_add_subscription($form);
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_Registration_Confirm($formName, &$form) {
  // get any seats on hold for this qfkey and assign them to form
  ft_security();
  require_once('fusionticket/includes/classes/class.router.php');
  require_once('fusionticket/includes/classes/model.seat.php');

  $seats = Seat::loadAllSid(substr($form->controller->_key, strlen($form->controller->_key) - 32));
  if ($seats) {
    $seatarr = Array();

    foreach ($seats as $seatid => $seat) {
      $seatarr[] = Array('seat_nr' => $seat->seat_nr,
	'seat_row_nr' => $seat->seat_row_nr,
	'pmz_name' => $seat->pmz_name);
    }
    $form->assign('show_seat_information', TRUE);
    $form->assign('seatInfo', $seatarr);
  }
  $subscription_participant_id = $form->get('subscription_participant_id');
  if ($subscription_participant_id != NULL) {
    $form->_values['event']['is_monetary'] = FALSE;
    $form->assign('totalAmount', 0);
    $form->set('totalAmount', 0);
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
    $seatarr = Array();

    foreach ($seats as $seatid => $seat) {
      if ($seat->seat_nr && $seat->seat_row_nr) {
	$seatarr[] = Array('seat_nr' => $seat->seat_nr,
	  'seat_row_nr' => $seat->seat_row_nr,
	  'pmz_name' => $seat->pmz_name);
      }
    }
    $form->assign('show_seat_information', TRUE);
    $form->assign('seatInfo', $seatarr);
  }
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_Registration_ThankYou($formName, &$form) {
  civiboxoffice_assign_seat_information($formName, $form);
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_ParticipantView($formName, &$form) {
  civiboxoffice_assign_seat_information($formName, $form);
}

function civiboxoffice_civicrm_buildForm_CRM_Event_Form_Participant($formName, &$form) {
  civiboxoffice_build_seat_selector($formName, $form);
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
  civiboxoffice_validate_seats($formName, $fields, $files, $form, $errors);
}

function civiboxoffice_civicrm_validateForm_CRM_Event_Form_Participant($formName, &$fields, &$files, &$form, &$errors) {
  civiboxoffice_validate_seats($formName, $fields, $files, $form, $errors);
}

function civiboxoffice_validate_seats($formName, &$fields, &$files, &$form, &$errors) {
  ft_security();
  $submitvalues = $form->getVar('_submitValues');

  // check submitted form for seat assignments
  if (array_key_exists('place', $submitvalues)) {
    $seats = array_values(array_filter($submitvalues['place']));
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
  $form->set('subscription_email_address', $_POST['subscription_email_address']);
  $form->set('subscription_participant_id', $_POST['subscription_participant_id']);
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
  civiboxoffice_reserve_seats($formName, $form);
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

function civiboxoffice_civicrm_post_Event_create($ob, $objectName, $id, &$params) {
  civiboxoffice_create_fusionticket_event($id, $params);
}

function civiboxoffice_civicrm_post_Event_edit($ob, $objectName, $id, &$params) {
  civiboxoffice_create_fusionticket_event($id, $params);
}

function civiboxoffice_create_fusionticket_event($id, &$params) {
  if (isset($_POST['allowed_subscription_ids'])) {
    $allowed_subscription_ids = $_POST['allowed_subscription_ids'];
  } else {
    $allowed_subscription_ids = array();
  }
  CRM_BoxOffice_BAO_SubscriptionAllowance::update_subscription_events_for_allowed_event_id($id, $allowed_subscription_ids);
  if ($_POST) {
    CRM_BoxOffice_BAO_Event::set_subscription_max_uses($id, $_POST['subscription_max_uses']);
  }

  $event = new CRM_Event_BAO_Event();
  $event->id = $id;
  if (!$event->find(TRUE))
  {
    throw new Exception("Couldn't find event $id when trying to update fusion ticket fields.");
  }

  ft_security();
  $general_admission_category_id = $_POST['general_admission_category_id'];
  $subscription_category_id = $_POST['subscription_category_id'];
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
