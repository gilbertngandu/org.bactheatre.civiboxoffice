<?php

class CRM_BoxOffice_FusionTicket_Event
{
  static function create_from_category_and_civicrm_event($category, $civicrm_event)
  {
    $date_components = date_parse($civicrm_event->start_date);
    $params = array
    (
      'event_date-d' => $date_components['day'],
      'event_date-m' => $date_components['month'],
      'event_date-y' => $date_components['year'],
      'event_name' => $civicrm_event->title,
      'event_pm_ort_id' => $category->category_pm_id . ',' . $category->pm_ort_id,
      'event_status' => 'unpub',
      'event_time-h' => $date_components['hour'],
      'event_time-m' => $date_components['minute'],
    );
    $ft_event = new Event(true);
    $ft_event->_fill($params, false);
    $ft_event->saveEx();
    $stats = NULL;
    $pmps = NULL;
    $result = $ft_event->publish($stats, $pmps);
    if ($result === FALSE)
    {
      throw new Exception("Error publishing ft_event: " . print_r($ft_event, TRUE));
    }
    return $ft_event;
  }
}
