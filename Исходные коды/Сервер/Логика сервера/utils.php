<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: text/html; charset=utf-8");

//Установка временной зоны
date_default_timezone_set('Asia/Krasnoyarsk');

function jsonRemoveUnicodeSequences($struct) {
//return str_replace( "\/","\\",  preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct)) ;   );
    
    writeInLog(NULL, $struct);
    //$struct="c:\temp>";
    $struct=  preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", json_encode($struct));
    
   //return $struct;
   
   
   return  str_replace("\\\\\\\\", "\\\\", $struct);
}
/**
 * Функция обеспечивающая соеденение с БД
 * 
 * @return PDO возвращает PDO объект для работы с БД
 */
function connect()
{
    $user='simmg_user';
    $pass='TKSdUW3nPU5R4PtV';
    $db = new PDO('mysql:host=localhost;dbname=sim_mg', $user, $pass);
    
    $db->query("SET NAMES 'utf8';"); 
    $db->query("SET CHARACTER SET 'utf8';"); 
    $db->query("SET SESSION collation_connection = 'utf8_general_ci';"); 

    return $db;
}


/**
 * Вывод информации о переменной в строку
 * @param type $var - переменная любого типа по которой будет возвращена информация
 * @return string - информация о переменной
 */
function varDumpToString($var) {
    ob_start();
    var_dump($var);
    $result = ob_get_clean();
    return $result;
}

/**
 * Запись в лог файл гет-запроса и ответа сервера
 * 
 * @param string $answ переменная обычно содержит возвращаемую таблицу в json формате
 */
function writeInLog($in, $out)
{
    $db=connect();
    
    $now = date ("Y-m-d H:i:s");
    
    if($out == null)
    {
        $in = varDumpToString($in);
        $http_referer = varDumpToString($_SERVER["HTTP_REFERER"]);
        $remoute_addr = varDumpToString($_SERVER['REMOTE_ADDR']);
        $php_self = varDumpToString($_SERVER['PHP_SELF']);
        $request_method = varDumpToString($_SERVER['REQUEST_METHOD']);
        
        
        
        $stmt = $db->query("INSERT INTO log(log_dt_in, log_incoming, log_http_referer, log_remote_addr, log_php_self, log_request_method) VALUES('$now', '$in', '$http_referer', '$remoute_addr', '$php_self', '$request_method');");
     
        $stmt = $db->query("SELECT LAST_INSERT_ID()");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $_GET['LastInsertIndex'] = $rows[0]['LAST_INSERT_ID()'];
    }
    
    if($in == NULL)
    {
        $id = $_GET['LastInsertIndex'];
        $out = varDumpToString($out);
        $stmt = $db->query("UPDATE log SET log_dt_out = '$now', log_outcoming = '$out' WHERE id_log = $id");
        $stmt = $db->query("SELECT LAST_INSERT_ID()");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

function raplaceEnterToBR($str)
{
    return str_replace('\n', "<br>", $str);
}


function replaceForDB($str)
{
    $str = str_replace("\\","\\\\", str_replace("\\\\","\\" ,$str) );
    return $str;
}
function replaceForClient($str)
{
    $str = str_replace("\\\\","\\", str_replace("\\\\","\\" ,$str) );
    
    return $str;
}

function deleteUser($id_user)
{
    $db=connect();

    $db->query("delete from statistic where id_session in (select id_session from session where id_user='$id_user');");
    $db->query("delete from session where id_user='$id_user';");
    $db->query("delete user from user where id_user = '$id_user';");
}


$act=$_GET['act'];

switch ($act) 
{ 
        case "deleteUser":
            $id_user=$_GET['id_user'];
            deleteUser($id_user);
            break;
}

?>