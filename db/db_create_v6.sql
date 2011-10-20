SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users` ;

CREATE  TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL ,
  `password` VARCHAR(320) NOT NULL ,
  `date_added` DATETIME NOT NULL ,
  `date_updated` DATETIME NOT NULL ,
  `date_disabled` DATETIME NOT NULL ,
  `admin` BOOL NOT NULL ,
  `active` BOOL NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `repository_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `repository_types` ;

CREATE  TABLE IF NOT EXISTS `repository_types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `shortname` VARCHAR(16) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `ssh_key_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ssh_key_types` ;

CREATE  TABLE IF NOT EXISTS `ssh_key_types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `ssh_keys`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ssh_keys` ;

CREATE  TABLE IF NOT EXISTS `ssh_keys` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `ssh_key_types_id` INT NOT NULL ,
  `private_key` TEXT NOT NULL ,
  `public_key` TEXT NOT NULL ,
  `comment` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_table1_ssh_key_types1` (`ssh_key_types_id` ASC) ,
  CONSTRAINT `fk_table1_ssh_key_types1`
    FOREIGN KEY (`ssh_key_types_id` )
    REFERENCES `ssh_key_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projects` ;

CREATE  TABLE IF NOT EXISTS `projects` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `slug` VARCHAR(45) NOT NULL ,
  `repository_types_id` INT NOT NULL ,
  `repository_url` VARCHAR(255) NOT NULL ,
  `deployment_branch` VARCHAR(64) NOT NULL ,
  `ssh_keys_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_projects_repository_types` (`repository_types_id` ASC) ,
  INDEX `fk_projects_public_keys1` (`ssh_keys_id` ASC) ,
  CONSTRAINT `fk_projects_repository_types`
    FOREIGN KEY (`repository_types_id` )
    REFERENCES `repository_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_ssh_keys1`
    FOREIGN KEY (`ssh_keys_id` )
    REFERENCES `ssh_keys` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `connection_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `connection_types` ;

CREATE  TABLE IF NOT EXISTS `connection_types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `default_port` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `servers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `servers` ;

CREATE  TABLE IF NOT EXISTS `servers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `hostname` VARCHAR(255) NOT NULL ,
  `connection_types_id` INT NOT NULL ,
  `port` INT NULL ,
  `username` VARCHAR(128) NULL ,
  `password` VARCHAR(128) NULL ,
  `remote_path` VARCHAR(255) NULL ,
  `projects_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_servers_connection_types1` (`connection_types_id` ASC) ,
  INDEX `fk_servers_projects1` (`projects_id` ASC) ,
  CONSTRAINT `fk_servers_connection_types1`
    FOREIGN KEY (`connection_types_id` )
    REFERENCES `connection_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_servers_projects1`
    FOREIGN KEY (`projects_id` )
    REFERENCES `projects` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `deployment_statuses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `deployment_statuses` ;

CREATE  TABLE IF NOT EXISTS `deployment_statuses` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(12) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `image_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `deployments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `deployments` ;

CREATE  TABLE IF NOT EXISTS `deployments` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `users_id` INT NOT NULL ,
  `projects_id` INT NOT NULL ,
  `when` DATETIME NOT NULL ,
  `servers_id` INT NOT NULL ,
  `from_revision` VARCHAR(45) NOT NULL ,
  `to_revision` VARCHAR(45) NOT NULL ,
  `comment` TEXT NOT NULL ,
  `deployment_statuses_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_deployments_servers1` (`servers_id` ASC) ,
  INDEX `fk_deployments_deployment_statuses1` (`deployment_statuses_id` ASC) ,
  INDEX `fk_deployments_users1` (`users_id` ASC) ,
  INDEX `fk_deployments_projects1` (`projects_id` ASC) ,
  CONSTRAINT `fk_deployments_servers1`
    FOREIGN KEY (`servers_id` )
    REFERENCES `servers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployments_deployment_statuses1`
    FOREIGN KEY (`deployment_statuses_id` )
    REFERENCES `deployment_statuses` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployments_users1`
    FOREIGN KEY (`users_id` )
    REFERENCES `users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployments_projects1`
    FOREIGN KEY (`projects_id` )
    REFERENCES `projects` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `deployment_file_actions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `deployment_file_actions` ;

CREATE  TABLE IF NOT EXISTS `deployment_file_actions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `git_status` CHAR(1) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `verb` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `deployment_file_statuses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `deployment_file_statuses` ;

CREATE  TABLE IF NOT EXISTS `deployment_file_statuses` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(12) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `image_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `deployment_files`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `deployment_files` ;

