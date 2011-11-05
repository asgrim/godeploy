SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `configs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `configs` ;

CREATE TABLE `configs` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `projects_id` INT NOT NULL ,
  `filename` VARCHAR( 512 ) NOT NULL ,
  `content` LONGTEXT NOT NULL ,
  INDEX (  `projects_id` )
) ENGINE = MYISAM ;


-- -----------------------------------------------------
-- Table `configs_servers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `configs_servers` ;

CREATE TABLE `configs_servers` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `servers_id` INT NOT NULL ,
  `configs_id` INT NOT NULL ,
  UNIQUE (
    `servers_id` ,
    `configs_id`
  )
) ENGINE = MYISAM ;


-- -----------------------------------------------------
-- Update version number
-- -----------------------------------------------------
UPDATE `configuration` SET `value` = '7' WHERE `key` = 'db_version';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;



