<?
require('./init.php');
require('./include/global.func.php');
require("./include/admin.func.php");	
$a_purview=$_SESSION["admin_purview"];
for($i=1;$i<100;$i++)
{
	$a_purview.=$i.',';	
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>无标题文档</title>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/chili-1.7.pack.js"></script>
<script type="text/javascript" src="js/jquery.easing.js"></script>
<script type="text/javascript" src="js/jquery.dimensions.js"></script>
<script type="text/javascript" src="js/jquery.accordion.js"></script>
<script language="javascript">
	jQuery().ready(function(){
		/*jQuery('#navigation').accordion({
			header: '.head',
			navigation1: true, 
			event: 'click',
			fillSpace: true,
			animated: 'bounceslide'
		});*/
	});
</script>
<style type="text/css">
<!--
body {
	margin:0px;
	padding:0px;
	font-size: 12px;
}
#navigation {
	margin:0px;
	padding:0px;
	width:147px;
}
#navigation a.head {
	cursor:pointer;
	background:url(images/main_34.gif) no-repeat scroll;
	display:block;
	font-weight:bold;
	margin:0px;
	padding:5px 0 5px;
	text-align:center;
	font-size:12px;
	text-decoration:none;
}
#navigation ul {
	border-width:0px;
	margin:0px;
	padding:0px;
	text-indent:0px;
}
#navigation li {
	list-style:none; display:inline;
}
#navigation li li a {
	display:block;
	font-size:12px;
	text-decoration: none;
	text-align:center;
	padding:3px;
}
#navigation li li a:hover {
	background:url(images/tab_bg.gif) repeat-x;
		border:solid 1px #adb9c2;
}
.STYLE1 {	font-size: 12px;
	color: #000000;
}
a{ color:#000}
-->
</style>
</head>
<body>

<div>

  <ul id="navigation">
  <?
  include_once './include/module.class.php';
  
  $module=new module();
  $result=$module->getMenu(0,$a_purview);
foreach($result as $row)
{
	echo '<li> <a class="head">'.$row['name'].'</a>';
	echo '<ul>';
	$result_1=$module->getMenu($row['id'],$a_purview);
	foreach($result_1 as $row1)
	{
		$link=$row1['link'];
		if(empty($link)) $link="./?act=".$row1['file'];
		echo "<li><a href='$link' target='rightFrame'>$row1[name]</a></li> ";	
	}
	echo '</ul>';	
}

  ?>
 <ul> 
 <li><a href="./?act=jd_tree" target="rightFrame">jd结构图</a></li>
 <li><a href="./?act=proxy" target="rightFrame">代理管理</a></li>
  </ul>
<!--   
<li><a href="./?act=test" target="rightFrame">返利测试</a></li>
 <li> <a class="head">会员管理</a>
      <ul>      
        <li><a href="./?act=member" target="rightFrame">用户管理</a></li>      
        <li><a href="./?act=member&ui=add" target="rightFrame">会员建档</a></li>
         <li><a href="./?act=my_webserv" target="rightFrame">套餐购买审核</a></li>
        <li><a href="#" target="rightFrame">冲销会员</a></li>
        <li><a href="./?act=my_moneylog" target="rightFrame">会员资金流水查询</a></li>
        <li><a href="#" target="rightFrame">会员统计</a></li>
      </ul>
    </li>
    <li> <a class="head">结算管理</a>
      <ul>
        <li><a href="./?act=accountlog" target="rightFrame">总帐户资金查询</a></li>
        <li><a href="./?act=compute" target="rightFrame">计算收益</a></li>
        <li><a href="#" target="rightFrame">日常结算</a></li>
        <li><a href="#" target="rightFrame">结算报表</a></li>
        <li><a href="#" target="rightFrame">结算记录</a></li>
      </ul>
    </li>
    <li> <a class="head">电子币管理</a>
      <ul>
        <li><a href="#" target="rightFrame">电子币充值</a></li>
        <li><a href="#" target="rightFrame">兑换电子币</a></li>
        <li><a href="#" target="rightFrame">电子币转帐</a></li>
         <li><a href="#" target="rightFrame">电子币报表</a></li>
          <li><a href="#" target="rightFrame">电子币台帐</a></li>
         <li><a href="./?act=bikulog" target="rightFrame">币库管理</a></li>
         <li><a href="#" target="rightFrame">币库台帐</a></li>
      </ul>
    </li>


    <li> <a class="head">统计报表</a>
      <ul>
        <li><a href="#" target="rightFrame">分类统计</a></li>
        <li><a href="#" target="rightFrame">收支统计</a></li>
        <li><a href="#" target="rightFrame">盈亏统计</a></li>
      </ul>
    </li> 
    <li> <a class="head">系统管理</a>
      <ul>
		<li><a href="./?act=admin" target="rightFrame">管理员管理</a></li>
        <li><a href="./?act=param" target="rightFrame">参数设置</a></li>
    	<li><a href="./?act=module" target="rightFrame">模块管理</a></li>
       
        <li><a href="./?act=modifypwd" target="rightFrame">修改密码</a></li>
        <li><a href="./?act=login&amp;func=logout"  onClick="return confirm('确定要退出吗？')">退出系统</a></li>
        
        
        <li><a href="#" target="rightFrame">报单奖励设置</a></li>
        <li><a href="#" target="rightFrame">数据备份</a></li>
        <li><a href="#" target="rightFrame">监控管理</a></li>

      </ul>
    </li>
  </ul>-->
</div>
</body>
</html>