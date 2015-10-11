USE `sim_mg`;
DROP procedure IF EXISTS `create_labwork`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `create_labwork` (IN `pname` VARCHAR(100) CHARSET utf8, IN `pbdate` DATE, IN `pedate` DATE, IN `ptlimit` INT, IN `pclimit` INT, IN `pnote` VARCHAR(5000) CHARSET utf8, IN `psystem` VARCHAR(100) CHARSET utf8, IN `ppcount` INT, IN `pid_lw` INT, IN `pswel` VARCHAR(1000) CHARSET utf8, IN `pavailable` INT, IN `pid_subject` INT, IN `pkey` VARCHAR(100) CHARSET utf8, IN `prift` DECIMAL(10,2))
BEGIN

DECLARE pid_sys INT;
DECLARE clw INT;

SET clw=(SELECT COUNT(*) FROM labwork WHERE labwork_name=pname);
SET pid_sys=(SELECT id_system FROM system WHERE system_name=psystem);

IF (pid_sys > 0 and clw=0) THEN
	IF pid_lw>0 THEN
		INSERT INTO labwork(id_labwork,labwork_name,labwork_bdate,labwork_edate,labwork_tlimit,labwork_climit,labwork_note,id_system,labwork_pcount,labwork_swel,labwork_available,id_subject,labwork_key,labwork_rift) 
		VALUES(pid_lw,pname,pbdate,pedate,ptlimit,pclimit,pnote,pid_sys,ppcount,pswel,pavailable,pid_subject,pkey,prift);
		SELECT pid_lw AS 'LAST_INSERT_ID()', pid_lw AS 'LAST_CREATE_LW_ID';
	ELSE
		INSERT INTO labwork(labwork_name,labwork_bdate,labwork_edate,labwork_tlimit,labwork_climit,labwork_note,id_system,labwork_pcount,labwork_swel,labwork_available,id_subject,labwork_key,labwork_rift) 
		VALUES(pname,pbdate,pedate,ptlimit,pclimit,pnote,pid_sys,ppcount,pswel,pavailable,pid_subject,pkey,prift);
		SELECT LAST_INSERT_ID(), LAST_INSERT_ID() AS 'LAST_CREATE_LW_ID';
	END IF;
END IF;
	
END$$
DELIMITER ;

USE `sim_mg`;
DROP procedure IF EXISTS `create_problem`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `create_problem` (IN `ptask` VARCHAR(1000) CHARSET utf8, 
IN `ptres` VARCHAR(1000) CHARSET utf8,
IN `pfres` VARCHAR(1000) CHARSET utf8,
IN `pcommand` VARCHAR(1000) CHARSET utf8,
IN `prwel` VARCHAR(1000) CHARSET utf8,
IN `pclimit` INT,
IN `pfoul` INT,
IN `pnote` VARCHAR(1000) CHARSET utf8,
IN `pid_labwork` INT,
IN `pind` INT,
IN `pmscore` INT
)
BEGIN

DECLARE pid_sys INT;
DECLARE clw INT;

INSERT INTO problem(problem_task,problem_tres,problem_fres,problem_command,problem_rwel,problem_climit,problem_foul,problem_note,id_labwork,problem_ind,problem_mscore)
VALUES(ptask,ptres,pfres,pcommand,prwel,pclimit,pfoul,pnote,pid_labwork,pind,pmscore);
SELECT LAST_INSERT_ID(), LAST_INSERT_ID() AS 'LAST_PR_ID';

END$$
DELIMITER ;



USE `sim_mg`;
DROP procedure IF EXISTS `get_labwork`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `get_labwork` ()
BEGIN
SELECT * from 
labwork
left OUTER JOIN system ON system.id_system=labwork.id_system; 
END$$
DELIMITER ;


USE `sim_mg`;
DROP procedure IF EXISTS `delete_labwork`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `delete_labwork` (IN `pid_labwork` INT)
BEGIN

DELETE FROM statistic WHERE id_problem in (SELECT id_problem FROM problem WHERE id_labwork = pid_labwork);
DELETE FROM problem WHERE id_labwork=pid_labwork;
DELETE FROM session WHERE id_labwork=pid_labwork;
DELETE FROM labwork WHERE id_labwork=pid_labwork;

END$$
DELIMITER ;




USE `sim_mg`;
DROP procedure IF EXISTS `create_session`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `create_session` (IN `plogin` VARCHAR(100) CHARSET utf8,IN `ppass` VARCHAR(1000) CHARSET utf8,IN `pid_lr` INT, 
IN `pstart_time` DATETIME)
BEGIN
DECLARE pid_user INT;
SET pid_user=(SELECT id_user FROM user WHERE user_login=plogin AND user_password=ppass);

