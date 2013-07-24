<?php

class CRM_BoxOffice_BAO_Participant
{
  static function find_all_for_subscription_event_id($subscription_event_id)
  {
    $sql = <<<EOS
      SELECT
	civicrm_participant.*
      FROM
	civicrm_participant
      WHERE
	civicrm_participant.subscription_event_id = %1	
EOS;

    $participant = CRM_Core_DAO::executeQuery($sql, array(1 => array($subscription_event_id, 'Integer')), 'CRM_Event_BAO_Participant');
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
