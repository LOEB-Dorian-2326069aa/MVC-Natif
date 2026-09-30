-- ==========================================================
-- Schéma de Base de Données - Exercice Fil Rouge (M1 INRA1)
-- Table Utilisateur conforme au sujet de TD
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `2026_M1` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `2026_M1`;

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `user_login` VARCHAR(191) NOT NULL,
  `user_password` LONGTEXT NOT NULL,
  `user_compte_id` INT NOT NULL,
  `user_mail` VARCHAR(191) NOT NULL,
  `user_date_new` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_date_login` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `cle_etrangere` (`user_compte_id`),
  UNIQUE KEY `unique_user_login` (`user_login`),
  UNIQUE KEY `unique_user_mail` (`user_mail`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
