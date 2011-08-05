SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `godeploy`.`deployment_statuses` ADD COLUMN `code` VARCHAR(12) NOT NULL  AFTER `id` ;

ALTER TABLE `godeploy`.`deployment_file_statuses` ADD COLUMN `code` VARCHAR(12) NOT NULL  AFTER `id` ;

UPDATE `godeploy`.`configuration` SET `value` = '2' WHERE `key` = 'db_version';

UPDATE `godeploy`.`deployment_statuses` SET `code` = 'PREVIEW' WHERE `id` = '1';
UPDATE `godeploy`.`deployment_statuses` SET `code` = 'RUNNING' WHERE `id` = '2';
UPDATE `godeploy`.`deployment_statuses` SET `code` = 'COMPLETE' WHERE `id` = '3';
UPDATE `godeploy`.`deployment_statuses` SET `code` = 'FAILED' WHERE `id` = '4';

ALTER TABLE `godeploy`.`deployment_statuses` ADD UNIQUE INDEX `code_UNIQUE` (`code` ASC) ;

UPDATE `godeploy`.`deployment_file_statuses` SET `code` = 'NEW' WHERE `id` = '1';
UPDATE `godeploy`.`deployment_file_statuses` SET `code` = 'IN_PROGRESS' WHERE `id` = '2';
UPDATE `godeploy`.`deployment_file_statuses` SET `code` = 'COMPLETE' WHERE `id` = '3';
UPDATE `godeploy`.`deployment_file_statuses` SET `code` = 'FAILED' WHERE `id` = '4';

ALTER TABLE `godeploy`.`deployment_file_statuses` ADD UNIQUE INDEX `code_UNIQUE` (`code` ASC) ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
