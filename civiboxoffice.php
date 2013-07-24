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
  CRM_Core_Resources::singleton()->addScriptFile(CBO_EXTENSION_NAME, 'js/civiboxoffice.js');
  $eid = $form->getVar('_eventId');
  $snippet = CRM_Utils_Array::value('snippet', $_REQUEST);
  if (!isset($eid) || !isset($snippet)) {
    return;
  }
  ft_security();
  require_once('fusionticket/includes/classes/class.router.php');
  require_once('fusionticket/includes/shop_plugins/function.placemap-civicrm.php');

  // Fusionticket Category.category_data field overloaded to store civi event ids
  // use this as our mapping from civi event id to fusionticket seat map category
  $query = 'SELECT category_id, category_event_id, category_pmp_id,
    category_ident, category_numbering
    FROM Category
    WHERE category_data LIKE "%civicrm_event_id=' . $eid . '%"';

  if ($res=ShopDB::query($query)) {
    $category = Array();

    // only supporting one category per event for now
    // so $res should be only one row
    while ($data=shopDB::fetch_assoc($res)) {
      foreach ($data as $mykey => $myvalue) {
	$form->assign('ft_' . $mykey, $myvalue);
	$category[$mykey] = $myvalue;
      }
    }

    // cancel seats previously on hold by same qfkey -- maybe user clicked
    // go back or page reload?
    $seats = Seat::loadAllSid(substr($form->controller->_key, strlen($form->controller->_key) - 32));
    if ($seats) {
      $staleseats = Array();
      foreach ($seats as $seatid => $seat) {
	if (! strcmp($seat->seat_status, Seat::STATUS_HOLD)) {
	  $staleseats[$seatid] = Array('seat_id' => $seat->seat_id,
	    'event_id' => $category['category_event_id'],
	    'category_id' => $category['category_id'],
	    'pmp_id' => $category['category_pmp_id']);
	}
      }
      if ($staleseats) {
	Seat::cancel($staleseats, 0);
      }
    }

    // fusionticket housekeeping to free abandoned seat selections
    check_system();

    // load seatmap and assign to form
    $seatmap = placeMapDraw($category);
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
    $form->assign('seatInfo', $seatarr);
  }
  $subscription_participant_id = $form->get('subscription_participant_id');
  if ($subscription_participant_id != NULL) {
    $form->_values['event']['is_monetary'] = FALSE;
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

  // add seatmap selector to event configuration page
  ft_security();
  require_once('fusionticket/includes/classes/class.router.php');
  $form_event_id = $form->getVar('_entityId');
  // check if this event already has a seatmap / category configured
  $query = 'SELECT Ort.ort_name, PlaceMap2.pm_name, Category.category_name
    FROM Category
    LEFT JOIN PlaceMap2 ON Category.category_pm_id = PlaceMap2.pm_id
    LEFT JOIN Ort ON PlaceMap2.pm_ort_id = Ort.ort_id';
  $query .= ' WHERE Category.category_data LIKE "%civicrm_event_id=' . $form_event_id . '%"';

  if (($res=ShopDB::query($query)) && $form_event_id) {

    // only supporting one category per event for now
    // so $res should be only one row
    while ($data=shopDB::fetch_assoc($res)) {
      $existing_seatmap = implode(' -- ', array_values($data));
    }
    if (isset($existing_seatmap)) {
      $form->assign('existing_seatmap', $existing_seatmap);
    }
  }
  else {
    // if no existing seatmap for this event, display a selector
    $query = "SELECT Category.category_id, Category.category_name, PlaceMap2.pm_id, Ort.ort_id, PlaceMap2.pm_ort_id,
      PlaceMap2.pm_name, Ort.ort_name
      FROM Category
      LEFT JOIN PlaceMap2 ON Category.category_pm_id = PlaceMap2.pm_id
      LEFT JOIN Ort ON PlaceMap2.pm_ort_id = Ort.ort_id
      WHERE pm_event_id IS NULL
      ORDER BY ort_name";
    if (!$res = ShopDB::query($query)) {
      return;
    }
    $ft_categories = Array();
    $ft_categories[] = Array('ort_name' => 'None',
      'pm_name' => 'None',
      'category_id' => 0,
      'category_name' => 'None');
    while ($data=shopDB::fetch_assoc($res)) {
      $ft_categories[] = Array('ort_name' => $data['ort_name'],
	'pm_name' => $data['pm_name'],
	'category_id' => $data['category_id'],
	'category_name' => $data['category_name']);
    }
    $form->assign('ft_categories', $ft_categories);
  }

  if (isset($_POST['subscription_max_uses'])) {
    $form->assign('subscription_max_uses', $_POST['subscription_max_uses']);
  }
    
  if (($snippet == NULL && $form_event_id == NULL) || ($snippet && $component == 'event')) {
    if ($form_event_id == NULL) {
      $subscription_allowances = array();
    }
    else {
      $subscription_allowances = CRM_BoxOffice_BAO_SubscriptionAllowance::find_all_by_allowed_event_id($form_event_id);
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
    $result = Seat::reservate(substr($form->controller->_key,
      strlen($form->controller->_key) - 32),
    $submitvalues['ft_category_event_id'],
    $submitvalues['ft_category_id'],
    $seats,
    $submitvalues['ft_category_numbering'],
    false, false);

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
  $subscription_participant = CRM_BoxOffice_BAO_SubscriptionAllowance::find_participant_by_participant_id_and_allowed_event_id($participant_id, $allowed_event_id);
  if ($subscription_participant == NULL)
  {
    throw new Exception("Unable to find subscription participant for allowed event $allowed_event_id and participant $participant_id.");
  }
  if ($subscription_participant->id != $subscription_participant_id)
  {
    throw new Exception("Found subscription participant record {$subscription_participant->id}, but that ID does not match the ID from the form {$subscription_participant_id}.");
  }
  CRM_BoxOffice_BAO_Participant::set_subscription_event_id($participant_id, $subscription_participant->event_id);
}

function civiboxoffice_civicrm_postProcess_CRM_Event_Form_Registration_Register($formName, &$form) {
  $form->set('subscription_participant_id', $_POST['subscription_participant_id']);
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
  $participant_id = $form->getVar('_id');
  civiboxoffice_record_subscription_usage($formName, $form, $participant_id);
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

  // on event create or edit, check submitted values for ft_category_id field
  if (isset($_POST['ft_category_id']) && ($_POST['ft_category_id'] != 0)) {
    ft_security();
    $ft_category_id = $_POST['ft_category_id'];
    require_once('fusionticket/includes/classes/class.router.php');

    // use selected category to lookup values needed to create FT event
    $query = "SELECT Category.category_pm_id, Category.category_ident, PlaceMap2.pm_ort_id
      FROM Category
      LEFT JOIN PlaceMap2 ON Category.category_pm_id = PlaceMap2.pm_id
      WHERE category_id=" . $ft_category_id;
    if ($res=ShopDB::query($query)) {
      while ($data=shopDB::fetch_assoc($res)) {
	$placemap_id = $data['category_pm_id'];
	$category_ident = $data['category_ident'];
	$ort_id = $data['pm_ort_id'];
      }
    }
    else {
      CRM_Core_Error::fatal("Selected FusionTicket event/category not found.");
    }

    // save civi $_POST values so we can restore them later
    $civi_POST = $_POST;

    // build fake $_POST for ft event create
    $date = array_combine(Array('event_date-m', 'event_date-d', 'event_date-y'),
      explode('/', $civi_POST['start_date']));
    $timehour = date("H", strtotime($civi_POST['start_date_time']));
    $timemin = date("i", strtotime($civi_POST['start_date_time']));
    $_POST = Array('event_name' => $civi_POST['title'],
      'event_pm_ort_id' => $placemap_id . ',' . $ort_id,
      'event_status' => 'unpub',
      'event_time-h' => $timehour,
      'event_time-m' => $timemin);
    $_POST = array_merge($_POST, $date);

    // create ft event
    $ft_event = new Event(true);
    $ft_event->fillPost();
    $ft_event_id = $ft_event->saveEx();
    // publish ft event
    unset($stats);
    unset($pmps);
    $ft_event->publish($stats, $pmps);

    // store civi event ID in category_data field of new category copy
    $query = 'UPDATE Category
      SET category_data = "civicrm_event_id=' . $id . '"' .
      ' WHERE category_event_id=' . $ft_event_id .
      ' AND category_ident=' . $category_ident;
    if (!ShopDB::query($query)) {
      CRM_Core_Session::setStatus('Error updating FusionTicket event/category.  Check database consistency.');
    }

    // restore civi $_POST values
    $_POST = $civi_POST;
  }		
}
