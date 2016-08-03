<?
require_once('../init.php');
//$result=$db->get_all("");
//$row=$db->get_one('');

 
						
$url=$_SERVER['HTTP_HOST'];
$u=explode('.',$url);
if(count($u)>2)
{
	$url=$u[1].'.'.$u[2];
}
$row=$db->get_one("select banquan,icp_num,city_id,city_title,qq1,qq2,qq3,qq4 from {city} where city_yuming like '%$url%' limit 1");
$city_id=$row['city_id'];	
						
?>	



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>服装专题-<?php echo $row['city_title'] ?></title>
<link href="css/style.css" rel="stylesheet" />
<SCRIPT type=text/javascript src="com_files/jquery-1.4.4.min.js"></SCRIPT>
<script type="text/javascript" src="/themes/mall/default/styles/default/js/jquery.scrollLoading.js" charset="utf-8"></script>

<script type="text/javascript">
<!--

var qqArr = new Array();
var nameArr = new Array();
qqArr.push("<?php echo $row['qq1'] ?>");
nameArr.push("售前咨询1");
qqArr.push("<?php echo $row['qq2'] ?>");
nameArr.push("售前咨询2");
qqArr.push("<?php echo $row['qq3'] ?>");
nameArr.push("技术咨询");
qqArr.push("<?php echo $row['qq4'] ?>");
nameArr.push("投诉反馈");

//-->
</script>
<script type="text/javascript" src="/themes/mall/default/styles/default/js/kf.js" charset="gb2312"></script>
</head>

<body>
<style type="text/css">  
    .scrollloading{background:url(/themes/mall/default/styles/default/img/quan.gif) no-repeat center; } 
		
