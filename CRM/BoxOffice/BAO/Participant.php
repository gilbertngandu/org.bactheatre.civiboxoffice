<?php

class CRM_BoxOffice_BAO_Participant
{
  static function find_all_for_subscription_participant($subscription_participant)
  {
    $sql = <<<EOS
      SELECT
	civicrm_participant.*
      FROM
	civicrm_participant
      WHERE
	civicrm_participant.subscription_event_id = %1	
      AND
	civicrm_participant.contact_id = %2
EOS;
    $params = array
    (
      1 => array($subscription_participant->event_id, 'Integer'),
      2 => array($subscription_participant->contact_id, 'Integer'),
    );
    dd($params);
    $participant = CRM_Core_DAO::executeQuery($sql, $params, 'CRM_Event_BAO_Participant');
    $participants = array();
    while ($participant->fetch())
    {
      $participants[] = clone($participant);
    }
    return $participants;
  }

  static function set_subscription_event_id($id, $value)
  {
    if ($value == NULL)
    {
      $value = 'NULL';
    }
    else
    {
      $value = mysql_real_escape_string($value);
    }
    $sql = <<<EOS
      UPDATE
	civicrm_participant
      SET
	subscription_event_id = $value
      WHERE
	civicrm_participant.id = %1
EOS;
    dd($sql, $value);
    CRM_Core_DAO::executeQuery($sql, array(1 => array($id, 'Integer')));
  }
}
