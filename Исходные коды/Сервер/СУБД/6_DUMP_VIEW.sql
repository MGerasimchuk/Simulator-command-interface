DROP view IF EXISTS `vlabwork_total_score`; 
CREATE VIEW `vlabwork_total_score` AS
SELECT id_labwork,  
sum(problem.problem_mscore) AS labwork_total_score
FROM problem 
GROUP BY problem.id_labwork;



DROP view IF EXISTS `vsession_accepted_count`; 
CREATE VIEW `vsession_accepted_count` AS
SELECT id_session, COUNT(*) AS session_accepted_count
FROM statistic 
WHERE statistic_accepted = 1
GROUP BY id_session;



DROP view IF EXISTS `vsession_rejected_count`; 
CREATE VIEW `vsession_rejected_count` AS
SELECT id_session, COUNT(*) AS session_rejected_count
FROM statistic 
WHERE statistic_accepted = 0
GROUP BY id_session;


DROP view IF EXISTS `vsession_all`; 
CREATE VIEW `vsession_all` AS
SELECT
user.id_user, user_login, user_sname, user_fname, user_pname, user_password, user_moodle_id, user_create_date, user_last_accessed,
tgroup.id_tgroup, tgroup_name,

system.id_system, system_name,

subject.id_subject, subject_name, subject_note, subject_create_date,

labwork.id_labwork, labwork_name, labwork_bdate, labwork_edate, labwork_tlimit, labwork_climit, labwork_note, 
labwork_pcount, labwork_swel, labwork_available, labwork_key, labwork_rift,

session.id_session, session_pnumber, session_start, session_finish,  session_last_sent, session_attempt_count, 
session_foul_sum, session_total_score, session_time_limit, session_ten_mark, session_percent_mark, session_force_quit, session_done, session_completed, 
session_course_name, session_completed_by_user,

#CASE WHEN(session_completed = 1) THEN '<font style="color:#32CD32">Да</font>' ELSE '<font style="color:#B22222">Нет</font>' END AS session_completed_text,
#CASE WHEN(session_done = 1) THEN '<font style="color:#32CD32">Да</font>' ELSE '<font style="color:#B22222">Нет</font>' END AS session_done_text,
#CASE WHEN(session_time_limit = 1) THEN '<font style="color:#32CD32">Да</font>' ELSE '<font style="color:#B22222">Нет</font>' END AS session_time_limit_text,
#CASE WHEN(session_force_quit = 1) THEN '<font style="color:#32CD32">Да</font>' ELSE '<font style="color:#B22222">Нет</font>' END AS session_force_quit_text,

(CASE WHEN(vsession_accepted_count.session_accepted_count > 0) THEN vsession_accepted_count.session_accepted_count ELSE 0 END) AS session_accepted_count,
(CASE WHEN(vsession_rejected_count.session_rejected_count > 0) THEN vsession_rejected_count.session_rejected_count ELSE 0 END) AS session_rejected_count,
(CASE WHEN((session_accepted_count+session_rejected_count) > 0) THEN (session_accepted_count+session_rejected_count) ELSE 0 END) AS session_send_count,

vlabwork_total_score.labwork_total_score,

CONCAT(user.user_sname, ' ', user.user_fname, ' ', user.user_pname) AS user_fio, 
(CASE  WHEN (session.session_completed = 1) 
THEN 
	TIMEDIFF(session.session_finish, session.session_start)
ELSE 
	CASE WHEN (TIME_TO_SEC(TIMEDIFF(NOW(), session.session_start))/60 > (labwork.labwork_tlimit + 2)) 
	THEN
		TIMEDIFF(session.session_last_sent, session.session_start)
	ELSE
		TIMEDIFF(NOW(), session.session_start)
	END
END)
AS session_exec_time,
#Столбец в формате отклонено/принято/всего
CONCAT(
'<font style="color:#32CD32">',
(CASE WHEN(vsession_accepted_count.session_accepted_count > 0) THEN vsession_accepted_count.session_accepted_count ELSE 0 END),
'</font>/<font style="color:#B22222">', 
(CASE WHEN(vsession_rejected_count.session_rejected_count > 0) THEN vsession_rejected_count.session_rejected_count ELSE 0 END),
'</font>/', 
(CASE WHEN((session_accepted_count+session_rejected_count) > 0) THEN (session_accepted_count+session_rejected_count) ELSE 0 END)
) AS session_acepted_rejected_send

FROM session 
LEFT OUTER JOIN user ON session.id_user = user.id_user 
LEFT OUTER JOIN tgroup ON tgroup.id_tgroup = user.id_tgroup 
LEFT OUTER JOIN labwork ON labwork.id_labwork = session.id_labwork 
LEFT OUTER JOIN system ON system.id_system = labwork.id_system 
LEFT OUTER JOIN subject ON subject.id_subject = labwork.id_subject
LEFT OUTER JOIN vsession_accepted_count ON vsession_accepted_count.id_session = session.id_session
LEFT OUTER JOIN vsession_rejected_count ON vsession_rejected_count.id_session = session.id_session
LEFT OUTER JOIN vlabwork_total_score ON vlabwork_total_score.id_labwork = session.id_labwork

ORDER BY id_session;