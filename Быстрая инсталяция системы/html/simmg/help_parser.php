<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: text/html; charset=utf-8");

include('utils.php');


function doHelpParseForWindows()
{
    $db=connect();
    $filename = "commandline_utf8.txt";
    
    $file = fopen($filename, "r");
    
    $count = 0;
    $help_all="";
    $fl=1;
    // Читать построчно до конца файла
    while(!feof($file)) { 
        
        $str = fgets($file);
        //echo $str;
        
        if( $fl == 1 )
        {
            $command = trim($str); 
            $fl=0;
            continue;
        }
        
       
        if($str[1] == "=" && $str[5] == "=")
        {
            //echo $command;
            //echo $help;
            
            $db->query("INSERT INTO help(id_system,help_command, help_info) VALUES (1,'$command','$help');");
            
            
            //echo "INSERT INTO help(id_system,help_command, help_info) VALUES (1,'','$help');";
            $help = "";
            $fl=1;
        }
        else
        {
            $help .= $str;
        }
        
    }
    fclose($file);
}






$act=$_GET['act'];

switch ($act) 
{ 
    case "doHelpParseForWindows":
        doHelpParseForWindows();
        break;
}
?>

