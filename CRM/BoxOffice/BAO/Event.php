<?php

class CRM_BoxOffice_BAO_Event
{
  static function set_subscription_max_uses($id, $value)
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
	civicrm_event
      SET
	subscription_max_uses = $value
      WHERE
	civicrm_event.id = %1
EOS;
    CRM_Core_DAO::executeQuery($sql, array(1 => array($id, 'Integer')));
  }

  static function get_subscription_max_uses($id)
  {
    $sql = <<<EOS
      SELECT
	civicrm_event.subscription_max_uses
      FROM
	civicrm_event
      WHERE
	civicrm_event.id = %1
EOS;
    $result = CRM_Core_DAO::executeQuery($sql, array(1 => array($id, 'Integer', TRUE)));
    $result->fetch();
    return $result->subscription_max_uses; 
  }

  static function save_extended_fields($event)
  {
    $fusionticket_general_admission_category_id = CRM_BoxOffice_Utils::escape($event->fusionticket_general_admission_category_id, 'Integer');
    $fusionticket_subscription_category_id = CRM_BoxOffice_Utils::escape($event->fusionticket_subscription_category_id, 'Integer');
    $sql = <<<EOS
      UPDATE
	civicrm_event
      SET
	fusionticket_general_admission_category_id = $fusionticket_general_admission_category_id,
	fusionticket_subscription_category_id = $fusionticket_subscription_category_id
      WHERE
	id = %1
EOS;
    dd($sql, 'foo');
    CRM_Core_DAO::executeQuery($sql, array(1 => array($event->id, 'Integer')));
  }
}
