CREATE TABLE IF NOT EXISTS `französisch_links` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titel` VARCHAR(250) NOT NULL,
  `url` VARCHAR(1000) NOT NULL,
  `beschreibung` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_französisch_links_titel` (`titel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
