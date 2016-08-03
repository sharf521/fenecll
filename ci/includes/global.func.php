<?php

function getuserno($user_id)
{
	$len=strlen($user_id);
	if($len<8)
	{	$str='';
		for($i=0;$i<8-$len;$i++)
		{
			$str.='0'; 	
		}
		return $str.$user_id;
	}
	else
	{
		return $user_id;
	}
}

function sock_open($url,$data=array())
{	
	$row = parse_url($url);
	$host = $row['host'];
	$port = isset($row['port']) ? $row['port']:80;
	
	$post='';//要提交的内容.
	foreach($data as $k=>$v)
	{
		//$post.=$k.'='.$v.'&';
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
	}
	$fp = fsockopen($host, $port, $errno, $errstr, 30); 
	if (!$fp)
	{ 
		echo "$errstr ($errno)<br />\n"; 
	} 
	else 
	{
		$header = "POST $url HTTP/1.1\r\n"; 
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent: MSIE\r\n";
		$header .= "Host: $host\r\n"; 
		$header .= "Content-Length: ".strlen($post)."\r\n";
		$header .= "Connection: Close\r\n\r\n"; 
		$header .= $post."\r\n\r\n";		
		fputs($fp, $header); 
		//$status = stream_get_meta_data($fp);
		
		while (!feof($fp)) 
		{
			$tmp .= fgets($fp, 128);
		}
		fclose($fp);
		$tmp = explode("\r\n\r\n",$tmp);
		unset($tmp[0]);
		$tmp= implode("",$tmp);
		
		/*while (!feof($fp)) 
		{
		 if(($header = fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
			break;
		 }
		}
		$tmp = ""; 
		while (!feof($fp))
		{ 
			$tmp .= fgets($fp, 128); 
		}
		fclose($fp); */
	}
	return $tmp;
}
