<?php
$modulename='商城日报表';	
$date=checkPost(strip_tags($_GET['date']));


	
	

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
    

	
	<?
	{		
		$sql="SELECT SUM( money ) money, LEFT( riqi, 7 ) riqi FROM {moneylog} WHERE `type` =100 GROUP BY LEFT( riqi, 7 ) ";
		$result=$db->get_all($sql);
		?>
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>

		<?
		echoTh(array('日期','充值金额(元)','到帐金额(元)','费用(元)'));
		$money_sum=0;
		$feiyong_sum=0;

		foreach ($result as $row)
		{
			$money_sum+=$row['money'];
			$feiyong_sum+=(float)$row['feiyong'];
			$recharge=$row['money']+$row['feiyong'];
			$recharge_sum+=$recharge;
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<?=$row['riqi']?></td>
                <td align='left'>&nbsp;&nbsp;<?=$recharge?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row['money']?></td>
                <td align='left'><?=$row['feiyong']?></td>

			</tr>
			<?
		}
		?>
        <tr <?=getChangeTr()?>>
            <td align='left'>&nbsp;&nbsp;总计</td>
            <td align='left'>&nbsp;&nbsp;<?=$recharge_sum?></td>
            <td align='left'>&nbsp;&nbsp;<?=$money_sum?></td>
            <td align='left'><?=$feiyong_sum?></td>

        </tr>
        </form></table>
        <br /><br />
        <h2>充值小于200</h2>
       <?
        $sql="SELECT SUM( money ) money, LEFT( riqi, 7 ) riqi FROM {moneylog} WHERE `type` =100 and money<200 GROUP BY LEFT( riqi, 7 ) ";
		$result=$db->get_all($sql);
		?>
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>

		<?
		echoTh(array('日期','充值金额(元)','到帐金额(元)','费用(元)'));
		$money_sum=0;
		$feiyong_sum=0;
$recharge_sum=0;
		foreach ($result as $row)
		{
			$money_sum+=$row['money'];
			$feiyong_sum+=(float)$row['feiyong'];
			$recharge=$row['money']+$row['feiyong'];
			$recharge_sum+=$recharge;
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<?=$row['riqi']?></td>
                <td align='left'>&nbsp;&nbsp;<?=$recharge?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row['money']?></td>
                <td align='left'><?=$row['feiyong']?></td>

			</tr>
			<?
		}
		?>
        <tr <?=getChangeTr()?>>
            <td align='left'>&nbsp;&nbsp;总计</td>
            <td align='left'>&nbsp;&nbsp;<?=$recharge_sum?></td>
            <td align='left'>&nbsp;&nbsp;<?=$money_sum?></td>
            <td align='left'><?=$feiyong_sum?></td>

        </tr>
        </form></table>
        <br /><br />
         <h2>充值大于等于200</h2>
       <? $sql="SELECT SUM( money ) money, LEFT( riqi, 7 ) riqi FROM {moneylog} WHERE `type` =100 and money>=200 GROUP BY LEFT( riqi, 7 ) ";
		$result=$db->get_all($sql);	
		?>	
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
        
		<?	
		echoTh(array('日期','充值金额(元)','到帐金额(元)','费用(元)'));	
		$money_sum=0;
		$feiyong_sum=0;
		$recharge_sum=0;
		foreach ($result as $row)
		{
			$money_sum+=$row['money'];
			$feiyong_sum+=(float)$row['feiyong'];
			$recharge=$row['money']+$row['feiyong'];
			$recharge_sum+=$recharge;
			?>
			<tr <?=getChangeTr()?>>
            	<td align='left'>&nbsp;&nbsp;<?=$row['riqi']?></td>
                <td align='left'>&nbsp;&nbsp;<?=$recharge?></td>
                <td align='left'>&nbsp;&nbsp;<?=$row['money']?></td>            	
                <td align='left'><?=$row['feiyong']?></td>  
               
			</tr>
			<?
		}
		?>
        <tr <?=getChangeTr()?>>
            <td align='left'>&nbsp;&nbsp;总计</td>
            <td align='left'>&nbsp;&nbsp;<?=$recharge_sum?></td>
            <td align='left'>&nbsp;&nbsp;<?=$money_sum?></td>            	
            <td align='left'><?=$feiyong_sum?></td>  
           
        </tr>
        </form></table>		
		<?php
	}

}

?>