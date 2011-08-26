SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

RENAME TABLE  `public_key_types` TO  `ssh_key_types` ;

RENAME TABLE  `public_keys` TO  `ssh_keys` ;

ALTER TABLE  `ssh_keys` CHANGE  `data`  `public_key` TEXT NOT NULL ;
ALTER TABLE  `ssh_keys` CHANGE  `public_key_types_id`  `ssh_key_types_id` INT( 11 ) NOT NULL ;
ALTER TABLE  `ssh_keys` ADD  `private_key` TEXT NOT NULL AFTER  `ssh_key_types_id` ;

ALTER TABLE  `projects` CHANGE  `public_keys_id`  `ssh_keys_id` INT( 11 ) NOT NULL ;

UPDATE `configuration` SET `value` = '3' WHERE `key` = 'db_version';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
