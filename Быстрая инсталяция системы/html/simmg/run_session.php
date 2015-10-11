<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: text/html; charset=utf-8");
//Установка временной зоны
date_default_timezone_set('Asia/Krasnoyarsk');

include('parser.php');
include('utils.php');
include('SetMarkMoodle.php');


/**
 * Создание сессии для лабороторной работы
 *
 * @param string $login - логин пользователя
 * @param string $pass - пароль пользователя
 * @param int $lr_key - ключ лабороторной работы для которой требуется создать сессию
 * @param string $course_name - имя курса
 * 
 * $login - логин пользователя
 * $lr_key - ключ лабораторной работы
 * $course_name - имя курса(для кажой ЛР он может быть разным)
 * $group - группа пользователя
 * $identifier - идентификатор пользователя (moodle_id)
 * $full_name - полное имя пользователя
 * 
 * @return TABLE_ROW возвращаетстроку содержащую id созданной сессии 
 * так же имеются флаги:
 * runtime_does_not_fit - 1 если сессия не создана т.к. ЛР еще недоступна
 * labwork_count_limit - 1 если сессия не создана по причине превышения кол-ва попыток выполнения
 * labwork_not_available - 1 если сессия не создана по пречине не доступности ЛР
 * access_denied_for_this_group - 1 если ЛР не доступна для группы данного пользователя
 */
