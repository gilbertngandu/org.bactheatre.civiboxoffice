<?php

class CRM_BoxOffice_FusionTicket_Seat
{
  static function cancel_for_sid($sid)
  {
    $seats = Seat::loadAllSid($sid);
    static::cancel_seats($seats, Seat::STATUS_HOLD);
  }

  static function cancel_for_order($order_id)
  {
    $seats = Seat::loadAllOrder($order_id);
    static::cancel_seats($seats, Seat::STATUS_ORDERED);
  }

  static function cancel_seats($seats, $expected_status)
  {
    $staleseats = array();
    foreach ($seats as $seatid => $seat) 
    {
      if (!strcmp($seat->seat_status, $expected_status)) 
      {
        $staleseats[$seatid] = array
        (
          'event_id' => $seat->seat_event_id,
          'category_id' => $seat->seat_category_id,
          'pmp_id' => $seat->seat_pmp_id,
          'seat_id' => $seat->seat_id,
        );
      }
    }
    if ($staleseats) {
      Seat::cancel($staleseats, 0);
    }
    check_system();
  }

  static function sid_from_qf($qf)
  {
    return substr($qf, strlen($qf) - 32);
  }

  static function to_info_array($seats)
  {
    $seat_arrays = Array();

    foreach ($seats as $seatid => $seat) 
    {
      $seat_arrays[] = array
      (
        'seat_nr' => $seat->seat_nr,
        'seat_row_nr' => $seat->seat_row_nr,
        'pmz_name' => $seat->pmz_name,
      );
    }
    return $seat_arrays;
  }
}
