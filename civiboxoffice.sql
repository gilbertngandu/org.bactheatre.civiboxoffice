DROP TABLE IF EXISTS civiboxoffice_price_set_associations;
DROP TABLE IF EXISTS civiboxoffice_subscription_allowances;

-- /*******************************************************
-- *
-- * civiboxoffice_subscription_allowances
-- *
-- *******************************************************/
CREATE TABLE `civiboxoffice_subscription_allowances` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Subscription Allowance ID',
     `subscription_event_id` int unsigned NOT NULL   COMMENT 'The event ID for the subscription event that will allow the allowed event.',
     `allowed_event_id` int unsigned NOT NULL   COMMENT 'The event ID for the event allowed by the subscription event.' 
,
    PRIMARY KEY ( `id` )
 
    ,     UNIQUE INDEX `UI_subscription_allowed_event`(
        subscription_event_id
      , allowed_event_id
  )
  
,          CONSTRAINT FK_civiboxoffice_subscription_allowances_subscription_event_id FOREIGN KEY (`subscription_event_id`) REFERENCES `civicrm_event`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civiboxoffice_subscription_allowances_allowed_event_id FOREIGN KEY (`allowed_event_id`) REFERENCES `civicrm_event`(`id`) ON DELETE CASCADE  
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

-- /*******************************************************
-- *
-- * civiboxoffice_price_set_associations
-- *
-- *******************************************************/
CREATE TABLE `civiboxoffice_price_set_associations` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Price Set Association ID',
     `event_id` int unsigned NOT NULL   COMMENT 'The event ID for the allowed event that will be used by a subscription.',
     `subscription_price_field_id` int unsigned NOT NULL   COMMENT 'The price field ID in the subscription event that will get mapped to a price field in the allowed event.',
     `allowed_event_price_field_id` int unsigned NOT NULL   COMMENT 'The price field ID in the allowed event that will get mapped from a price field from the subscription event.' 
,
    PRIMARY KEY ( `id` )
 
    ,     UNIQUE INDEX `UI_price_set_associations`(
        event_id
      , subscription_price_field_id
      , allowed_event_price_field_id
  )
  
,          CONSTRAINT FK_civiboxoffice_price_set_associations_event_id FOREIGN KEY (`event_id`) REFERENCES `civicrm_event`(`id`) ON DELETE CASCADE  
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

ALTER TABLE `civicrm_event` ADD COLUMN `subscription_max_uses` INT UNSIGNED;
ALTER TABLE `civicrm_participant` ADD COLUMN `subscription_participant_id` INT UNSIGNED;
ALTER TABLE `civicrm_participant` ADD CONSTRAINT FK_civicrm_event_subscription_participant_id FOREIGN KEY (`subscription_participant_id`) REFERENCES `civicrm_participant` (`id`);
ALTER TABLE `civicrm_event` ADD COLUMN `fusionticket_general_admission_category_id` INT;
ALTER TABLE `civicrm_event` ADD COLUMN `fusionticket_subscription_category_id` INT;