</style> 
						

				<div class="topbox">
					<div class="topbg">				
						<div class="pbox"><img src="img/wu.jpg" /><a href="/index.php">返回首页</a></div>
						<div class="pbox"><img src="img/phone.jpg" /><a href="/index.php?app=article&cate_id=24">手机客户端下载</a></div>
						<div class="xianbox"></div>
						<div class="pbox"><div class="mt"><img  src="img/sian.jpg" /></div><a href="/index.php?app=message&act=newpm">站内消息</a></div>
						
						<div class="rightbox">
							
							<div class="pbox"><img src="img/help.jpg"  style="margin-top:8px;"/><a href="/index.php?app=article&code=$acc_help">帮助中心</a></div>
							<!--<div class="pbox"><img src="img/er.jpg" style="margin-top:10px;" /><a href="#">在线客服</a></div>-->
							<div class="pbox"><img src="img/shi.jpg" style="margin-top:10px;" /><a href="/index.php?app=article&cate_id=23">视频</a>&nbsp;&nbsp;&nbsp;</div>
					<?php 
					$userid=$_SESSION['user_info']['user_id'];
					if(empty($userid))	
					{
					?>		
					<div class="pbox"><a href="/index.php?app=member&act=login&ret_url=$ret_url"><font style="color:#CC0000">登录</font></a></div>			
				
					<div class="pbox"><a href="/index.php?app=member&act=register&ret_url=$ret_url"><font style="color:#CC0000">注册</font></a></div>					
						<?php 
						}
						else
						{
						?>
						 
						 <div class="pbox">您好,<?php echo $_SESSION['user_info']['user_name'] ?>&nbsp;&nbsp;<a href="/index.php?app=member">用户中心</a></div>
            <div class="pbox"><a href="/index.php?app=member&act=logout">退出</a></div>
            			<?php 
						}
						?>
						</div>		
					
					</div>
					
					</div>
					
					
				
					
					
					<div class="qppicbox"><a href="#"><img src="img/bpic.jpg" /></a></div>
					
					<div class="container">
						<div class="contbox">
							<ul class="allpicbox">
								<li><a href="#"><img src="img/xx.jpg" /></a></li>
								<li><a href="#"><img src="img/xx.jpg" /></a></li>
								<li><a href="#"><img src="img/xx.jpg" /></a></li>
						
						
							</ul>
					
						
							<div class="starshopbox">
						
						<div class="ssbt">
							<div class="menutitle">品牌旗舰店</div>
							<span class="morecss">	
							
													                         
								<a href="/index.php?app=search&act=index&keyword=风衣" target="_blank">风衣</a> | 
								<a href="/index.php?app=search&act=index&keyword=羽绒服" target="_blank">羽绒服</a> | 
								<a href="/index.php?app=search&act=index&keyword=连衣裙" target="_blank">连衣裙</a> | 
								<a href="/index.php?app=search&act=index&keyword=牛仔裤" target="_blank">牛仔裤</a> | 
								<a href="/index.php?app=search&act=index&keyword=运动服" target="_blank">运动服</a> | 
								<a href="/index.php?app=search&act=index&keyword=西装" target="_blank">西装</a> | 
								<a href="/index.php?app=search&act=index&keyword=短袖" target="_blank">短袖</a> | 
								<a href="/index.php?app=search&act=qijian&lei=1&cate_id=1" target="_blank">更多>></a>
							</span>
							
									
						</div>
						
						<ul class="shopbox">
						
						<?php 
							
							$result=$db->get_all("select s.store_name,s.owner_name,s.store_logo,s.store_id from {store} s left join {category_store} c on s.store_id=c.store_id left join {scategory} sc on sc.cate_id=c.cate_id  where sc.cate_id=1 and s.state=1 and s.recommended=1 limit 7");
							$zong=count($result);
							$sheng=7-$zong;
							
							$jiehe=array();
							if($zong<7)
							{
								$result1=$db->get_all("select s.store_name,s.owner_name,s.store_logo,s.store_id from {store} s left join {category_store} c on s.store_id=c.store_id left join {scategory} sc on sc.cate_id=c.cate_id  where sc.cate_id=1 and s.state=1 and s.recommended!=1 limit $sheng");
								$jiehe=array_merge($result,$result1); 
							}
							else
							{
								$jiehe=$result;
							}
							
							
							foreach($jiehe as $key=>$var)
							{
						?>
						
							<li>
								<a href="/index.php?app=store&id=<?php echo $var['store_id'] ?>" target="_blank">
								<div class="shopspan"><b><?php echo $var['store_name'] ?></b>店主：<?php echo $var['owner_name'] ?></div>
								<?php 
								if(empty($var['store_logo']))
								{
								?>
								<img data-url="img/pp.jpg" src="/themes/mall/default/styles/default/img/bbj.gif" class="scrollloading">
								<?php 
								}
								else{
								?>
								<img data-url="/<?php echo $var['store_logo'] ?>" src="/themes/mall/default/styles/default/img/bbj.gif" class="scrollloading"/>
								<?php 
								}
								?>
								</a>
								
							</li>
							<?php 
							}
							?>
							
							
						</ul>
					

					
					</div>
					
					
					
					
					<div class="starshopbox">
						
						<div class="ssbt">
							<div class="menutitle">今日推荐</div>
							<span class="morecss">
								<a href="/index.php?app=search&act=index&keyword=风衣" target="_blank">风衣</a> | 
								<a href="/index.php?app=search&act=index&keyword=羽绒服" target="_blank">羽绒服</a> | 
								<a href="/index.php?app=search&act=index&keyword=连衣裙" target="_blank">连衣裙</a> | 
								<a href="/index.php?app=search&act=index&keyword=牛仔裤" target="_blank">牛仔裤</a> | 
								<a href="/index.php?app=search&act=index&keyword=运动服" target="_blank">运动服</a> | 
								<a href="/index.php?app=search&act=index&keyword=西装" target="_blank">西装</a> | 
								<a href="/index.php?app=search&act=index&keyword=短袖" target="_blank">短袖</a> | 
								<a href="/index.php?app=search&act=tuijian&lei=1&cate_id=73,1935,1972,134" target="_blank">更多>></a>
							</span>
									
						</div>
						
						
						<ul class="cpbox">
						
						<?php 
						
						
						
						/*select goods_name,goods_id,default_image,jifen_price,vip_price from {goods} where (cate_id_2=1233 or cate_id_2=1234 or cate_id_2=1259) order by rand() limit 18*/
						$conditions = "g.if_show = 1 AND g.closed = 0 AND s.state = 1 ";
						$result=$db->get_all("SELECT  g.goods_id, g.goods_name, g.default_image, gs.price, gs.stock,gs.vip_price,gs.jifen_price,g.daishou,g.subhead,gss.sales " .
                    "FROM {recommended_goods} AS rg " .
                    "   LEFT JOIN {goods} AS g ON rg.goods_id = g.goods_id " .
                    "   LEFT JOIN {goods_spec} AS gs ON g.default_spec = gs.spec_id " .
                    "   LEFT JOIN {store} AS s ON g.store_id = s.store_id " .
					"   LEFT JOIN {goods_statistics} AS gss ON gss.goods_id = g.goods_id " .
                    "WHERE " . $conditions . 
                    "AND (g.cate_id_1 = 1935 or g.cate_id_1=73 or g.cate_id_2=6389 or g.cate_id_2=6390 or g.cate_id_1=1972 or g.cate_id_2=9107) " .
                    "AND g.goods_id IS NOT NULL " .
                    "ORDER BY gss.sales desc,rg.sort_order desc " .
                    "LIMIT 36");
					
					$cou=count($result);
					$yu=36+$cou;
					$uu=36-$cou;
					$arr=array();
					$hebing=array();
					if($cou<36)
					{
						foreach($result as $key=>$var)
						{
							array_push($arr,$var['goods_id']);
						}
					$result1=$db->get_all("SELECT g.goods_id, g.goods_name, g.default_image, gs.price, gs.stock,gs.vip_price,gs.jifen_price,g.daishou,g.subhead " .
                    "FROM {goods} AS g " .
                    "   LEFT JOIN {goods_spec} AS gs ON g.default_spec = gs.spec_id " .
                    "   LEFT JOIN {store} AS s ON g.store_id = s.store_id " .
					"   LEFT JOIN {goods_statistics} AS gss ON gss.goods_id = g.goods_id " .
                    "WHERE " . $conditions . 
                    "AND (g.cate_id_1 = 1935 or g.cate_id_1=73 or g.cate_id_2=6389 or g.cate_id_2=6390 or g.cate_id_1=1972 or g.cate_id_2=9107) " .
                    "AND g.goods_id IS NOT NULL " .
                    "ORDER BY gss.sales desc " .
                    "LIMIT $yu");
						$newarr=array();
						$i=0;
						foreach($result1 as $key=>$var)
						{
							if(!in_array($var['goods_id'],$arr) && $i<$uu)
							{
								$newarr[$key]=$result1[$key];
								$i++;
							}
						}
						
						$hebing=array_merge($result,$newarr); 
					}
					else
					{
						$hebing=$result;
					}
					
											
							foreach($hebing as $key=>$var)
							{
								if($var['default_image'])
								{
									if(strpos(strtolower($var['default_image']),'http://') ===false)
											$var['default_image']='/'.$var['default_image'];
								}
								
								
							?>
							<li>
								<a href="/index.php?app=goods&id=<?php echo $var['goods_id'] ?>" target="_blank">
								
								<div class="cpboxspan">
									<b><?php echo $var['goods_name'] ?></b>
									<label>积分价：<font style="font-weight:bold; font-size:13px;"><?php echo $var['jifen_price'] ?></font>  &nbsp;&nbsp;&nbsp;&nbsp;
									VIP积分价：<font style=" color:#FF6600; font-weight:bold; font-size:13px;"><?php echo $var['vip_price'] ?></font></label>
								</div>
								
								<img data-url="<?php echo $var['default_image'] ?>" src="/themes/mall/default/styles/default/img/bbj.gif" class="scrollloading"/>
								</a>
								
								<?php 
								}
								?>
							</li>
							
						
						
						</ul>
						
						
						
						
					</div>
						
							
	
					
					
						</div>
					
					</div>
					
					
					
				<div style="clear:both"></div>
				<div class="footbox">
					<div class="flink">
						<div class="flinkbox"> 
							<a href="/index.php">首页</a> | <a href="/index.php?act=groupbuy&app=search">团购</a> | <a href="/index.php?act=youhui&app=search">优惠券</a> | <a href="/index.php?app=my_theme&act=gonghuo">我要供货</a> | <a href="/index.php?act=gonghuo&app=search">我要采购</a>
							<span>Copyright 2012-2020 <?php echo $row['banquan'] ?> Inc.,All rights reserved. 
                  				<br /><?php echo $row['icp_num'] ?></span>
						 </div>
					
					
					</div>
				
				
				
				</div>
				
		
