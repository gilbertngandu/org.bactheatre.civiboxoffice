<?php

class CRM_BoxOffice_BAO_Contact
{
  static function addresses($contact_id)
  {
    $addresses = array();
    $address = new CRM_Core_BAO_Address();
    $address->contact_id = $contact_id;
    $address->find();
    while ($address->fetch()) {
      $addresses[] = clone($address);
    }
    return $addresses;
  }

  static function phones($contact_id)
  {
    $phones = array();
    $phone = new CRM_Core_BAO_Phone();
    $phone->contact_id = $contact_id;
    $phone->find();
    while ($phone->fetch()) {
      $phones[] = clone($phone);
    }
    return $phones;
  }
}
