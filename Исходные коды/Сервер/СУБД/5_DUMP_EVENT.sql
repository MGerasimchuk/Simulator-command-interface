SET GLOBAL event_scheduler="ON";
SET SQL_SAFE_UPDATES = 0;

DROP EVENT IF EXISTS `UPDATE_DONE_FOR_OLD_SESSION`;
CREATE EVENT `UPDATE_DONE_FOR_OLD_SESSION` ON SCHEDULE EVERY 5 MINUTE ON COMPLETION NOT PRESERVE ENABLE DO 
UPDATE session 
left OUTER JOIN labwork ON labwork.id_labwork = session.id_labwork 
SET session.session_force_quit = (TIME_TO_SEC(TIMEDIFF(NOW(), session.session_start))/60 > (labwork.labwork_tlimit + 2)) 
WHERE session.session_completed = 0;
