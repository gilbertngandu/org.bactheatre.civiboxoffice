<?php

class CRM_BoxOffice_BAO_Participant
{
  public static function counted_particpant_status_ids()
  {
    $counted_statuses = CRM_Event_PseudoConstant::participantStatus(NULL, 'is_counted = 1');
    return implode(', ', array_keys($counted_statuses));
  }


  static function find_all_for_subscription_email_address_and_allowed_event_id($subscription_email_address, $allowed_event_id)
  {
    $params = array
    (
      1 => array(self::counted_particpant_status_ids(), 'String'),
      2 => array($allowed_event_id, 'Integer'),
      3 => array($subscription_email_address, 'String'),
    );
    $sql = <<<EOS
      SELECT
	civicrm_participant.*,
	civicrm_event.title AS event_title
      FROM
	civiboxoffice_subscription_allowances
      JOIN
	civicrm_participant ON (civiboxoffice_subscription_allowances.subscription_event_id = civicrm_participant.event_id)
      JOIN
	civicrm_email ON (civicrm_participant.contact_id = civicrm_email.contact_id)
      JOIN
	civicrm_event ON (civicrm_participant.event_id = civicrm_event.id)
      WHERE
	civicrm_participant.status_id IN (%1)
      AND
	civiboxoffice_subscription_allowances.allowed_event_id = %2
      AND
	civicrm_email.email = %3
      AND
	civicrm_email.is_primary
EOS;
    $participants = array();
    $dao = CRM_Core_DAO::executeQuery($sql, $params, 'CRM_Event_BAO_Participant');
    while ($dao->fetch())
    {
      $participants[] = clone($dao);
    }
    return $participants;
  }

  static function find_all_not_cancelled_for_subscription_participant($subscription_participant)
  {
    $sql = <<<EOS
      SELECT
	civicrm_participant.*, civicrm_participant_status_type.name, civicrm_participant_status_type.id
      FROM
	civicrm_participant
      LEFT JOIN
        civicrm_participant_status_type
      ON
         civicrm_participant.status_id = civicrm_participant_status_type.id
      WHERE
        lower(civicrm_participant_status_type.name) != 'cancelled'
      AND
	civicrm_participant.subscription_participant_id = %1
EOS;
    $params = array
    (
      1 => array($subscription_participant->id, 'Integer'),
    );
    $participant = CRM_Core_DAO::executeQuery($sql, $params, 'CRM_Event_BAO_Participant');
    $participants = array();
    while ($participant->fetch())
    {
      $participants[] = clone($participant);
    }
    return $participants;
  }

  static function find_all_subscription_participants_for_allowed_particpant_id($allowed_participant_id)
  {
    $params = array
    (
      1 => array($allowed_participant_id, 'Integer'),
    );
    $sql = <<<EOS
      SELECT
	subscription_participants.*
      FROM
	civicrm_participant allowed_participants
      JOIN
	civiboxoffice_subscription_allowances ON (allowed_participants.event_id = civiboxoffice_subscription_allowances.allowed_event_id)
      JOIN
	civicrm_participant subscription_participants ON (civiboxoffice_subscription_allowances.subscription_event_id = subscription_participants.event_id)
      WHERE
	allowed_participants.id = %1
EOS;
    $participants = array();
    $dao = CRM_Core_DAO::executeQuery($sql, $params, 'CRM_Event_BAO_Participant');
    while ($dao->fetch())
    {
      $participants[] = clone($dao);
    }
    return $participants;
  }

  static function set_subscription_participant_id($id, $value)
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
	subscription_participant_id = $value
      WHERE
	civicrm_participant.id = %1
EOS;
    CRM_Core_DAO::executeQuery($sql, array(1 => array($id, 'Integer')));
  }
}