function createSessionByKey($login,$lr_key,$course_name, $group, $identifier, $full_name)
{
    
    $db=connect();
    $str_date = date ("Y-m-d H:i:s");
    
    //$stmt = $db->query("SELECT * FROM labwork LEFT OUTER JOIN system ON system.id_system=labwork.id_system  WHERE labwork_key='$lr_key' ;");
    $stmt = $db->query("SET @p0='$lr_key';");
    $stmt = $db->query("CALL `get_labwork_by_key`(@p0);");
    
    $lr_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pass = $full_name.'|'.$identifier.'|'.$group;
    
    /** ПРОВЕРКА ДОСТУПНА ЛИ ЛР ПО ФЛАГУ ДОСТУПНОСТИ */
    if($lr_info[0]['labwork_available'] == 0)
    {
        $rows[0]['labwork_not_available']=1;
        echo jsonRemoveUnicodeSequences($rows);
        $db=NULL;
        return;
    }
    
    /** ПРОВЕРКА ДОСТУПНА ЛИ ЛР ПО ВРЕМЕНИ */
    $now = date('Y-m-d');
    $date_now = date_create_from_format('Y-m-d', $now);
    $date_begin = date_create_from_format('Y-m-d', $lr_info[0]['labwork_bdate']);
    $date_end = date_create_from_format('Y-m-d', $lr_info[0]['labwork_edate']);
    
    if($date_now<$date_begin || $date_now>$date_end)
    {
        $rows[0]['runtime_does_not_fit']=1;
        echo jsonRemoveUnicodeSequences($rows);
        $db=NULL;
        return;
    }
    
    //$create_enable=0;
    //if(strpos($_SERVER['HTTP_REFERER'], "e.sfu-kras.ru")<10 && strpos($_SERVER['HTTP_REFERER'], "e.sfu-kras.ru") >1)
    //    $create_enable=1;
    
    //$stmt = $db->query("SELECT COUNT(*) AS count FROM user WHERE user_login='$login';");
    $stmt = $db->query("SET @p0='$login';");
    $stmt = $db->query("CALL `valid_login`(@p0);");
    $count = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if($count[0]['count'] == 0)
    {
        //if($create_enable)
        //{ 
            $temp = parseBySeparator($pass, "|");
            $FIO = parseBySeparator($full_name, " ");
            $moodle_id = $identifier;
        
            $stmt = $db->query("SET @p0='$group';");
            $stmt = $db->query("CALL `get_group_id`(@p0);");
            $temp = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $id_tgroup = $temp[0]['id_tgroup'];
            
            
            //$stmt = $db->query("INSERT INTO user(user_login,user_sname,user_fname,user_pname,user_password,id_tgroup, user_moodle_id, user_create_date, user_last_accessed) VALUES('$login','$FIO[0]','$FIO[1]','$FIO[2]','$pass','$id_tgroup','$moodle_id','$str_date','$now_datetime');");
            $stmt = $db->query("SET @p0='$login';");
            $stmt = $db->query("SET @p1='$FIO[0]';");
            $stmt = $db->query("SET @p2='$FIO[1]';");
            $stmt = $db->query("SET @p3='$FIO[2]';");
            $stmt = $db->query("SET @p4='$pass';");
            $stmt = $db->query("SET @p5='$id_tgroup';");
            $stmt = $db->query("SET @p6='$moodle_id';");
            $stmt = $db->query("SET @p7='$str_date';");
            $stmt = $db->query("SET @p8='$now_datetime';");
            
            $stmt = $db->query("CALL `add_user`(@p0,@p1,@p2,@p3,@p4,@p5,@p6,@p7,@p8);");
        ////}
        //else
        //{
        //    $temp[0]['access_denied'] = 1;
        //    echo jsonRemoveUnicodeSequences($temp);
        //    $db=NULL;
        //}
        
    }
    
    /** ПРОВЕРКА ДОСТУПНА ЛИ ЛР ДЛЯ ГРУППЫ ДАННОГО ПОЛЬЗОВАТЕЛЯ */
    
   /* $id_lr=$lr_info[0]['id_labwork'];
//    $stmt = $db->query("SELECT COUNT(*) AS count"
//            . " FROM subject_has_tgroup"
//            . " LEFT OUTER JOIN user ON user_login='$login' AND user_password='$pass'"
//            . " LEFT OUTER JOIN labwork ON labwork.id_labwork = '$id_lr'"
//            . " WHERE subject_has_tgroup.id_tgroup = user.id_tgroup AND subject_has_tgroup.id_subject = labwork.id_subject");
    $stmt = $db->query("SET @p0='$login';");
    $stmt = $db->query("SET @p1='$pass';");
    $stmt = $db->query("SET @p2='$id_lr';");
    $stmt = $db->query("CALL `check_labwork_for_user`(@p0,@p1,@p2);");
    
    $count = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($count[0]['count'] == 0)
    {
        $rows[0]['access_denied_for_this_group']=1;//
        echo jsonRemoveUnicodeSequences($rows);
        $db=NULL;
        return;
    }*/
    
    /** ПРОВЕРКА НЕ ПРЕВЫШЕН ЛИ ЛИМИТ ПО ВЫПОЛНЕНЮ ЛР */
    $id_lr=$lr_info[0]['id_labwork'];
    
    //$stmt = $db->query("SELECT COUNT(*) AS COUNT FROM session WHERE id_user IN (SELECT id_user FROM user WHERE user_login='$login' AND user_password='$pass') AND id_labwork='$id_lr'");  
    $stmt = $db->query("SET @p0='$login';");
    $stmt = $db->query("SET @p1='$pass';");
    $stmt = $db->query("SET @p2='$id_lr';");
    $stmt = $db->query("CALL `check_count_labwork_for_user`(@p0,@p1,@p2);");
    
    $count = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($count[0]['COUNT'] >= $lr_info[0]['labwork_climit'])
    {
        $rows[0]['labwork_count_limit']=1;
        echo jsonRemoveUnicodeSequences($rows);
        $db=NULL;
        return;
    }
    
   
    
    $stmt = $db->query("SET @p0='$login';");
    $stmt = $db->query("SET @p1='$pass';");
    $stmt = $db->query("SET @p2='$lr_key';");
    $stmt = $db->query("SET @p3='$str_date';");
    $stmt = $db->query("SET @p4='$course_name';");
    $stmt = $db->query("CALL `create_session_by_key`(@p0, @p1, @p2, @p3, @p4);");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    $id_session = $rows[0]['LAST_INSERT_ID()'];
    $stmt = $db->query("SET @p0='$id_session';");
    $stmt = $db->query("CALL `load_attempt_limit_to_session`(@p0);");
    
    /** Обновить время последнего обращения пользователя к сиситеме */
    $id_session = $rows[0]['LAST_INSERT_ID()'];
    
    //$stmt = $db->query("UPDATE user LEFT OUTER JOIN session ON session.id_session = '$id_session' SET user.user_last_accessed = '$str_date' WHERE user.id_user = session.id_user;");
    $stmt = $db->query("SET @p0='$id_session';");
    $stmt = $db->query("SET @p1='$str_date';");
    $stmt = $db->query("CALL `update_last_accessed`(@p0, @p1);");
    
    echo jsonRemoveUnicodeSequences($rows);

}

