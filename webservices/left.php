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
<title>�ޱ����ĵ�</title>
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
 <li><a href="./?act=jd_tree" target="rightFrame">jd�ṹͼ</a></li>
 <li><a href="./?act=proxy" target="rightFrame">��������</a></li>
  </ul>
<!--   
<li><a href="./?act=test" target="rightFrame">��������</a></li>
 <li> <a class="head">��Ա����</a>
      <ul>      
        <li><a href="./?act=member" target="rightFrame">�û�����</a></li>      
        <li><a href="./?act=member&ui=add" target="rightFrame">��Ա����</a></li>
         <li><a href="./?act=my_webserv" target="rightFrame">�ײ͹������</a></li>
        <li><a href="#" target="rightFrame">������Ա</a></li>
        <li><a href="./?act=my_moneylog" target="rightFrame">��Ա�ʽ���ˮ��ѯ</a></li>
        <li><a href="#" target="rightFrame">��Աͳ��</a></li>
      </ul>
    </li>
    <li> <a class="head">�������</a>
      <ul>
        <li><a href="./?act=accountlog" target="rightFrame">���ʻ��ʽ��ѯ</a></li>
        <li><a href="./?act=compute" target="rightFrame">��������</a></li>
        <li><a href="#" target="rightFrame">�ճ�����</a></li>
        <li><a href="#" target="rightFrame">���㱨��</a></li>
        <li><a href="#" target="rightFrame">�����¼</a></li>
      </ul>
    </li>
    <li> <a class="head">���ӱҹ���</a>
      <ul>
        <li><a href="#" target="rightFrame">���ӱҳ�ֵ</a></li>
        <li><a href="#" target="rightFrame">�һ����ӱ�</a></li>
        <li><a href="#" target="rightFrame">���ӱ�ת��</a></li>
         <li><a href="#" target="rightFrame">���ӱұ���</a></li>
          <li><a href="#" target="rightFrame">���ӱ�̨��</a></li>
         <li><a href="./?act=bikulog" target="rightFrame">�ҿ����</a></li>
         <li><a href="#" target="rightFrame">�ҿ�̨��</a></li>
      </ul>
    </li>


    <li> <a class="head">ͳ�Ʊ���</a>
      <ul>
        <li><a href="#" target="rightFrame">����ͳ��</a></li>
        <li><a href="#" target="rightFrame">��֧ͳ��</a></li>
        <li><a href="#" target="rightFrame">ӯ��ͳ��</a></li>
      </ul>
    </li> 
    <li> <a class="head">ϵͳ����</a>
      <ul>
		<li><a href="./?act=admin" target="rightFrame">����Ա����</a></li>
        <li><a href="./?act=param" target="rightFrame">��������</a></li>
    	<li><a href="./?act=module" target="rightFrame">ģ�����</a></li>
       
        <li><a href="./?act=modifypwd" target="rightFrame">�޸�����</a></li>
        <li><a href="./?act=login&amp;func=logout"  onClick="return confirm('ȷ��Ҫ�˳���')">�˳�ϵͳ</a></li>
        
        
        <li><a href="#" target="rightFrame">������������</a></li>
        <li><a href="#" target="rightFrame">���ݱ���</a></li>
        <li><a href="#" target="rightFrame">��ع���</a></li>

      </ul>
    </li>
  </ul>-->
</div>
</body>
</html>