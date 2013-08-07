<?php

require_once('../../../default/settings.php');

$db_settings = $databases['default']['default'];
$db = new PDO("mysql:dbname={$db_settings['database']};host={$db_settings['host']}", $db_settings['username'], $db_settings['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = <<<EOS
SELECT
  civicrm_participant.id,
  civicrm_participant.contact_id
FROM
  civicrm_participant
JOIN
  civicrm_event ON (civicrm_participant.event_id = civicrm_event.id)
WHERE
  civicrm_participant.source LIKE '%Flex Pass%'
AND
  civicrm_event.start_date > '2013-06-01'
AND
  (
      civicrm_event.title LIKE '%Cat in the%'
    OR
      civicrm_event.title LIKE '%Frog and Toad%'
    OR
      civicrm_event.title LIKE '%Mercy Watson%'
    OR
      civicrm_event.title LIKE '%Mountain Meets%'
    OR
      civicrm_event.title LIKE '%Ladybug%'
  )
EOS;
$flex_pass_uses_query = $db->prepare($sql);
$sql = <<<EOS
  SELECT
    civicrm_participant.id
  FROM
    civicrm_participant
  WHERE
    civicrm_participant.event_id IN (475, 476) 
  AND
    civicrm_participant.contact_id = :contact_id
EOS;
$subscription_record_query = $db->prepare($sql);
$sql = <<<EOS
  UPDATE
    civicrm_participant
  SET
    subscription_participant_id = :subscription_participant_id
  WHERE
    civicrm_participant.id = :allowed_participant_id
EOS;
$record_subscription_statement = $db->prepare($sql);

$flex_pass_uses_query->execute();

foreach ($flex_pass_uses_query as $row)
{
  $params = array
  (
    'contact_id' => $row['contact_id'],
  );
  $subscription_record_query->execute($params);
  if ($subscription_record_query->rowCount() == 0)
  {
    print("Found no subscriptions for {$row['contact_id']} for participant record {$row['id']}. Skipping\n");
    continue;
  }
  elseif ($subscription_record_query->rowCount() > 1)
  {
    print("Found more than one subscription for {$row['contact_id']} for participant record {$row['id']}. Skipping\n");
    continue;
  }
  $subscription_participant = $subscription_record_query->fetch();
  $params = array
  (
    'subscription_participant_id' => $subscription_participant['id'],
    'allowed_participant_id' => $row['id'],
  );
//  print_r($params);
  $record_subscription_statement->execute($params);
}

