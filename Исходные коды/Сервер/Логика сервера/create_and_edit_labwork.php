<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: text/html; charset=utf-8");

include('utils.php');

/**
 * Возвращает список всех доступных операционных систем для создания ЛР
 * 
 * @return TABLE - таблица доступных ОС
 */
function getSystem()
{
    $db=connect();
    $stmt = $db->query("SELECT * FROM system;");

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows); 
    $db=NULL;
}

/**
 * Создание лабороторной работы с заданными параметрами
 * 
 * @param String $pname - имя лабороторной работы(ЛР)
 * @param Date $pbdate - дата начала выполнения
 * @param Date $pedate - дата окончания выполнения
 * @param Int $ptlimit - ограничение на время выолнения ЛР в минутах
 * @param Int $pclimit - количество попыток выполнения
 * @param String $pnote - прмечание
 * @param String $psystem - операционная система 
 * @param Int $ppcount - количество попыток выполнения
 * @param Int $pid_lw - id ЛР(указывается в том случае, если требется изменить существующую ЛР)
 * @param String $pswel - начальное приглашение
 * @param String $pavailable - доступна ли лабораторная работа
 * @param Int $pid_subject - id предмета
 * @param String $pkey - ключ лабораторной
 * @param Decimal $prift - пороговое значение в % соответствующее минемальном дял прохождения ЛР
 * 
 * @return TABLE_ROW - строка содержащая id ЛР в случае успешного создания или редактирования, null в случае ошибки создания
 */
