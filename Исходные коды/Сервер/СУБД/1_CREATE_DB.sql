SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `sim_mg` ;
CREATE SCHEMA IF NOT EXISTS `sim_mg` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `sim_mg` ;

-- -----------------------------------------------------
-- Table `sim_mg`.`tgroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`tgroup` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`tgroup` (
  `id_tgroup` INT ZEROFILL UNSIGNED NOT NULL AUTO_INCREMENT,
  `tgroup_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_tgroup`),
  UNIQUE INDEX `id_tgroup_UNIQUE` (`id_tgroup` ASC),
  UNIQUE INDEX `tgroup_name_UNIQUE` (`tgroup_name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`user` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`user` (
  `id_user` INT ZEROFILL UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_login` VARCHAR(100) NOT NULL,
  `user_sname` VARCHAR(100) NOT NULL,
  `user_fname` VARCHAR(100) NULL,
  `user_pname` VARCHAR(100) NULL,
  `user_password` VARCHAR(100) NOT NULL,
  `user_moodle_id` INT NULL,
  `user_create_date` DATETIME NULL,
  `user_last_accessed` DATETIME NULL,
  `id_tgroup` INT ZEROFILL UNSIGNED NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE INDEX `id_user_UNIQUE` (`id_user` ASC),
  INDEX `fk_user_tgroup1_idx` (`id_tgroup` ASC),
  UNIQUE INDEX `user_login_UNIQUE` (`user_login` ASC),
  CONSTRAINT `fk_user_group1`
    FOREIGN KEY (`id_tgroup`)
    REFERENCES `sim_mg`.`tgroup` (`id_tgroup`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`system`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`system` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`system` (
  `id_system` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `system_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_system`),
  UNIQUE INDEX `id_system_UNIQUE` (`id_system` ASC),
  UNIQUE INDEX `system_name_UNIQUE` (`system_name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`subject`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`subject` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`subject` (
  `id_subject` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `subject_name` VARCHAR(1000) NULL,
  `subject_note` VARCHAR(1000) NULL,
  `subject_create_date` DATE NULL,
  PRIMARY KEY (`id_subject`),
  UNIQUE INDEX `id_subject_UNIQUE` (`id_subject` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`labwork`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`labwork` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`labwork` (
  `id_labwork` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `labwork_name` VARCHAR(100) NOT NULL,
  `labwork_bdate` DATE NOT NULL,
  `labwork_edate` DATE NOT NULL,
  `labwork_tlimit` INT NOT NULL,
  `labwork_climit` INT NOT NULL,
  `labwork_note` VARCHAR(5000) NULL,
  `id_system` INT UNSIGNED ZEROFILL NOT NULL,
  `labwork_pcount` INT NOT NULL,
  `labwork_swel` VARCHAR(1000) NOT NULL,
  `labwork_available` INT NOT NULL DEFAULT 1,
  `id_subject` INT UNSIGNED ZEROFILL NOT NULL,
  `labwork_key` VARCHAR(100) NOT NULL,
  `labwork_rift` DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_labwork`),
  UNIQUE INDEX `id_labwork_UNIQUE` (`id_labwork` ASC),
  INDEX `fk_labwork_system1_idx` (`id_system` ASC),
  UNIQUE INDEX `labwork_name_UNIQUE` (`labwork_name` ASC),
  INDEX `fk_labwork_subject1_idx` (`id_subject` ASC),
  CONSTRAINT `fk_labwork_system1`
    FOREIGN KEY (`id_system`)
    REFERENCES `sim_mg`.`system` (`id_system`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_labwork_subject1`
    FOREIGN KEY (`id_subject`)
    REFERENCES `sim_mg`.`subject` (`id_subject`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`problem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`problem` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`problem` (
  `id_problem` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `problem_task` VARCHAR(1000) NOT NULL,
  `problem_tres` VARCHAR(5000) NULL,
  `problem_fres` VARCHAR(5000) NULL,
  `problem_command` VARCHAR(1000) NOT NULL,
  `problem_rwel` VARCHAR(1000) NULL,
  `problem_climit` INT NOT NULL,
  `problem_foul` INT NOT NULL,
  `problem_note` VARCHAR(1000) NULL,
  `id_labwork` INT UNSIGNED ZEROFILL NOT NULL,
  `problem_ind` INT NOT NULL,
  `problem_mscore` INT NOT NULL,
  PRIMARY KEY (`id_problem`),
  UNIQUE INDEX `id_problem_UNIQUE` (`id_problem` ASC),
  INDEX `fk_problem_labwork_idx` (`id_labwork` ASC),
  CONSTRAINT `fk_problem_labwork`
    FOREIGN KEY (`id_labwork`)
    REFERENCES `sim_mg`.`labwork` (`id_labwork`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`subject_has_tgroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`subject_has_tgroup` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`subject_has_tgroup` (
  `id_subject_has_tgroup` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `id_tgroup` INT ZEROFILL UNSIGNED NOT NULL,
  `id_subject` INT UNSIGNED ZEROFILL NOT NULL,
  PRIMARY KEY (`id_subject_has_tgroup`),
  INDEX `fk_labwork_has_group_tgroup1_idx` (`id_tgroup` ASC),
  INDEX `fk_labwork_has_tgroup_subject1_idx` (`id_subject` ASC),
  CONSTRAINT `fk_labwork_has_group_group1`
    FOREIGN KEY (`id_tgroup`)
    REFERENCES `sim_mg`.`tgroup` (`id_tgroup`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_labwork_has_tgroup_subject1`
    FOREIGN KEY (`id_subject`)
    REFERENCES `sim_mg`.`subject` (`id_subject`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`session`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`session` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`session` (
  `id_session` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `id_user` INT ZEROFILL UNSIGNED NOT NULL,
  `id_labwork` INT UNSIGNED ZEROFILL NOT NULL,
  `session_pnumber` INT NOT NULL DEFAULT 1,
  `session_start` DATETIME NOT NULL,
  `session_finish` DATETIME NOT NULL,
  `session_completed` INT NOT NULL DEFAULT 0,
  `session_last_sent` DATETIME NULL,
  `session_attempt_count` INT NULL,
  `session_foul_sum` INT NULL DEFAULT 0,
  `session_total_score` INT NULL DEFAULT 0,
  `session_time_limit` INT NULL DEFAULT 0,
  `session_ten_mark` DECIMAL(10,2) NULL DEFAULT 0,
  `session_percent_mark` DECIMAL(10,2) NULL DEFAULT 0,
  `session_force_quit` INT NULL DEFAULT 0,
  `session_done` INT NULL DEFAULT 0,
  `session_course_name` VARCHAR(100) NULL,
  PRIMARY KEY (`id_session`),
  UNIQUE INDEX `id_session_UNIQUE` (`id_session` ASC),
  INDEX `fk_session_user1_idx` (`id_user` ASC),
  INDEX `fk_session_labwork1_idx` (`id_labwork` ASC),
  CONSTRAINT `fk_session_user1`
    FOREIGN KEY (`id_user`)
    REFERENCES `sim_mg`.`user` (`id_user`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_session_labwork1`
    FOREIGN KEY (`id_labwork`)
    REFERENCES `sim_mg`.`labwork` (`id_labwork`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`statistic`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`statistic` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`statistic` (
  `id_statistic` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `id_session` INT UNSIGNED ZEROFILL NOT NULL,
  `id_problem` INT UNSIGNED ZEROFILL NOT NULL,
  `statistic_accepted` INT NULL DEFAULT 0,
  `statistic_sent_time` DATETIME NULL,
  `statistic_exec_time` TIME NULL,
  `statistic_welcome` VARCHAR(1000) NULL,
  `statistic_command` VARCHAR(1000) NULL,
  `statistic_sent_result` VARCHAR(1000) NULL,
  `statistic_attempt_limit` INT NULL DEFAULT 0,
  PRIMARY KEY (`id_statistic`),
  UNIQUE INDEX `id_statistic_UNIQUE` (`id_statistic` ASC),
  INDEX `fk_statistic_session1_idx` (`id_session` ASC),
  INDEX `fk_statistic_problem1_idx` (`id_problem` ASC),
  CONSTRAINT `fk_statistic_session1`
    FOREIGN KEY (`id_session`)
    REFERENCES `sim_mg`.`session` (`id_session`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_statistic_problem1`
    FOREIGN KEY (`id_problem`)
    REFERENCES `sim_mg`.`problem` (`id_problem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`help`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`help` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`help` (
  `id_help` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `id_system` INT UNSIGNED ZEROFILL NOT NULL,
  `help_command` VARCHAR(100) NULL,
  `help_info` VARCHAR(10000) NULL,
  PRIMARY KEY (`id_help`),
  UNIQUE INDEX `id_help_UNIQUE` (`id_help` ASC),
  INDEX `fk_help_system1_idx` (`id_system` ASC),
  CONSTRAINT `fk_help_system1`
    FOREIGN KEY (`id_system`)
    REFERENCES `sim_mg`.`system` (`id_system`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sim_mg`.`log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sim_mg`.`log` ;

CREATE TABLE IF NOT EXISTS `sim_mg`.`log` (
  `id_log` INT UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `log_dt_in` DATETIME NULL,
  `log_dt_out` DATETIME NULL,
  `log_incoming` VARCHAR(5000) NULL,
  `log_outcoming` VARCHAR(5000) NULL,
  `log_http_referer` VARCHAR(5000) NULL,
  `log_remote_addr` VARCHAR(100) NULL,
  `log_php_self` VARCHAR(100) NULL,
  `log_request_method` VARCHAR(100) NULL,
  PRIMARY KEY (`id_log`),
  UNIQUE INDEX `id_log_UNIQUE` (`id_log` ASC))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
