SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `godeploy` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;

-- -----------------------------------------------------
-- Table `godeploy`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`users` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL ,
  `password` VARCHAR(320) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`repository_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`repository_types` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`repository_types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `shortname` VARCHAR(16) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`public_key_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`public_key_types` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`public_key_types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`public_keys`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`public_keys` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`public_keys` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `public_key_types_id` INT NOT NULL ,
  `data` TEXT NOT NULL ,
  `comment` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_table1_public_key_types1` (`public_key_types_id` ASC) ,
  CONSTRAINT `fk_table1_public_key_types1`
    FOREIGN KEY (`public_key_types_id` )
    REFERENCES `godeploy`.`public_key_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`projects` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`projects` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `slug` VARCHAR(45) NOT NULL ,
  `repository_types_id` INT NOT NULL ,
  `repository_url` VARCHAR(255) NOT NULL ,
  `deployment_branch` VARCHAR(64) NOT NULL ,
  `public_keys_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_projects_repository_types` (`repository_types_id` ASC) ,
  INDEX `fk_projects_public_keys1` (`public_keys_id` ASC) ,
  CONSTRAINT `fk_projects_repository_types`
    FOREIGN KEY (`repository_types_id` )
    REFERENCES `godeploy`.`repository_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_public_keys1`
    FOREIGN KEY (`public_keys_id` )
    REFERENCES `godeploy`.`public_keys` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`connection_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`connection_types` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`connection_types` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `default_port` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`servers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`servers` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`servers` (
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
    REFERENCES `godeploy`.`connection_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_servers_projects1`
    FOREIGN KEY (`projects_id` )
    REFERENCES `godeploy`.`projects` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`deployment_statuses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`deployment_statuses` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`deployment_statuses` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`deployments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`deployments` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`deployments` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `users_id` INT NOT NULL ,
  `projects_id` INT NOT NULL ,
  `when` DATETIME NOT NULL ,
  `servers_id` INT NOT NULL ,
  `from_revision` VARCHAR(45) NOT NULL ,
  `to_revision` VARCHAR(45) NOT NULL ,
  `deployment_statuses_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_deployments_servers1` (`servers_id` ASC) ,
  INDEX `fk_deployments_deployment_statuses1` (`deployment_statuses_id` ASC) ,
  INDEX `fk_deployments_users1` (`users_id` ASC) ,
  INDEX `fk_deployments_projects1` (`projects_id` ASC) ,
  CONSTRAINT `fk_deployments_servers1`
    FOREIGN KEY (`servers_id` )
    REFERENCES `godeploy`.`servers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployments_deployment_statuses1`
    FOREIGN KEY (`deployment_statuses_id` )
    REFERENCES `godeploy`.`deployment_statuses` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployments_users1`
    FOREIGN KEY (`users_id` )
    REFERENCES `godeploy`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployments_projects1`
    FOREIGN KEY (`projects_id` )
    REFERENCES `godeploy`.`projects` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`deployment_file_actions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`deployment_file_actions` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`deployment_file_actions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `git_status` CHAR(1) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `verb` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`deployment_file_statuses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`deployment_file_statuses` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`deployment_file_statuses` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`deployment_files`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`deployment_files` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`deployment_files` (
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
    REFERENCES `godeploy`.`deployments` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployment_files_deployment_file_actions1`
    FOREIGN KEY (`deployment_file_actions_id` )
    REFERENCES `godeploy`.`deployment_file_actions` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_deployment_files_deployment_file_statuses1`
    FOREIGN KEY (`deployment_file_statuses_id` )
    REFERENCES `godeploy`.`deployment_file_statuses` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `godeploy`.`configuration`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `godeploy`.`configuration` ;

CREATE  TABLE IF NOT EXISTS `godeploy`.`configuration` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `key` VARCHAR(45) NOT NULL ,
  `value` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `godeploy`.`repository_types`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `godeploy`.`repository_types` (`id`, `name`, `shortname`) values (1, 'Git', 'git');
insert into `godeploy`.`repository_types` (`id`, `name`, `shortname`) values (2, 'Subversion', 'svn');

COMMIT;

-- -----------------------------------------------------
-- Data for table `godeploy`.`public_key_types`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `godeploy`.`public_key_types` (`id`, `name`) values (1, 'rsa');
insert into `godeploy`.`public_key_types` (`id`, `name`) values (2, 'dsa');

COMMIT;

-- -----------------------------------------------------
-- Data for table `godeploy`.`connection_types`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `godeploy`.`connection_types` (`id`, `name`, `default_port`) values (1, 'FTP', 21);

COMMIT;

-- -----------------------------------------------------
-- Data for table `godeploy`.`deployment_statuses`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `godeploy`.`deployment_statuses` (`id`, `name`) values (1, 'Previewing');
insert into `godeploy`.`deployment_statuses` (`id`, `name`) values (2, 'Running');
insert into `godeploy`.`deployment_statuses` (`id`, `name`) values (3, 'Completed (no errors)');
insert into `godeploy`.`deployment_statuses` (`id`, `name`) values (4, 'Failed - There were errors (see below)');

COMMIT;

-- -----------------------------------------------------
-- Data for table `godeploy`.`deployment_file_actions`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `godeploy`.`deployment_file_actions` (`id`, `git_status`, `name`, `verb`) values (1, 'D', 'Removed', 'Removing...');
insert into `godeploy`.`deployment_file_actions` (`id`, `git_status`, `name`, `verb`) values (2, 'A', 'Added', 'Uploading...');
insert into `godeploy`.`deployment_file_actions` (`id`, `git_status`, `name`, `verb`) values (3, 'M', 'Modified', 'Uploading...');

COMMIT;

-- -----------------------------------------------------
-- Data for table `godeploy`.`deployment_file_statuses`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `godeploy`.`deployment_file_statuses` (`id`, `name`) values (1, 'Not started');
insert into `godeploy`.`deployment_file_statuses` (`id`, `name`) values (2, 'In progress');
insert into `godeploy`.`deployment_file_statuses` (`id`, `name`) values (3, 'Complete');
insert into `godeploy`.`deployment_file_statuses` (`id`, `name`) values (4, 'Failed');

COMMIT;

-- -----------------------------------------------------
-- Data for table `godeploy`.`configuration`
-- -----------------------------------------------------
SET AUTOCOMMIT=0;
insert into `godeploy`.`configuration` (`id`, `key`, `value`) values (1, 'db_version', '1');

COMMIT;