function createLabwork($pname,$pbdate,$pedate,$ptlimit,$pclimit,$pnote,$psystem,$ppcount,$pid_lw,$pswel,$pavailable,$pid_subject,$pkey,$prift)
{
    $db=connect();
    
    if($pkey=="")
        $pkey=uniqid();
    
    $pname=replaceForDB($pname);
    $pnote=replaceForDB($pnote);
    $pswel=replaceForDB($pswel);
    
    $stmt = $db->query("SET @p0='$pname';");
    $stmt = $db->query("SET @p1='$pbdate';");
    $stmt = $db->query("SET @p2='$pedate';");
    $stmt = $db->query("SET @p3='$ptlimit';");
    $stmt = $db->query("SET @p4='$pclimit';");
    $stmt = $db->query("SET @p5='$pnote';");
    $stmt = $db->query("SET @p6='$psystem';");
    $stmt = $db->query("SET @p7='$ppcount';");
    $stmt = $db->query("SET @p8='$pid_lw';");
    $stmt = $db->query("SET @p9='$pswel';");
    $stmt = $db->query("SET @p10='$pavailable';");
    $stmt = $db->query("SET @p11='$pid_subject';");
    $stmt = $db->query("SET @p12='$pkey';");
    $stmt = $db->query("SET @p13='$prift';");
  
    $stmt = $db->query("CALL `create_labwork`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9, @p10, @p11, @p12, @p13);");
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Создание пункта лабороторной работы
 * 
 * @param String $ptask - задание
 * @param String $ptres - результат соответствующий верно введенной команде
 * @param String $pfres - результат соответствующий не верно введенной команде
 * @param String $pcommand - запись для определения на корректность команды 
 * @param String $prwel - - результирующее приглашение
 * @param Int $pclimit - количество попыток для выполнения
 * @param Int $pfoul - штрафные баллы за неверную попытку
 * @param String $pnote - примечание
 * @param Int $pidlabwork - id ЛР для которой создается пункт
 * @param Int $pind - порядковый номер пункта ЛР начиная с 1
 * @param Int $pmscore - максимальное количество баллов за пункт
 * 
 * @return TABLE_ROW - содержит id созданного пункта лабороторной работы
 */
function createProblem($ptask,$ptres,$pfres,$pcommand,$prwel,$pclimit,$pfoul,$pnote,$pidlabwork,$pind,$pmscore)
{
    $db=connect();
    
    
    
    $ptask=replaceForDB($ptask);
    $ptres=replaceForDB($ptres);
    $pfres=replaceForDB($pfres);  
    $pcommand=replaceForDB($pcommand);
    $prwel=replaceForDB($prwel);
    $ptres=replaceForDB($ptres);
    $pnote=replaceForDB($pnote);
    
    $stmt = $db->query("SET @p0='$ptask';");
    $stmt = $db->query("SET @p1='$ptres';");
    $stmt = $db->query("SET @p2='$pfres';");
    $stmt = $db->query("SET @p3='$pcommand';");
    $stmt = $db->query("SET @p4='$prwel';");
    $stmt = $db->query("SET @p5='$pclimit';");
    $stmt = $db->query("SET @p6='$pfoul';");
    $stmt = $db->query("SET @p7='$pnote';");
    $stmt = $db->query("SET @p8='$pidlabwork';");
    $stmt = $db->query("SET @p9='$pind';");
    $stmt = $db->query("SET @p10='$pmscore';");
       
    
    $stmt = $db->query("CALL `create_problem`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9, @p10);");
 
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получение списка всех лабороторных работ
 * 
 * @return TABLE список всех доступных лабороторных работ
 */
function getLabwork()
{
    $db=connect();
    
    //$stmt = $db->query("CALL `get_labwork`();");
    $stmt = $db->query("SELECT * from labwork left OUTER JOIN subject ON subject.id_subject=labwork.id_subject left OUTER JOIN system ON system.id_system=labwork.id_system;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    /*for($i=0;$i<count($rows);$i++)
    {
        for($j=0;$j<count($rows[$i]);$j++)
        {
            $rows[$i][$j]=replaceForClient($rows[$i][$j]);
        }
    }*/
    
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Получения списка заданий лабороторной работы
 * 
 * @param type $pidlw - id лабороторной работы
 * 
 * @return TABLE - список заданий лабороторной работы
 */
function loadProblem($pidlw)
{
    $db=connect();
    
    $stmt = $db->query("SET @p0='$pidlw';");
    $stmt = $db->query("SELECT * FROM problem WHERE id_labwork=@p0;");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    /*for($i=0;$i<count($rows);$i++)
    {
        for($j=0;$j<count($rows[$i]);$j++)
        {
            $rows[$i][$j]=replaceForClient($rows[$i][$j]);
        }
    }*/
    
    echo jsonRemoveUnicodeSequences($rows);
    
    $db=NULL;
}

/**
 * Удаление лабороторной работы
 * 
 * @param Int $pidlw - id лабороторной работы
 */
function deleteLabwork($pidlw)
{
    $db=connect();
    
    $stmt = $db->query("SET @p0='$pidlw';");
    $stmt = $db->query("CALL `delete_labwork`(@p0);");
    
    $db=NULL;
}

/**
 * Возвращает список предметов
 * 
 * @return TABLE - таблица предметов
 */
function getSubject()
{
    
    $db=connect();
    $stmt = $db->query("SELECT * from subject;");

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo jsonRemoveUnicodeSequences($rows); 
    $db=NULL;
}


/**
 * Добавить предмет
 * 
 * @param type $id_subject
 * @param type $subject_name
 * @param type $subject_note
 */
function editSubject($id_subject, $subject_name, $subject_note)
{
    $db=connect();
    
    if($id_subject!=null)
    {
        $stmt = $db->query("UPDATE subject SET subject_name='$subject_name', subject_note='$subject_note' WHERE id_subject='$id_subject'");
    }
    else 
    {
        $now = date('Y-m-d');
         
        $stmt = $db->query("INSERT INTO subject(subject_name, subject_note, subject_create_date) VALUES('$subject_name', '$subject_note', '$now')");
       
    }
    
    $db=NULL;
}


function linkGroupAndSubject($id_group, $id_subject, $bind)
{
    $db=connect();
   
    if($bind == 1)
    {
          
        $stmt = $db->query("SELECT COUNT(*) AS count FROM subject_has_tgroup WHERE id_tgroup='$id_group' AND id_subject='$id_subject'");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if($rows[0]['count'] == 0)
            $stmt = $db->query("INSERT INTO subject_has_tgroup(id_tgroup, id_subject) VALUES('$id_group', '$id_subject')");
    }
    else
    {
        $stmt = $db->query("DELETE FROM subject_has_tgroup WHERE id_tgroup='$id_group' AND id_subject='$id_subject'");
    }
    
    $db=NULL;
}



writeInLog($_GET, NULL);
$act=$_GET['act'];

switch ($act) 
{
	case "getSystem":
            getSystem();
            break;
            
        case "createLabwork":
            $pname=$_GET['pname'];
            $pbdate=$_GET['pbdate'];
            $pedate=$_GET['pedate'];
            $ptlimit=$_GET['ptlimit'];
            $pclimit=$_GET['pclimit'];
            $pnote=$_GET['pnote'];
            $psystem=$_GET['psystem'];
            $ppcount=$_GET['ppcount'];
            $pid_lw=$_GET['ppid_lw'];
            $pswel=$_GET['pswel'];
            $pavailable=$_GET['pavailable'];
            $pid_subject=$_GET['pid_subject'];
            $pkey=$_GET['pkey'];
            $prift=$_GET['prift'];
            createLabwork($pname,$pbdate,$pedate,$ptlimit,$pclimit,$pnote,$psystem,$ppcount,$pid_lw,$pswel,$pavailable,$pid_subject,$pkey,$prift);
            break;
		
        case "createProblem":
            $ptask=$_GET['ptask'];
            $ptres=$_GET['ptres'];
            $pfres=$_GET['pfres'];
            $pcommand=$_GET['pcommand'];
            $prwel=$_GET['prwel'];
            $pclimit=$_GET['pclimit'];
            $pfoul=$_GET['pfoul'];
            $pnote=$_GET['pnote'];
            $pidlabwork=$_GET['pidlabwork'];
            $pind=$_GET['pind'];
            $pmscore=$_GET['pmscore'];

            createProblem($ptask,$ptres,$pfres,$pcommand,$prwel,$pclimit,$pfoul,$pnote,$pidlabwork,$pind,$pmscore);
            break;
        
        case "getLabwork":
            getLabwork();
            break;
        
        case "loadProblem":
            $pidlw=$_GET['pidlw'];
            loadProblem($pidlw);
            break;
        
        case "deleteLabwork":
            $pidlw=$_GET['pidlw'];
            deleteLabwork($pidlw);
            break;
        
        case "getSubject":
            getSubject();
            break;
        
        case "editSubject":
            $id_subject=$_GET['id_subject'];
            $subject_name=$_GET['subject_name'];
            $subject_note=$_GET['subject_note'];
            
            editSubject($id_subject, $subject_name, $subject_note);
            break;
        
        case "linkGroupAndSubject":
            $id_group=$_GET['id_group'];
            $id_subject=$_GET['id_subject'];
            $bind=$_GET['bind'];
            
        linkGroupAndSubject($id_group, $id_subject, $bind);
        break;
}
?>