<script language="javascript">
$(function()
	{		
	$(".cpbox li").mouseover(function(){
		var parent=$(this).parents('a');
		//$('.bot',parent).slideDown(100);
		$('.cpboxspan',$(this)).show();
		//$('.cpboxspan',$(this)).show();
		//$('.piao',$(this).parents('.item')).show();
	})
	
	$(".cpbox li").mouseout(function(){		
		var parent=$(this).parents('a');
		//$('.bot',parent).slideUp(100);
		$('.cpboxspan',$(this)).hide();
		//$('.piao',$(this).parents('.item')).hide();
	})
})

$(function()
	{		
	$(".shopbox li").mouseover(function(){
		var parent=$(this).parents('a');
		//$('.bot',parent).slideDown(100);
		$('.shopspan',$(this)).show();
		//$('.cpboxspan',$(this)).show();
		//$('.piao',$(this).parents('.item')).show();
	})
	
	$(".shopbox li").mouseout(function(){		
		var parent=$(this).parents('a');
		//$('.bot',parent).slideUp(100);
		$('.shopspan',$(this)).hide();
		//$('.piao',$(this).parents('.item')).hide();
	})
})
$(function() {  
        $(".scrollloading").scrollLoading();  
    });

</script>			
  <!-- Baidu Button BEGIN -->
<script type="text/javascript" id="bdshare_js" data="type=slide&amp;img=7&amp;pos=right&amp;uid=0" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000);
</script>
<!-- Baidu Button END --> 		

</body>
</html>
