<?php

class CRM_BoxOffice_FusionTicket_Seat
{
  static function cancel_for_sid($sid, $place_map_category)
  {
    $seats = Seat::loadAllSid($sid);
    if ($seats) 
    {
      $staleseats = Array();
      foreach ($seats as $seatid => $seat) 
      {
	if (! strcmp($seat->seat_status, Seat::STATUS_HOLD)) 
	{
	  $staleseats[$seatid] = array
	  (
	    'event_id' => $place_map_category->category_event_id,
	    'category_id' => $place_map_category->category_id,
	    'pmp_id' => $place_map_category->category_pmp_id,
	    'seat_id' => $seat->seat_id,
	  );
	}
      }
      if ($staleseats) {
	Seat::cancel($staleseats, 0);
      }
    }
    // fusionticket housekeeping to free abandoned seat selections
    check_system();
  }

  static function sid_from_qf($qf)
  {
    return substr($qf, strlen($qf) - 32);
  }
}
