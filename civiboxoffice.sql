DROP TABLE IF EXISTS `civiboxoffice_associated_price_sets`;
DROP TABLE IF EXISTS `civiboxoffice_valid_subscription_events`;

CREATE TABLE `civiboxoffice_associated_price_sets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int unsigned DEFAULT NULL COMMENT 'FK to event that subscribers can use subscription for',
  `subscription_price_field_id` int unsigned DEFAULT NULL COMMENT 'FK to price field that is used for subscription',
  `event_price_field_id` int unsigned DEFAULT NULL COMMENT 'FK to price field for this event that receives value from subscription',
  PRIMARY KEY (`id`),
  CONSTRAINT FK_civiboxoffice_aps_event_id FOREIGN KEY (`event_id`) REFERENCES `civicrm_event`(`id`) ON DELETE CASCADE,
  CONSTRAINT FK_civiboxoffice_subscription_price_field_id FOREIGN KEY (`subscription_price_field_id`) REFERENCES `civicrm_price_field`(`id`) ON DELETE CASCADE,
  CONSTRAINT FK_civiboxoffice_event_price_field_id FOREIGN KEY (`event_price_field_id`) REFERENCES `civicrm_price_field`(`id`) ON DELETE CASCADE
);

CREATE TABLE `civiboxoffice_valid_subscription_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `subscription_event_id` int unsigned NOT NULL COMMENT 'FK to event that can be used to purchase this event',
  `event_id` int unsigned NOT NULL COMMENT 'FK to event that can be purchased by participant of subscription_event_id',
  PRIMARY KEY (`id`),
  CONSTRAINT FK_civiboxoffice_subscription_event_id FOREIGN KEY (`subscription_event_id`) REFERENCES `civicrm_event`(`id`) ON DELETE CASCADE,
  CONSTRAINT FK_civiboxoffice_vse_event_id FOREIGN KEY (`event_id`) REFERENCES `civicrm_event`(`id`) ON DELETE CASCADE
);