IF pid_user >0 THEN
	INSERT INTO session(id_user,id_labwork,session_start,session_last_sent) VALUES(pid_user,pid_lr,pstart_time,pstart_time);
	SELECT LAST_INSERT_ID(), LAST_INSERT_ID() AS 'LAST_SES_ID';
END IF;

END$$
DELIMITER ;


USE `sim_mg`;
DROP procedure IF EXISTS `create_session_by_key`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `create_session_by_key` (IN `plogin` VARCHAR(100) CHARSET utf8,IN `ppass` VARCHAR(1000) CHARSET utf8,IN `lr_key` VARCHAR(100) CHARSET utf8, 
IN `pstart_time` DATETIME,IN `course_name` VARCHAR(100) CHARSET utf8)
BEGIN
DECLARE pid_user INT;
DECLARE pid_lw INT;

SET pid_user=(SELECT id_user FROM user WHERE user_login=plogin AND user_password=ppass);
SET pid_lw=(SELECT id_labwork FROM labwork WHERE labwork_key=lr_key);


IF pid_user >0 THEN
	INSERT INTO session(id_user,id_labwork,session_start,session_last_sent, session_course_name) VALUES(pid_user,pid_lw,pstart_time,pstart_time,course_name);
	SELECT LAST_INSERT_ID(), LAST_INSERT_ID() AS 'LAST_SES_ID';
END IF;

END$$
DELIMITER ;



USE `sim_mg`;
DROP procedure IF EXISTS `get_info_problem`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `get_info_problem` (IN `pid_session` INT)
BEGIN
DECLARE pid_lab INT;
DECLARE pnumber INT;

SET pid_lab=(SELECT id_labwork FROM session WHERE id_session=pid_session);
SET pnumber=(SELECT session_pnumber FROM session WHERE id_session=pid_session);

SELECT problem.problem_task, problem.problem_note, problem.problem_ind, session.session_attempt_count FROM problem 
left OUTER JOIN session ON session.id_session = pid_session 
WHERE problem.id_labwork=pid_lab AND problem.problem_ind=pnumber;

END$$
DELIMITER ;


USE `sim_mg`;
DROP procedure IF EXISTS `get_execute_result`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `get_execute_result` (IN `pid_session` INT)
BEGIN
DECLARE pid_lab INT;
DECLARE pnumber INT;

SET pid_lab=(SELECT id_labwork FROM session WHERE id_session=pid_session);
SET pnumber=(SELECT session_pnumber FROM session WHERE id_session=pid_session);

SELECT problem_twel, problem_command, id_problem, labwork.labwork_pcount 
FROM problem 
left OUTER JOIN labwork  ON labwork.id_labwork=pid_lab
WHERE problem.id_labwork=pid_lab AND problem_ind=pnumber;

END$$
DELIMITER ;



USE `sim_mg`;
DROP procedure IF EXISTS `get_session_info`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `get_session_info` (IN `pid_session` INT)
BEGIN
DECLARE pid_lab INT;
DECLARE pnumber INT;

SET pid_lab=(SELECT id_labwork FROM session WHERE id_session=pid_session);
SET pnumber=(SELECT session_pnumber FROM session WHERE id_session=pid_session);

SELECT *
FROM problem 
left OUTER JOIN labwork ON labwork.id_labwork=pid_lab
left OUTER JOIN session ON session.id_session=pid_session
left OUTER JOIN system ON labwork.id_system=system.id_system
WHERE problem.id_labwork=pid_lab AND problem_ind=pnumber;

END$$
DELIMITER ;



USE `sim_mg`;
DROP procedure IF EXISTS `load_attempt_limit_to_session`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `load_attempt_limit_to_session` (IN `pid_session` INT)
BEGIN
DECLARE max_attempt INT;
DECLARE pid_lb INT;
DECLARE problem_number INT;

SET problem_number=(SELECT session_pnumber FROM session WHERE id_session=pid_session);
SET pid_lb=(SELECT id_labwork FROM session WHERE id_session=pid_session);
SET max_attempt=(SELECT problem_climit FROM problem WHERE id_labwork=pid_lb AND problem_ind=problem_number);

UPDATE session SET session_attempt_count=max_attempt WHERE id_session=pid_session;

END$$
DELIMITER ;


