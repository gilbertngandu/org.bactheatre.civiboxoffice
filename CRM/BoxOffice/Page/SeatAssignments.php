<?php

class CRM_BoxOffice_Page_SeatAssignments
{
  static function report()
  {
    $sql = <<<EOS
SELECT
  Seat.seat_row_nr AS row,
  Seat.seat_nr AS seat,
  PlaceMapZone.pmz_name AS section,
  civicrm_participant.id AS ticket_id,
  civicrm_contact.last_name AS last_name,
  civicrm_contact.first_name AS first_name
FROM
  Seat
JOIN
  civicrm_event ON (Seat.seat_category_id IN (fusionticket_general_admission_category_id, fusionticket_subscription_category_id))
LEFT JOIN
  civicrm_participant ON (Seat.seat_order_id = civicrm_participant.id)
LEFT JOIN
  civicrm_contact ON (civicrm_participant.contact_id = civicrm_contact.id)
LEFT JOIN
  PlaceMapZone ON (Seat.seat_zone_id = PlaceMapZone.pmz_id)
WHERE
  civicrm_event.id = 527
ORDER BY
  Seat.seat_row_nr,
  Seat.seat_nr,
  PlaceMapZone.pmz_name
EOS;
    $style_sheet_url = CRM_Core_Resources::singleton()->getURL(CBO_EXTENSION_NAME, "css/seat_assignments.css");
    print("<html>\n");
    print("  <head>\n");
    print("    <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$style_sheet_url\">\n");
    print("  </head>\n");
    print("  <body>\n");
    print("    <table>\n");
    $column_widths = array
    (
      'section' => '150px',
      'row' => '30px',
      'seat' => '60px',
      'ticket_id' => '90px',
      'last_name' => '200px',
      'first_name' => '200px',
    );
    $event_id = $_REQUEST['event_id'];
    $params = array(1 => array($event_id, 'Integer'));
    $dao = CRM_Core_DAO::executeQuery($sql, $params);
    $header_printed = FALSE;
    while ($dao->fetch())
    {
      $row = $dao->toArray();
      if (!$header_printed)
      {
	print("  <tr>\n");
	foreach ($row as $key => $value)
	{
	  $style = "";
	  if (array_key_exists($key, $column_widths))
	  {
	    $width = $column_widths[$key];
	    $style = " style=\"width: $width;\"";
	  }
	  print("    <th$style>\n");
	  print("      $key\n");
	  print("    </th>\n");
	}
	print("  </tr>\n");
	$header_printed = TRUE;
      }
      print("  <tr>\n");
      foreach ($row as $cell)
      {
	print("    <td>\n");
	print("      $cell\n");
	print("    </td>\n");
      }	
      print("  </tr>\n");
    }
  }
}
