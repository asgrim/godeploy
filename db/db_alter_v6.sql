SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP TABLE IF EXISTS `deployment_statuses` ;

CREATE  TABLE `deployment_statuses` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(12) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `image_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) )
ENGINE = MyISAM;

DROP TABLE IF EXISTS `deployment_file_statuses` ;

CREATE  TABLE `deployment_file_statuses` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(12) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `image_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) )
ENGINE = MyISAM;

SET AUTOCOMMIT=0;
insert into `deployment_statuses` (`id`, `code`, `name`, `image_name`) values (1, 'PREVIEW', 'Previewing', 'view/16x16.png');
insert into `deployment_statuses` (`id`, `code`, `name`, `image_name`) values (2, 'RUNNING', 'Running', 'running/on_ededed/16x16.gif');
insert into `deployment_statuses` (`id`, `code`, `name`, `image_name`) values (3, 'COMPLETE', 'Completed (no errors)', 'complete/16x16.png');
insert into `deployment_statuses` (`id`, `code`, `name`, `image_name`) values (4, 'FAILED', 'Failed - There were errors (see below)', 'failed/16x16.png');

COMMIT;

SET AUTOCOMMIT=0;
insert into `deployment_file_statuses` (`id`, `code`, `name`, `image_name`) values (1, 'NEW', 'Not started', 'waiting/16x16.png');
insert into `deployment_file_statuses` (`id`, `code`, `name`, `image_name`) values (2, 'IN_PROGRESS', 'In progress', 'running/on_ffffff/16x16.gif');
insert into `deployment_file_statuses` (`id`, `code`, `name`, `image_name`) values (3, 'COMPLETE', 'Complete', 'complete/16x16.png');
insert into `deployment_file_statuses` (`id`, `code`, `name`, `image_name`) values (4, 'FAILED', 'Failed', 'failed/16x16.png');

COMMIT;

UPDATE `configuration` SET `value` = '6' WHERE `key` = 'db_version';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
