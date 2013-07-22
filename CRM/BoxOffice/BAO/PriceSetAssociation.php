<?php

class CRM_BoxOffice_BAO_PriceSetAssociation extends CRM_BoxOffice_DAO_PriceSetAssociation
{
  static function find_all_for_event_id($event_id)
  {
    $price_set_associations = array();
    $price_set_association = new CRM_BoxOffice_BAO_PriceSetAssociation();
    $price_set_association->event_id = $event_id;
    $price_set_association->find(FALSE);
    while ($price_set_association->fetch())
    {
      $price_set_associations[] = clone($price_set_association);
    }
    return $price_set_associations;
  }
}
