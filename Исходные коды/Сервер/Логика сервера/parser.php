<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: text/html; charset=utf-8");
 
/**
 * Распарсить строку по разделителю
 * 
 * @param string $str - входная строка
 * @param string $sep_str - строка содержащая набор разделителей
 * @return string_array - массив строк
 */
function parseBySeparator($str,$sep_str)
{
   
    $sep_str="`".$sep_str;//так как позицию с нуля возвращает, происходит наложения значений в условии false и 0, т.е. если позиция нулевая, он думает что нет элемента в спске
    $result=array();
    $word="";
    $n=0;
    $quote_count=0;//текущее количество кавычек, для того чтобы не разделять если путь находится в кавычках
    
    
    for($i=0;$i<strlen($str);$i++)
    {
        if($str[$i]=='"')
            $quote_count++;
        
        if(strripos($sep_str,$str[$i]) == false 
                || ($str[$i] == ' ' &&  $quote_count%2 != 0))//дабы не разделять на разные аргументы путь(с пробелами) в кавычках
        {
            $word.=$str[$i];
        }
        
        if((strripos($sep_str,$str[$i]) != false) || ($i == strlen($str)-1) )
        {
            if($str[$i] == ' ' &&  $quote_count%2 != 0)//дабы не разделять на разные аргументы путь(с пробелами) в кавычках
                continue;
            
            if(strlen($word)!=0)
            {
                $result[$n]=$word;
                $word="";
                $n++;
            }
        }
    }
    
    
    return $result;
}

/**
 * Поиск параметра в массиве
 * @param string $param - парметр
 * @param string $arr - массив параметров(одномерный)
 * @return boolean - найден или не найден
 */
function findParam($param, $arr)
{
    for($i=0;$i<count($arr);$i++)
    {
        if(strtolower($param) == strtolower($arr[$i]))
        {
            return true;
        }
    }
    
    return false;
}

/**
 * Удаление ключей находящихся в команде
 * 
 * @param string $command - команда
 * @param string $keys - ключи
 * @return boolean - false - в случае если не все ключи найдены
 */
function deleteKey(&$command, $keys)
{
    for($i=0;$i<count($keys);$i++)
    {
        $fl=false;
        for($j=0;$j<count($command);$j++)
        {
            if(strtolower($command[$j])==strtolower($keys[$i]))//СРАВНИВАЕМ В НИЖНЕМ РЕГИСТРЕ
            {
                $fl=true;
                array_splice($command,$j,1);
                $j=0;
            }
        }
        if($fl==false)
        {
            return false;
        }
    }
    
    
    return true;
}

/**
 * Удаление необязательных параметров(ключей) из команды
 * 
 * @param string $command - команда
 * @param string $keys - ключи
 */
function deleteKeyOption(&$command, $keys)
{
    for($i=0;$i<count($keys);$i++)
    {
        for($j=0;$j<count($command);$j++)
        {
            if(strtolower($command[$j])==strtolower($keys[$i]))//СРАВНИВАЕМ В НИЖНЕМ РЕГИСТРЕ
            {
                array_splice($command,$j,1);
                $j=0;
            }
        }
    }
}

/**
 * Метод сравнения поступиышей комманды с эталонныой командой
 * 
 * @param String $incoming_command - поступившая комманда
 * @param String $sample_command - эталон (верный результат)
 * @param String $error_list - список ошибок (неверный результат)
 * 
 * @return Bool - подходит ли команда под описание(эталон)
 */