/*
USE `sim_mg`;
DROP procedure IF EXISTS `commit_session_iteration`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `commit_session_iteration` (IN `pid_session` INT, IN `tses_pnumber` INT, IN `tses_finish` DATETIME, IN `tses_completed` INT,
IN `tses_last_sent` DATETIME, IN `tses_attempt_count` INT, IN `tses_foul_sum` INT, IN `tses_total_score` INT,
IN `st_accepted` INT, IN `st_sent_time` DATETIME, IN `st_exec_time` TIME, IN `st_welcome` VARCHAR(1000) CHARSET utf8, 
IN `st_command` VARCHAR(1000) CHARSET utf8, IN `st_sent_result` VARCHAR(1000) CHARSET utf8, IN `st_attempt_limit` INT,
IN `tses_completed_by_user` INT )
BEGIN

DECLARE pid_lb INT;
DECLARE problem_number INT;
DECLARE pid_problem INT;
SET problem_number=(SELECT session_pnumber FROM session WHERE id_session=pid_session);
SET pid_lb=(SELECT id_labwork FROM session WHERE id_session=pid_session);
SET pid_problem=(SELECT id_problem FROM problem WHERE id_labwork=pid_lb AND problem_ind=problem_number);

INSERT INTO statistic(statistic_accepted, statistic_sent_time, statistic_exec_time, statistic_welcome, 
						statistic_command, statistic_sent_result, statistic_attempt_limit, id_session, id_problem)
VALUES (st_accepted, st_sent_time, st_exec_time, st_welcome, st_command, st_sent_result, st_attempt_limit, pid_session, pid_problem);

IF tses_completed = 1 THEN 
	UPDATE session SET session_pnumber=tses_pnumber, session_finish=tses_finish, session_completed=tses_completed, 
	session_last_sent=tses_last_sent, session_attempt_count=tses_attempt_count, session_foul_sum=tses_foul_sum, session_total_score=tses_total_score, 
	session_completed_by_user = tses_completed_by_user
	WHERE id_session=pid_session;
ELSE
	UPDATE session SET session_pnumber=tses_pnumber, session_last_sent=tses_last_sent, session_attempt_count=tses_attempt_count, 
	session_foul_sum=tses_foul_sum
	WHERE id_session=pid_session;
END IF;


IF (st_attempt_limit = 1 OR st_accepted = 1) AND (tses_completed = 0)  THEN
	CALL load_attempt_limit_to_session(pid_session);
END IF;

END$$
DELIMITER ;
*/




USE `sim_mg`;
DROP procedure IF EXISTS `commit_session_iteration`;

DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `commit_session_iteration` (IN `pid_session` INT, IN `tses_pnumber` INT, IN `tses_finish` DATETIME, IN `tses_completed` INT,
IN `tses_last_sent` DATETIME, IN `tses_attempt_count` INT, IN `tses_foul_sum` INT, IN `tses_total_score` INT,
IN `st_accepted` INT, IN `st_sent_time` DATETIME, IN `st_exec_time` TIME, IN `st_welcome` VARCHAR(1000) CHARSET utf8, 
IN `st_command` VARCHAR(1000) CHARSET utf8, IN `st_sent_result` VARCHAR(1000) CHARSET utf8, IN `st_attempt_limit` INT,
IN `tses_ten_mark` DECIMAL(10,2), IN `tses_percent_mark` DECIMAL(10,2), IN `tses_done` INT, IN `tses_completed_by_user` INT )
BEGIN

DECLARE pid_lb INT;
DECLARE problem_number INT;
DECLARE pid_problem INT;
SET problem_number=(SELECT session_pnumber FROM session WHERE id_session=pid_session);
SET pid_lb=(SELECT id_labwork FROM session WHERE id_session=pid_session);
SET pid_problem=(SELECT id_problem FROM problem WHERE id_labwork=pid_lb AND problem_ind=problem_number);

INSERT INTO statistic(statistic_accepted, statistic_sent_time, statistic_exec_time, statistic_welcome, 
						statistic_command, statistic_sent_result, statistic_attempt_limit, id_session, id_problem)
VALUES (st_accepted, st_sent_time, st_exec_time, st_welcome, st_command, st_sent_result, st_attempt_limit, pid_session, pid_problem);

#IF tses_completed = 1 THEN 
	UPDATE session SET session_pnumber=tses_pnumber, session_finish=tses_finish, session_completed=tses_completed, 
	session_last_sent=tses_last_sent, session_attempt_count=tses_attempt_count, session_foul_sum=tses_foul_sum, session_total_score=tses_total_score,
	session_ten_mark = tses_ten_mark, session_percent_mark = tses_percent_mark, session_done = tses_done, session_completed_by_user = tses_completed_by_user
	WHERE id_session=pid_session;
#ELSE
#	UPDATE session SET session_pnumber=tses_pnumber, session_last_sent=tses_last_sent, session_attempt_count=tses_attempt_count, 
#	session_foul_sum=tses_foul_sum, session_total_score=tses_total_score
#	WHERE id_session=pid_session;
#END IF;