CREATE  TABLE IF NOT EXISTS `deployment_files` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `deployments_id` INT NOT NULL ,
  `deployment_file_actions_id` INT NOT NULL ,
  `deployment_file_statuses_id` INT NOT NULL ,
  `details` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_deployment_files_deployments1` (`deployments_id` ASC) ,
  INDEX `fk_deployment_files_deployment_file_actions1` (`deployment_file_actions_id` ASC) ,
  INDEX `fk_deployment_files_deployment_file_statuses1` (`deployment_file_statuses_id` ASC) ,
  CONSTRAINT `fk_deployment_files_deployments1`
    FOREIGN KEY (`deployments_id` )
    REFERENCES `deployments` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployment_files_deployment_file_actions1`
    FOREIGN KEY (`deployment_file_actions_id` )
    REFERENCES `deployment_file_actions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployment_files_deployment_file_statuses1`
    FOREIGN KEY (`deployment_file_statuses_id` )
    REFERENCES `deployment_file_statuses` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `configuration`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `configuration` ;

CREATE  TABLE IF NOT EXISTS `configuration` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `key` VARCHAR(45) NOT NULL ,
  `value` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `repository_types`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `repository_types` (`id`, `name`, `shortname`) values (1, 'Git', 'git');
insert into `repository_types` (`id`, `name`, `shortname`) values (2, 'Subversion', 'svn');

COMMIT;

-- -----------------------------------------------------
-- Data for table `public_key_types`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `ssh_key_types` (`id`, `name`) values (1, 'rsa');
insert into `ssh_key_types` (`id`, `name`) values (2, 'dsa');

COMMIT;

-- -----------------------------------------------------
-- Data for table `connection_types`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `connection_types` (`id`, `name`, `default_port`) values (1, 'FTP', 21);

COMMIT;

-- -----------------------------------------------------
-- Data for table `deployment_statuses`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `deployment_statuses` (`id`, `code`, `name`, `image_name`) values (1, 'PREVIEW', 'Previewing', 'view/16x16.png');
insert into `deployment_statuses` (`id`, `code`, `name`, `image_name`) values (2, 'RUNNING', 'Running', 'running/on_ededed/16x16.gif');
insert into `deployment_statuses` (`id`, `code`, `name`, `image_name`) values (3, 'COMPLETE', 'Completed (no errors)', 'complete/16x16.png');
insert into `deployment_statuses` (`id`, `code`, `name`, `image_name`) values (4, 'FAILED', 'Failed - There were errors (see below)', 'failed/16x16.png');

COMMIT;

-- -----------------------------------------------------
-- Data for table `deployment_file_actions`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `deployment_file_actions` (`id`, `git_status`, `name`, `verb`) values (1, 'D', 'Removed', 'Removing...');
insert into `deployment_file_actions` (`id`, `git_status`, `name`, `verb`) values (2, 'A', 'Added', 'Uploading...');
insert into `deployment_file_actions` (`id`, `git_status`, `name`, `verb`) values (3, 'M', 'Modified', 'Uploading...');

COMMIT;

-- -----------------------------------------------------
-- Data for table `deployment_file_statuses`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `deployment_file_statuses` (`id`, `code`, `name`, `image_name`) values (1, 'NEW', 'Not started', 'waiting/16x16.png');
insert into `deployment_file_statuses` (`id`, `code`, `name`, `image_name`) values (2, 'IN_PROGRESS', 'In progress', 'running/on_ffffff/16x16.gif');
insert into `deployment_file_statuses` (`id`, `code`, `name`, `image_name`) values (3, 'COMPLETE', 'Complete', 'complete/16x16.png');
insert into `deployment_file_statuses` (`id`, `code`, `name`, `image_name`) values (4, 'FAILED', 'Failed', 'failed/16x16.png');

COMMIT;

-- -----------------------------------------------------
-- Data for table `configuration`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `configuration` (`id`, `key`, `value`) values (1, 'db_version', '6');

COMMIT;