function compareCommand($incoming_command, $sample_command_list, $result_list, $error_list)
{
    //!!! Примечание
    //если в листе эталонных команд много команд, то ошибки могут выводится некорректно
    //из за того что он до конца списка идет, короче это понять надо!!!
    
    //$incoming_command=strtolower( trim($incoming_command) );
    //$sample_command=strtolower( trim($sample_command) );
    
    /** Разбиваем команду полученную от пользователя */
    $incoming=parseBySeparator($incoming_command," ");
    
    /** Формируем массив сообщений об ошибке 
    * последнйи элемент массива соответствует дефолтной ошибке
    */
    
    
    $sample_command_list=parseBySeparator($sample_command_list,";");
    $error_list=parseBySeparator($error_list, ";");
    $result_list=parseBySeparator($result_list, ";");
   
    $result=array();
    $result["accepted"]=false;
    $result["message"]=$errors[count($errors)-1];
    
    
    for($k=0;$k<count($sample_command_list);$k++)
    {
        $sample_command=$sample_command_list[$k];
        $sample=array();
        $sample_key=array();
        $sample_key_optional=array();
        $s_n=0;
        
        for($i=0;$i<strlen($sample_command);$i++)
        {
            if($sample_command[$i]=="(" || $sample_command[$i]=="{" || $sample_command[$i]=="[")
            {
                $word="";
            }
            if($sample_command[$i]!="(" && $sample_command[$i]!="{" && $sample_command[$i]!="[" && $sample_command[$i]!=")" && $sample_command[$i]!="}" && $sample_command[$i]!="]")
            {
                $word.=$sample_command[$i];
            }


            if($sample_command[$i]==")")
            {
                $sample[$s_n]=parseBySeparator($word,", ");
                $s_n++;
            }
            if($sample_command[$i]=="}")
            {
                $sample_key=parseBySeparator($word,", ");
            }
            if($sample_command[$i]=="]")
            {
                $sample_key_optional=parseBySeparator($word,", ");
            }
        }
        
        
        /**
        * На данном этапе имеется:
        * 
        * $incoming - одномерный массив соответствующий поступившей команде
        * $sample - двумерный массив состоящий из обяхательных эталонных параметров (элементы строки говорят о том что параметр может иметь различные значения)
        * $sample_key - двумерный массив состоящий из обяхательных эталонных параметров которые могут расоплогаться в любом месте комманды
        * $sample_key_optional - двумерный массив состоящий из необязательных эталонных параметров которые могут находиться в любом месте команды
        * #errors - одномерный массив ошибок, где i-ое сообщение соответствует i-ому параметру $incoming последний эл-т это дефолттное сообщение об ошибке
        */

        /*
        echo "ITERATION".$k;
        echo "INCOMING:";
        var_dump($incoming);
        echo "SAMPLE:";
        var_dump($sample);
         echo "SAMPLE_KEY:";
        var_dump($sample_key);
        echo "SAMPLE_KEY OPTIONAL:";
        var_dump($sample_key_optional);
        echo "RESULT_LIST:";
        var_dump($result_list);
        echo "ERROR_LIST:";
        var_dump($error_list);
        */
        
        
        
        $accept_key=true;
        /** Удаляем ключи из команды тем самым проверяя являются ли ключи правильно введенными*/
        if(!deleteKey($incoming,$sample_key))
        {
            $accept_key=false;
            $result["accepted"]=false;
            $result["message"]="Неверно заданы ключи!";
        }
        
        /** Удаляем параметры/ключи являющиеся необязательными для ввода */ 
        deleteKeyOption($incoming,$sample_key_optional);
            
        /** Разбираем неверный результат */
        $errors=array();
        $e_n=0;
        for($i=0;$i<strlen($error_list[$k]);$i++)
        {
            if($error_list[$k][$i]=="(")
            {
                $word="";
            }
            if($error_list[$k][$i]!="(" && $error_list[$k][$i]!=")")
            {
                $word.=$error_list[$k][$i];
            }

            if($error_list[$k][$i]==")")
            {
                $errors[$e_n]=$word;
                $e_n++;
            }
        }
        /** Подставляем текст заместо ссылок в ошибках */
        for($i=0;$i<count($errors);$i++)
        {
            if($i<count($incoming))
            {
                $errors[$i]=str_replace("PAR", $incoming[$i], $errors[$i]);
            }
            $errors[$i]=str_replace("DEF", $errors[count($errors)-1], $errors[$i]);
        }
        
        
        if( ($k==count($sample_command_list)-1) && ((count($incoming) != count($sample)) || ($accept_key==false)))
        {
            if($accept_key==false)
            {
                $result["accepted"]=false;
                $result["message"]="Неверно заданы ключи!";
            }
            else
            {
                $result["accepted"]=false;
                $result["message"]=$errors[count($errors)-1];
            }
            
            //--var_dump($result);
            return $result;
        }
        if($accept_key==false)
        {
            continue;
        }
        
        $result["accepted"]=true;
        
            
        /** проверяем команду */
        for($i=0;$i<count($incoming);$i++)
        {
            if(findParam( $incoming[$i],$sample[$i]) == false)  //СРАВНИВАЕМ В НИЖНЕМ РЕГИСТРЕ
            {
                $result["accepted"]=false;
                if($i<count($errors))
                {
                    $result["message"]=$errors[$i];
                }
                else
                {
                    $result["message"]=$errors[count($errors)-1];
                }
            }
        }
        
        if($result["accepted"] == true)
        {
            $result["message"]=$result_list[$k];
            //--var_dump($result);
            return $result;
        }
    }
    
    //--var_dump($result);
    return $result;
}

?>

