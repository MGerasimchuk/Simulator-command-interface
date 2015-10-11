<?php


function SetMark (	
					$id,// id курса
					$userid,// id пользователя в Moodle
					$new_value,// оценка
					$name_labwork,//название лабороторной работы
					$moodle_login,
					$moodle_password
				)
{
	#настроки
	$moodle_url_login="https://e.sfu-kras.ru/login/index.php";

	//попытка авторизации
	$postfields = array('username'=>$moodle_login, 'password'=>$moodle_password);


	$ch = curl_init();    // initialize curl handle
	curl_setopt($ch, CURLOPT_URL,$moodle_url_login); // set url to post to
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 4s
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:11.0) Gecko/20100101 Firefox/11.0"); 
	$cookie_file = "cookie1.txt";
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!

	$result = curl_exec($ch);

	$chars = preg_match('/sesskey=(.*)\"/i', $result,$search);

	if (!count($search))
	{
		return 0;//die ("error");
	}

	$action="update";
	$type="value";
	$sesskey=$search[1];

	//-------------------------------------------------
	$url="https://e.sfu-kras.ru/grade/report/singleview/index.php?id=".$id;
	curl_setopt($ch, CURLOPT_URL,$url);
	$result = curl_exec($ch);

	$chars = preg_match("/\"(.*)\">".$name_labwork."/i", $result,$search);
	$itemid=substr( $search[1],strrpos($search[1],"\"")+1,strlen($search[1]));

	$url="https://e.sfu-kras.ru/grade/report/grader/ajax_callbacks.php";
	$postfields = array('id'=>$id, 'userid'=>$userid, 'itemid'=>$itemid, 'action'=>$action,'newvalue'=>$new_value, 'type'=>$type, 'sesskey'=>$sesskey);

	curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$result = curl_exec($ch);
	
	$result=json_decode($result,true);
	if (isset($result['result'])&&$result['result']==="success")
	{
		return 1;
	}
         else 
        {
            return 0;
        }
	
	curl_close($ch);
}

	
?>