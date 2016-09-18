<?php
$modulename='商铺交易量报表';	
$startdate=checkPost(strip_tags($_GET['startdate']));
if(empty($startdate)) 	$startdate=date('Y-m-d');

$starttime=strtotime($startdate);
$sqlW="add_time>='".($starttime)."'";

if(!empty($_GET['enddate']))
{
	$enddate=checkPost(strip_tags($_GET['enddate']));
	$endtime=strtotime($enddate);
	$sqlW.=" and add_time <'$endtime' ";
}


if(!empty($_GET['user_name']))
{
	$user_name=checkPost(strip_tags($_GET['user_name']));
	$sqlW.=" and seller_name ='$user_name' ";
	$url.='&user_name='.$user_name;
}
if(!empty($_GET['user_id']))
{
	$user_id=intval($_GET['user_id']);
	$sqlW.=" and seller_id='$user_id'";
	$url.='&user_id='.$user_id;
}
if($_GET['status']!='')
{
	$status=intval($_GET['status']);
	$sqlW.=" and status=$status";
	$url.='&status='.$status;
}
if(!empty($_GET['c']))
{
	$c=checkPost(strip_tags($_GET['c']));
	$sqlW.=" and city='$c'";
	$url.='&c='.$c;
}

	
	

pageTop($modulename.'管理');

$city_result=$db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach($city_result as $row)
{
	$city[$row['city_id']]=substr($row['city_name'],0,4);	
}

if(empty($_GET['ui']))
{
?>
	<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>	
    <script language="javascript" charset="utf-8" src="include/js/My97DatePicker/WdatePicker.js"></script>

	<div style="margin-bottom:5px;">
	<form method="GET">   
    
        会员ID:<input type="text" name="user_id" value="<? if(!empty($user_id)){echo getuserno($user_id);}?>" size="8"/>	
    	店铺名称：<input type="text" name="user_name" value="<?=$user_name?>" size="15"/>
        
        
        <select name="status">
                    <option value="">订单状态</option>
                    <option value="11" <? if($status==11){echo 'selected';}?>>待付款</option>
                   
                    <option value="20" <? if($status==20){echo 'selected';}?>>待发货</option>
                    <option value="30" <? if($status==30){echo 'selected';}?>>已发货</option>
                    <option value="40" <? if($status==40){echo 'selected';}?>>已完成</option>
                    <option value="0" <? if($status=='0'){echo 'selected';}?>>已取消</option>                </select>
        
        <select name="c">
        <option value="">选择分站</option>
        <?
        foreach($city as $i=>$k)
		{
			$ch=($c==$i)?'selected':'';
			echo "<option value='$i' $ch>$k</option>";
		}
		?>        
        </select>

    	下单时间：<input name="startdate" type="text"  class="Wdate" onclick="javascript:WdatePicker();" value="<?=$startdate?>">
		<input name="enddate" type="text"  class="Wdate" onclick="javascript:WdatePicker();" value="<?=$enddate?>">
		
		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
     </form>


    </div>
	<?
	{		
		//$sql="select a.seller_id,a.seller_name,a.status,sum(order_jifen) ojifen,sum(fanhuan_jia) fanhuan ,b.shipping_fee,b.fee from {order} a left join {order_extm} b on a.order_id=b.order_id where $sqlW group by a.seller_id";
		$sql="select seller_id,seller_name,status,count(seller_id) as num,city,sum(order_jifen) ojifen,sum(fanhuan_jia) fanhuan  from {order} where $sqlW group by seller_id order by ojifen desc";
		//echo $sql;
		$result=$db->get_all($sql);	
		//print_r($result);
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?	
		echoTh(array('排序','卖家ID','店铺名称','订单数','订单积分','收入积分','分站'));	
		$money_sum=0;
		$money_dj_sum=0;
		$jifen_sum=0;
		$jifen_dj_sum=0;
		foreach ($result as $i=>$row)
		{			
			$user_id=$row['seller_id'];
			$user_name=$row['seller_name'];
			
			$ojifen_sum+=$row['ojifen'];
			$fanhuan_sum+=$row['fanhuan'];

			?>
			<tr <?=getChangeTr()?>>
            	<td align="center"><?=$i+1?></td>
            	<td align='left'>&nbsp;&nbsp;<? if(!empty($user_id)){echo getuserno($user_id);}?></td>
                <td align='left'>&nbsp;&nbsp;<?=$user_name?></td>
                <td><?=$row['num']?></td>        	
                <td align='left'><?=$row['ojifen']?></td>  
                <td align='left'><?=$row['fanhuan']?></td>  
      
              
                <td align="center"><?=$city[$row['city']];?></td>
			</tr>
			<?
		}
		?>
        <tr <?=getChangeTr()?>>
            	<td align='left' colspan="2"></td>   
                <td colspan="2"></td>         	
                <td align='left'><?=$ojifen_sum?>（<?=$ojifen_sum/2.52?>元）</td>  
                 
                <td align='left'><?=$fanhuan_sum?>（<?=$fanhuan_sum/2.52?>元）</td> 
                                                  
                <td align='center' colspan="2"></td>
			</tr>
        </form></table>
       	
		
		<?php
	}

}

?>