/**
 * Получение списка всех лабороторных работ
 * 
 * @return TABLE список всех доступных лабороторных работ
 */
function getLabwork()
{
    $db=connect();
    
    $stmt = $db->query("CALL `get_labwork`();");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получение информации по текущему пункту задания в сессии
 * 
 * @param int $id_session - идентификатор сесии
 * @return TABLE_ROW - информация по текущему заданю, которая требуется ля клиента
 */
function getInfoProblem($id_session)
{
    $db=connect();
    $stmt = $db->query("SET @p0='$id_session';");
    $stmt = $db->query("CALL `get_info_problem`(@p0);");
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}


/**
 * Получение информации по лабораторной работе
 * 
 * @param int $lw_key - ключ лабораторной работы
 * @return TABLE_ROW - информация по лабораторной работе
 */
function getLabworkInfo($lw_key)
{
    $db=connect();
    //$stmt = $db->query("SELECT * FROM labwork left OUTER JOIN system ON system.id_system=labwork.id_system  WHERE labwork_key='$lw_key' ;");
    $stmt = $db->query("SET @p0='$lw_key';");
    $stmt = $db->query("CALL `get_labwork_by_key`(@p0);");
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}




/**
 * Получить результат выполнения комманды которую ввел пользователь на клиенте
 * 
 * @param int $id_session - идентификатор сессии
 * @param string $command - команнда полученная от клиента
 * @param string $wel - текущее приглашение на клиенте
 * 
 * @return TABLE_ROW - содержится результат выполнения команды а так же набор флагов сигнализирующих о наступивших событиях
 * 
 * //описать флаги
 */
function getExecuteResult($id_session,$command,$wel, $login, $group, $identifier, $full_name, $course_id)
{
    $db=connect();    
    
    $pass = $full_name.'|'.$identifier.'|'.$group;
    
    $stmt = $db->query("SET @p0='$id_session';");
    $stmt = $db->query("CALL `get_session_info`(@p0);");
    /* @var $session_info TABLE_ROW - содержит полную информацию о текущей сессии включая все поля текущего пункта сессии и лабороторной работы */
    $session_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    /** проверка тот ли пользователь выполняет сессию */
    /*$id_user = $session_info[0]['id_user'];
    $stmt = $db->query("SELECT COUNT(*) AS count FROM user WHERE id_user='$id_user' AND user_login='$login' AND user_password='$pass';");
    $temp = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if($temp[0]['count'] == 0)
    {
        $result[0]['bad_login_or_pass'] = 1;
        echo jsonRemoveUnicodeSequences($result);
        return;
    }*/
    
    
    /** @var $now_datetime DateTime - определение времени прихода команды на сервер, в дальнейшем используется для определения времени выполнения команды */
    $now_datetime = date ("Y-m-d H:i:s");
    
    /** Обновить время последнего обращения пользователя к сиситеме */
    //$stmt = $db->query("UPDATE user LEFT OUTER JOIN session ON session.id_session = '$id_session' SET user.user_last_accessed = '$now_datetime' WHERE user.id_user = session.id_user;");
    $stmt = $db->query("SET @p0='$id_session';");
    $stmt = $db->query("SET @p1='$now_datetime';");
    $stmt = $db->query("CALL `update_last_accessed`(@p0, @p1);");
    
    /** Определение времени выполнения текущей лабороторной работы(не превышен ли лимит) */
    $now = date_create_from_format('Y-m-d H:i:s', $now_datetime);
    $start = date_create_from_format('Y-m-d H:i:s', $session_info[0]['session_start']);
    $interval = date_diff($now, $start);
    
    if($interval->format('%y') > 0 || $interval->format('%m') > 0 || $interval->format('%d') > 1 || 
            (($interval->format('%h')*60)+$interval->format('%i')) >= $session_info[0]['labwork_tlimit'])
    {
         $result[0]['time_limit'] = 1;
         $id_session=(int)$id_session;
         $stmt = $db->query("UPDATE session SET session_completed=1, session_time_limit=1, session_finish='$now_datetime' WHERE id_session=$id_session;");
    }
    else 
    {
        $result[0]['time_limit'] = 0;
    }
    
    /** Если в текущей сессии выполнения ЛР еще остались пункты и осталось время то переходим к анализу входных данных */
    if($session_info[0]['session_pnumber'] <= $session_info[0]['labwork_pcount'] && $result[0]['time_limit'] == 0 && $session_info[0]['session_completed'] != 1)
    {
        $result[0]['is_help'] = 0;
        /** Проверяем является ли полученная команда запросом справки */
        $helpResult = isHelp($command, $session_info[0]['system_name']);
        if($helpResult != null || strtolower($command) == "exit")
        {
            $result[0]['result'] = $helpResult;
            $result[0]['problem_rwel'] = $wel;
            $result[0]['accept']=0;
            $result[0]['attemp_limit'] = 0;
            
            if($helpResult != null)
                $result[0]['is_help'] = 1;
            
            $session_foul_sum = 0;
            $session_total_score = $session_info[0]['session_total_score'];
            
            $session_info[0]['session_attempt_count'] ++;
        }
        else
        {
            /** Если команда полученная от клиента не является запросом справки по команде */
            $compare_result=compareCommand($command, $session_info[0]['problem_command'], $session_info[0]['problem_tres'], $session_info[0]['problem_fres']);
            /** Является ли введенная пользователем команда верной, и соответствует ли приглашение */
            if($compare_result['accepted'] == true)
            {
                $result[0]['result'] = $compare_result['message'];//$session_info[0]['problem_tres'];
                $result[0]['problem_rwel'] = $session_info[0]['problem_rwel'];
                $result[0]['accept']=1;
                $result[0]['attemp_limit'] = 0;
                $session_foul_sum = $session_info[0]['session_foul_sum'];
                $session_total_score=$session_info[0]['session_total_score'] + $session_info[0]['problem_mscore'];
            }
            else
            {
                $result[0]['result'] = $compare_result['message'];
                $result[0]['problem_rwel'] = $wel;
                $result[0]['accept']=0;
                $result[0]['attemp_limit'] = 0;

                
                
                $session_foul_sum = $session_info[0]['session_foul_sum'] + $session_info[0]['problem_foul'];
                $session_total_score = $session_info[0]['session_total_score'];
                
                if($session_info[0]['session_attempt_count'] < 2)
                {
                    $result[0]['attemp_limit']=1;
                    $result[0]['problem_rwel'] = $session_info[0]['problem_rwel'];
                    //не считать штрафные баллы за невыполненные пункты
                    $session_foul_sum = $session_foul_sum - $session_info[0]['problem_foul'] * $session_info[0]['problem_climit'];
                }
            }
        }
        
        /** Время выполнения пункта работы */
        $now = date_create_from_format('Y-m-d H:i:s', $now_datetime);
        $last_sent = date_create_from_format('Y-m-d H:i:s', $session_info[0]['session_last_sent']);
        $interval = date_diff($now, $last_sent);
        $exec_time = $interval->format('%h:%i:%s');

        /** Будет ли произведен переход на следующий пункт работы */
        if(($result[0]['attemp_limit'] == 0 && $result[0]['accept'] == 1) || ($result[0]['attemp_limit'] == 1))
        {
            $session_pnumber = $session_info[0]['session_pnumber'] + 1;
        }
        else
        {
            $session_pnumber = $session_info[0]['session_pnumber'];
        }
        
        /** Завершено ли выполнение ЛР */
        if(($session_pnumber > $session_info[0]['labwork_pcount']))
        {    
            $session_pnumber = $session_info[0]['labwork_pcount'];
            $result[0]['completed'] = 1;
            
        }
        else
        {
            $result[0]['completed'] = 0;
        }
        
        if(strtolower($command) == "exit")
        {    
            $result[0]['completed_by_user'] = 1;
            $result[0]['completed'] = 1;
        }
          
        
        $result[0]['attempt_count'] = $session_info[0]['session_attempt_count'] - 1;
        
        $completed = $result[0]['completed'];
        $finish_time = $now_datetime;
        $last_sent = $now_datetime;
        $session_attempt_count = $result[0]['attempt_count'];
        $accepted = $result[0]['accept'];
        $is_attemp_limit = $result[0]['attemp_limit'];
        $st_result = $result[0]['result'];
        $completed_by_user = $result[0]['completed_by_user'];
        
        $id_labwork = (int)$session_info[0]['id_labwork'];
        $stmt = $db->query("SELECT sum(problem_mscore) AS sum FROM problem WHERE id_labwork = '$id_labwork';");
        $temp = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $all_score = $temp[0]['sum'];
        $score = $session_total_score-$session_foul_sum;
        if($score<0)
            $score = 0;
        
        $ten_mark = round((10.0 / $all_score) * $score, 2);
        $percent_mark = round((100.0 / $all_score) * $score, 2);
        $done = 0;
        
        if($percent_mark > $session_info[0]['labwork_rift'])
            $done = 1;
        
        $stmt = $db->query("SET @p0='$id_session';");
        $stmt = $db->query("SET @p1='$session_pnumber';");
        $stmt = $db->query("SET @p2='$finish_time';");
        $stmt = $db->query("SET @p3='$completed';");
        $stmt = $db->query("SET @p4='$last_sent';");
        $stmt = $db->query("SET @p5='$session_attempt_count';");
        $stmt = $db->query("SET @p6='$session_foul_sum';");
        $stmt = $db->query("SET @p7='$session_total_score';");
        $stmt = $db->query("SET @p8='$accepted';");
        $stmt = $db->query("SET @p9='$now_datetime';");
        $stmt = $db->query("SET @p10='$exec_time';");
        $stmt = $db->query("SET @p11='$wel';");
        $stmt = $db->query("SET @p12='$command';");
        $stmt = $db->query("SET @p13='$st_result';");
        $stmt = $db->query("SET @p14='$is_attemp_limit';");
        $stmt = $db->query("SET @p15='$ten_mark';");
        $stmt = $db->query("SET @p16='$percent_mark';");
        $stmt = $db->query("SET @p17='$done';");
        $stmt = $db->query("SET @p18='$completed_by_user';");
        
        
   
        //echo "CALL commit_session_iteration($id_session,$session_pnumber,$finish_time,$completed,$last_sent,$session_attempt_count,$session_foul_sum,$session_total_score,$accepted,$now_datetime,$exec_time,$wel,$command,$st_result,$is_attemp_limit);";

        /** Фиксируем статистику и данные сессии в БД */
        $stmt = $db->query("CALL `commit_session_iteration`(@p0,@p1,@p2,@p3,@p4,@p5,@p6,@p7,@p8,@p9,@p10,@p11,@p12,@p13,@p14,@p15,@p16,@p17,@p18);");
        
        if($completed == 1 || $completed_by_user == 1)
        {
            SetMark($course_id, $identifier, round($percent_mark), $session_info[0]['labwork_name'], "asidorov", "vfhfpv");
        }
        
        /** Подгрузка информации по текущему заданию */
        $stmt = $db->query("SET @p0='$id_session';");
        $stmt = $db->query("CALL `get_info_problem`(@p0);");
        $problem_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result[0]['problem_task'] = $problem_info[0]['problem_task'];
        $result[0]['problem_note'] = $problem_info[0]['problem_note'];
        $result[0]['problem_ind'] = $problem_info[0]['problem_ind'];
        $result[0]['attempt_count'] = $problem_info[0]['session_attempt_count'];
        //$result[0]['problem_note'] =  $_SERVER['REMOTE_ADDR'];
        $result[0]['percent_mark'] = round($percent_mark);
        $result[0]['done'] = $done;   
    }
    else
    {
       $result[0]['completed'] = 1;
    }
  
    echo jsonRemoveUnicodeSequences($result);
    $db=NULL;
}

function isHelp($command, $system)
{
    if($system == "Windows")
    {    
        $command_list=parseBySeparator($command," ");
        
        if(count($command_list) == 1 && $command_list[0]=="help")
            $command_list[1] = "help";
        
        //var_dump($command_list);
        if(count($command_list) != 2)
            return null;
        
        if(strtolower($command_list[0]) == "help" || $command_list[1] == "/?")
        {
            $com_ind=1;
            if(strtolower($command_list[1]) == "/?")
                $com_ind=0;
            
            $db = connect();
            
            $stmt = $db->query("SET @p0='$command_list[$com_ind]';");
            $stmt = $db->query("SET @p1='$system';");
            $stmt = $db->query("CALL `get_help_info'(@p0,@p1);");
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(count($rows)==0)
            {
                //echo '"'.$command_list[$com_ind].'" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.';
                return '"'.$command_list[$com_ind].'" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.';
            }
            else
            {
                //echo $rows[0]['help_info'];
                return $rows[0]['help_info'];
            }
        }
        else
        {
            return null;
        }
       
    }
    
    if($system == "Linux")
    {    
        $command_list=parseBySeparator($command," ");
        
        if(count($command_list) != 2)
            return null;
        
        
        
        if(strtolower($command_list[0]) == "man" || strtolower($command_list[1]) == "-h" || strtolower($command_list[1]) == "--help" 
                || strtolower($command_list[1]) == "help" || strtolower($command_list[0]) == "help")
        {
            
            $db = connect();
//            $stmt = $db->query("SELECT help_info FROM help WHERE help_command LIKE '$command_list[1]' and "
//                    . "help.id_system IN (SELECT system.id_system FROM system WHERE system.system_name = '$system')");
            
            
            $com_ind=1;
            if(strtolower($command_list[0]) != "man" && strtolower($command_list[0]) != "help")
                $com_ind=0;
            
            $stmt = $db->query("SET @p0='$command_list[$com_ind]';");
            $stmt = $db->query("SET @p1='$system';");
            $stmt = $db->query("CALL `get_help_info`(@p0,@p1);");
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
            if(count($rows)==0)
            {
                //echo '"'.$command_list[$com_ind].'" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.';
                return '"'.$command_list[$com_ind].'" не является внутренней или внешней командой, исполняемой программой или пакетным файлом.';
            }
            else
            {
                //echo $rows[0]['help_info'];
                return $rows[0]['help_info'];
            }
        }
        else
        {
            return null;
        }
       
    }
    
    
    return null;
    
        
    
    
    
}



//phpinfo();
writeInLog($_GET, NULL);
        
$act=$_GET['act'];

switch ($act) 
{ 
        case "createSession":
            $login=$_GET['login'];
            $pass=$_GET['pass'];
            $id_lr=(int)$_GET['id_lr'];
            createSession($login,$pass,$id_lr);
            break;
        
        case "createSessionByKey":
            $login=$_GET['login'];
            $lr_key=$_GET['lr_key'];
            $course_name=$_GET['course_name'];
            $group=$_GET['group'];
            $email=$_GET['email'];
            $identifier=$_GET['identifier'];
            $full_name=$_GET['full_name'];
            
            createSessionByKey($login,$lr_key,$course_name,$group,$identifier,$full_name);
            break;
        
        case "getLabwork":
            getLabwork();
            break;
        
        case "getInfoProblem":
            $id_session=(int)$_GET['id_session'];
            getInfoProblem($id_session);
            break;
        
        case "getLabworkInfo":
            $lw_key=$_GET['lw_key'];
            getLabworkInfo($lw_key);
            break;
        
	case "getExecuteResult":
            $id_session=(int)$_GET['id_session'];
            $command=$_GET['command'];
            $wel=$_GET['wel'];
            $login=$_GET['login'];
            $group=$_GET['group'];
            $identifier=$_GET['identifier'];
            $full_name=$_GET['full_name'];
            $course_id=$_GET['course_id'];
            
            getExecuteResult($id_session,$command,$wel, $login, $group, $identifier, $full_name, $course_id);
            break;
        
        case "isHelp":
            $command=$_GET['command'];
            $system=$_GET['system'];
            isHelp($command, $system);
            break;
        
}
?>