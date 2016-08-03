<?php

class AdApp extends MallbaseApp
{
    function index()
    {
	
$this->adv_mod=& m('adv');
	$type = empty($_GET['type']) ? 0 : (int)$_GET['type'];	
	$row=$this->adv_mod->get_cityrow();
	$city_id=$row['city_id'];
	$time=date('Y-m-d H:i:s');
	$result=$this->adv_mod->getrow(
	   "select * from ".DB_PREFIX."adv where 
	   adv_city='$city_id' and type=$type and start_time<='$time' and end_time>='$time' order by riqi desc limit 1
	   ");
	   $im=$result['image'];
	   $lianjie=$result['lianjie'];
	   if(empty($result['image']))
	   {
			if($type==7 || $type==11)//横幅广告
			$url="<a href='#' target='_blank'><img src='/themes/mall/default/styles/default/img/05.png'/></a>";
			if($type==2 || $type==3 || $type==4 || $type==5 || $type==6)//新款上市广告
			$url="<a href='#' target='_blank'><img src='/themes/mall/default/styles/default/img/04.png'/></a>";
			if($type==14 || $type==15)//2F 5F
			$url="<a href='#' target='_blank'><img src='/themes/mall/default/styles/default/img/06.png'/></a>";
			if($type==13)
			$url="<a href='#' target='_blank'><img src='/themes/mall/default/styles/default/img/222.jpg'/></a>";
	   }
	   else
	   {
	   		$url="<a href='$lianjie' target='_blank'><img src='$im'/></a>";
	   }
	    $content=AddSlashes($url); 
	  	echo $this->htmltojs($content);
	  	//echo "document.write($url)";
}

function htmltojs($str)
{
  $re='';
  $str= split("\r\n",$str);
  foreach($str as $v)
  {
	  $re.="document.writeln('".trim($v)."');\r\n";
  }  
  return $re;
}



}
?>