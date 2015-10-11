<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: text/html; charset=utf-8");

//Установка временной зоны
date_default_timezone_set('Asia/Krasnoyarsk');

include('utils.php');
include('parser.php');

/**
 * Получить предметы
 * 
 * @return TABLE - информация о предметах
 */
function getSubject()
{
    $db=connect();
    
    $stmt = $db->query("SELECT * from subject ORDER BY subject_name;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получить список лабораторных по дисциплине
 * @param Int $pid_subject - id предмета
 * @return TABLE - список лабораторных работ
 */
function getLabworkBySubject($pid_subject)
{
    $db=connect();
    
    $stmt = $db->query("SELECT * FROM labwork WHERE id_subject = $pid_subject;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получить список групп по лабороторной работе
 * @param Int $pid_group - id ЛР
 * 
 * @return TABLE - список групп
 */
function getGroupByLabwork($pid_labwork)
{
    $db=connect();
    
    $stmt = $db->query("SELECT * from tgroup WHERE id_tgroup IN (SELECT id_tgroup FROM labwork_has_tgroup WHERE id_labwork=$pid_labwork);");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получить список студентов группы
 * @param Int $pid_group - id группы
 * 
 * @return TABLE - ФИО и идентификаторы студентов
 */
function getStudentByGroup($pid_group)
{
    $db=connect();
    
    $stmt = $db->query("SELECT id_user, user_sname, user_fname, user_pname from user WHERE id_tgroup = $pid_group ORDER BY user_sname, user_pname, user_fname;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получить сессии по студенту
 * @param Int $pid_user - id студента
 * 
 * @return TABLE - таблица сессий отсортирванная по дате завершения
 */
function getSessionByStudent($pid_user)
{
    $db=connect();
    
    $stmt = $db->query("SELECT * FROM session AS ses left OUTER JOIN labwork AS lw ON lw.id_labwork = ses.id_labwork where id_user=$pid_user GROUP BY ses.session_finish;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получить статистику по сессии
 * @param Int $pid_session - id сесии
 * 
 * @return TABLE - таблица с статистикой
 */
function getStatisticBySession($pid_session)
{
    $db=connect();
    
    $stmt = $db->query("SELECT * FROM statistic AS stat left OUTER JOIN problem ON stat.id_problem=problem.id_problem WHERE id_session=$pid_session;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}












/**
 * Получить имеющиеся учебные группы
 * 
 * @return TABLE - имена и идентификаторы групп
 */
function getGroup()
{
    $db=connect();
    
    $stmt = $db->query("SELECT * from tgroup ORDER BY tgroup_name;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получить список всех студентов
 * 
 * @return TABLE - отсортирвоанная по ФИО тпблица всех студентов
 */
function getAllStudent()
{
    $db=connect();
    
    $stmt = $db->query("SELECT id_user, user_sname, user_fname, user_pname from user ORDER BY user_sname, user_pname, user_fname;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получить лабороторные
 * 
 * @return TABLE - таблица с лабораторными
 */
function getLabwork()
{
    $db=connect();
    
    $stmt = $db->query("SELECT * FROM labwork;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    
    
    
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получить студентов которые выполняли ЛР
 * @param Int pid_labwork - id ЛР
 * 
 * @return TABLE - таблица студентов отсортированная по ФИО
 */
function getStudentByLabwork($pid_labwork)
{
    $db=connect();
    
    $stmt = $db->query("SELECT id_user, user_sname, user_fname, user_pname, tgroup.tgroup_name FROM user left OUTER JOIN tgroup ON tgroup.id_tgroup=user.id_tgroup WHERE id_user IN (SELECT id_user FROM session WHERE id_labwork = $pid_labwork) GROUP BY user_sname, user_pname, user_fname");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}








/**
 * Получить исходые данные для фильтров в статистике
 * 
 */
function  getParForFilter()
{
    $db=connect();
    
    $stmt = $db->query("SELECT subject_name FROM subject");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result['subject'] = $rows;
    $stmt = $db->query("SELECT system_name FROM system");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result['system'] = $rows;
    $stmt = $db->query("SELECT tgroup_name FROM tgroup");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result['group'] = $rows;
    $stmt = $db->query("SELECT labwork_name FROM labwork");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result['labwork'] = $rows;
    $stmt = $db->query("SELECT DISTINCT session_course_name FROM session");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result['session_course'] = $rows;
    
    echo jsonRemoveUnicodeSequences($result);
}


/**
 * Получить сессию и статистику по условию
 * 
 * @param type $os_list - список операционных систем , пример &os_list=Windows~Linux
 * @param type $group_list - список групп, формат как и в предидущем примере
 * @param type $lw_list - список ЛР, формат как и в предидущем примере
 * @param type $subj_list - список предметов, формат как и в предидущем примере
 * @param type $course_list - список курсов, формат как и в предидущем примере
 * @param type $us - фамилия, может быть передана не полностью а только часть
 * @param type $uf - имя, может быть передана не полностью а только часть
 * @param type $up - отчество, может быть передана не полностью а только часть
 * @param type $ses_bdate - дата начала работы
 * @param type $ses_edate - дата окончания работы
 * @param type $ses_comp_stat - статус завершения выполнения 0 - незавершена 1 - завершена
 * @param type $ses_tl - тайм лимит сессии
 * @param type $ses_force_quit - сессия была завершена принудительно с помощью ивента в БД
 * @param type $ses_done - работа сдана - 1 работа не сдана - 0
 * @param type $best_ses - лучшие сесиии пользователя - 1 все сесии - 0
 * 
 * 
 * ОТВЕТ:
 * массив состоящий из под массивов
 * SESSSION_TABLE в котором расопложенна вся информация о сессиях
 * STATISTIC_TABLE в котором находится вся информация по статистике каждой сессии
 */
function getSessionByPar($os_list, $group_list, $lw_list, $subj_list, $course_list, $us, $uf, $up, $ses_bdate, $ses_edate, $ses_comp_stat, $ses_tl, $ses_force_quit, $ses_done, $best_ses)
{
    $db=connect();
    
    if($best_ses)
        $query = "SELECT *, MAX(session_percent_mark) AS best FROM vsession_all ";
    else
        $query = "SELECT * FROM vsession_all ";
    
    $where = false;
    
    /** добавляем условие на ОС */
    if($os_list != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        $os = parseBySeparator($os_list, "~");
        $query.=" system_name IN (";
        for($i=0;$i<count($os);$i++)
            $query.="'$os[$i]',";
        
        $query[strlen($query)-1] = ")";
    }
    
    /** добавляем условие на ГРУППЫ */
    if($group_list != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        $group = parseBySeparator($group_list, "~");
        $query.=" tgroup_name IN (";
        for($i=0;$i<count($group);$i++)
            $query.="'$group[$i]',";
        
        $query[strlen($query)-1] = ")";
    }
    
    /** добавляем условие на ЛАБЫ */
    if($lw_list != NULL)
    {
        
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
       
        $lw = parseBySeparator($lw_list, "~");
        $query.=" labwork_name IN (";
        for($i=0;$i<count($lw);$i++)
            $query.="'$lw[$i]',";
        
        $query[strlen($query)-1] = ")";
    }
    
    /** добавляем условия на ФИО*/
    if($us != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        $query.=" user_sname LIKE '%$us%'";
    }
    
    if($us != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        $query.=" user_sname LIKE '%$us%'";
    }
    
    
    if($uf != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        $query.=" user_fname LIKE '%$uf%'";
    }
    
    if($up != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        $query.=" user_pname LIKE '%$up%'";
    }
    
    /** добавляем условия на ДАТЫ*/
    if($ses_bdate != NULL && $ses_edate != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        $query.=" session_start BETWEEN '$ses_bdate' AND '$ses_edate'";
    }
    
    /** добавляем условия на СТАТУС СЕСИИ*/
    if($ses_comp_stat != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        switch($ses_comp_stat)
        {
            case 0:
                $query.=" session_completed = 0";
                break;
            case 1:
                $query.=" session_completed = 1";
                break;
        }
    }
    
    /** добавляем условия на TL*/
    if($ses_tl != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        switch($ses_tl)
        {
            case 0:
                $query.=" session_time_limit = 0";
                break;
            case 1:
                $query.=" session_time_limit = 1";
                break;
        }
    }
    
    /** добавляем условия на ПРИНУДИТЕЛЬНОЕ ЗАВЕРШЕНИЕ*/
    if($ses_force_quit != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        switch($ses_force_quit)
        {
            case 0:
                $query.=" session_force_quit = 0";
                break;
            case 1:
                $query.=" session_force_quit = 1";
                break;
        }
    }
    
    /** добавляем условия на ЛР ВЫПОЛНЕНА*/
    if($ses_done != NULL)
    {
        if(!$where) $query.=" WHERE";if($where) $query.=" AND";$where = true;
        
        switch($ses_done)
        {
            case 0:
                $query.=" session_done = 0";
                break;
            case 1:
                $query.=" session_done = 1";
                break;
        }
    }
    if($best_ses)
       $query.= " GROUP BY id_user, id_labwork ORDER BY id_session ";

    $stmt = $db->query($query);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    /*for($i=0;$i<count($result['SESSSION_TABLE']);$i++)
    {
        $id_ses = $result['SESSSION_TABLE'][$i]['id_session'];
        
        
        $stmt = $db->query("SELECT * FROM statistic WHERE id_session = '$id_ses'");
        $result['STATISTIC_TABLE'][$i] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }*/
    
   
   
    
    echo jsonRemoveUnicodeSequences($result);
}

/**
 * Получить логи в диапзоне дат
 * 
 * @param type $begin
 * @param type $end
 * @return TABLE - логи
 */
function getLog($begin, $end)
{
    $db=connect();
    
    $stmt = $db->query("SELECT * FROM log WHERE log_dt_in BETWEEN '$begin' AND '$end'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получить список пользователей
 * 
 * @param type $group - группа
 * @return TABLE - пользователи
 */
function getUser($group)
{
    $db=connect();
    
    $stmt = $db->query("SELECT *, CONCAT(user.user_sname, ' ', user.user_fname, ' ', user.user_pname) AS user_fio "
            . "FROM user LEFT OUTER JOIN tgroup ON user.id_tgroup= tgroup.id_tgroup "
            . "WHERE tgroup_name LIKE '%$group%' "
            . "ORDER BY user_sname, user_fname, user_pname");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}


writeInLog($_GET, NULL);

$act=$_GET['act'];


switch ($act) 
{ 
        case "getSubject":
            getSubject();
            break;
    
        case "getLabworkBySubject":
            $pid_subject=$_GET['pid_subject'];
            getLabworkBySubject($pid_subject);
            break;
        
        case "getGroupByLabwork":
            $pid_labwork=$_GET['pid_labwork'];
            getGroupByLabwork($pid_labwork);
            break;
        
        case "getStudentByGroup":
            $pid_group=$_GET['pid_group'];
            getStudentByGroup($pid_group);
            break;
        
        case "getSessionByStudent":
            $pid_user=$_GET['pid_user'];
            getSessionByStudent($pid_user);
            break;
        
        case "getStatisticBySession":
            $pid_session=$_GET['pid_session'];
            getStatisticBySession($pid_session);
            break;
    
    
        case "getGroup":
            getGroup();
            break;
        
        case "getAllStudent":
            getAllStudent();
            break;
        
        case "getLabwork":
            getLabwork();
            break;
        
        case "getStudentsByLabwork":
            $pid_labwork=$_GET['pid_labwork'];
            getStudentByLabwork($pid_labwork);
            break;
        
        
        
        case "getParForFilter":
            getParForFilter();
            break;
            
            
        case "getSessionByPar":
            $os_list=$_GET['os_list'];
            $group_list=$_GET['group_list'];
            $lw_list=$_GET['lw_list'];
            $subj_list=$_GET['subj_list'];
            $course_list=$_GET['course_list'];
            $us=$_GET['us'];
            $uf=$_GET['uf'];
            $up=$_GET['up'];
            $ses_bdate=$_GET['ses_bdate'];
            $ses_edate=$_GET['ses_edate'];
            $ses_comp_stat=$_GET['ses_comp_stat'];
            $ses_tl=$_GET['ses_tl'];
            $ses_force_quit=$_GET['ses_force_quit'];
            $ses_done=$_GET['ses_done'];
            $best_ses=$_GET['best_ses'];
            
            getSessionByPar($os_list, $group_list, $lw_list, $subj_list, $course_list, $us, $uf, $up, $ses_bdate, $ses_edate, $ses_comp_stat, $ses_tl, $ses_force_quit, $ses_done, $best_ses);
            break;
        
        case "getLog":
            $begin=$_GET['begin'];
            $end=$_GET['end'];
            
            getLog($begin,$end);
            break;
        
        case "getUser":
            $group=$_GET['group'];
            
            getUser($group);
            break;
        
       
}   
?>