IF (st_attempt_limit = 1 OR st_accepted = 1) AND (tses_completed = 0)  THEN
	CALL load_attempt_limit_to_session(pid_session);
END IF;

END$$
DELIMITER ;



USE `sim_mg`;
DROP procedure IF EXISTS `get_labwork_by_key`;
DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `get_labwork_by_key` (IN `lr_key` VARCHAR(100) CHARSET utf8)
BEGIN
SELECT * FROM labwork LEFT OUTER JOIN system ON system.id_system=labwork.id_system  WHERE labwork_key=lr_key;
END$$
DELIMITER ;


USE `sim_mg`;
DROP procedure IF EXISTS `valid_login`;
DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `valid_login` (IN `login` VARCHAR(100) CHARSET utf8)
BEGIN
SELECT COUNT(*) AS count FROM user WHERE user_login=login;
END$$
DELIMITER ;


USE `sim_mg`;
DROP procedure IF EXISTS `get_group_id`;
DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `get_group_id` (IN `group_name` VARCHAR(100) CHARSET utf8)
BEGIN

DECLARE count INT;
SET count=(SELECT COUNT(*) FROM tgroup WHERE tgroup_name=group_name);
IF( count > 0) 
THEN 
	SELECT id_tgroup FROM tgroup WHERE tgroup_name = group_name;
ELSE
	INSERT INTO tgroup(tgroup_name) VALUES(group_name);
	SELECT last_insert_id() AS id_tgroup;
END IF;

END$$
DELIMITER ;


USE `sim_mg`;
DROP procedure IF EXISTS `add_user`;
DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `add_user` (IN `plogin` VARCHAR(100) CHARSET utf8,IN `sname` VARCHAR(100) CHARSET utf8,IN `fname` VARCHAR(100) CHARSET utf8,
IN `pname` VARCHAR(100) CHARSET utf8,IN `password` VARCHAR(1000) CHARSET utf8,IN `id_tgroup` INT,IN `moodle_id` INT,IN `create_date` DATETIME,IN `last_accessed` DATETIME)
BEGIN

INSERT INTO user(user_login,user_sname,user_fname,user_pname,user_password,id_tgroup, user_moodle_id, user_create_date, user_last_accessed) 
VALUES(plogin,sname,fname,pname,password,id_tgroup,moodle_id,create_date,last_accessed);

END$$
DELIMITER ;


USE `sim_mg`;
DROP procedure IF EXISTS `check_labwork_for_user`;
DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `check_labwork_for_user` (IN `plogin` VARCHAR(100) CHARSET utf8,IN `password` VARCHAR(1000) CHARSET utf8, IN `id_lr` VARCHAR(100) CHARSET utf8)
BEGIN

SELECT COUNT(*) AS count
FROM subject_has_tgroup
LEFT OUTER JOIN user ON user_login= plogin AND user_password=password
LEFT OUTER JOIN labwork ON labwork.id_labwork = id_lr
WHERE subject_has_tgroup.id_tgroup = user.id_tgroup AND subject_has_tgroup.id_subject = labwork.id_subject;

END$$
DELIMITER ;


USE `sim_mg`;
DROP procedure IF EXISTS `check_count_labwork_for_user`;
DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `check_count_labwork_for_user` (IN `plogin` VARCHAR(100) CHARSET utf8,IN `password` VARCHAR(1000) CHARSET utf8, IN `id_lr` VARCHAR(100) CHARSET utf8)
BEGIN
SELECT COUNT(*) AS COUNT FROM session WHERE id_user IN (SELECT id_user FROM user WHERE user_login=plogin AND user_password=password) AND id_labwork=id_lr;
END$$
DELIMITER ;



USE `sim_mg`;
DROP procedure IF EXISTS `update_last_accessed`;
DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `update_last_accessed` (IN `id_ses` INT,IN `date` DATETIME)
BEGIN
UPDATE user LEFT OUTER JOIN session ON session.id_session = id_ses SET user.user_last_accessed = date WHERE user.id_user = session.id_user;
END$$
DELIMITER ;




USE `sim_mg`;
DROP procedure IF EXISTS `get_help_info`;
DELIMITER $$
USE `sim_mg`$$
CREATE PROCEDURE `get_help_info` (IN `command` VARCHAR(100) CHARSET utf8,IN `system` VARCHAR(100) CHARSET utf8)
BEGIN
SELECT help_info FROM help WHERE help_command LIKE command and 
                    help.id_system IN (SELECT system.id_system FROM system WHERE system.system_name = system);
END$$
DELIMITER ;