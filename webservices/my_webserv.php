<?php
require('./include/my_webserv.class.php');
$tclass = new my_webserv();
require('./include/member.class.php');
$member = new member();
$modulename = '购买套餐审核';
$page = intval($_GET['page']);
$url = "?act=$act&page=$page";
$sqlW = '1=1';


        $taocan_arr = array(
            array('buytype' => 0, 'name' =>'超级合作商', 'price' => 12.98,'price_dj'=>6.4),
            array('buytype' => 1, 'name' =>'钻石合作商', 'price' => 6.4,'price_dj'=>3.2),
            array('buytype' => 2, 'name' =>'金牌合作商', 'price' => 3.2,'price_dj'=>0.72),
            array('buytype' => 3, 'name' =>'精英版店铺', 'price' => 1.6,'price_dj'=>0.51),
        );

if (isset($_REQUEST['func'])) {
    $func = $_REQUEST['func'];
    if ($func == 'del') {
        $tclass->delete(intval($_GET['id']));
    } elseif ($func == 'edit') {
        if ($func == 'edit') {
            if ($_S['kaiguan']['webservice'] != 'yes') {
                showMsg('WebServices接口开关没有开启！');
                exit();
            }
            $id = $_POST['id'];
            $user_id = $_POST['user_id'];


            $member = $db->get_one("select a.web_id,a.yaoqing_id,a.user_name,a.city,b.buytype,b.paymoney,b.status from {member} a join {my_webserv} b on a.user_id=b.user_id where a.user_id=$user_id limit 1");

            if (!$member) {
                showMsg($user_id . '用户不存在！');
                exit();
            } else {
                $web_id = $member['web_id'];
                $user_name = $member['user_name'];
                $city = $member['city'];
                $paymoney= $member['paymoney'];
                $yaoqing_id=$member['yaoqing_id'];
            }
            unset($member);

            if($member['status']!=0){
                showMsg('不要重复审核！');
                exit();
            }

            $level = $_POST['level'];
            if ($level) {
                $level = implode(',', $level);
                $db->query("update {member} set level='$level' where user_id=$user_id limit 1");
            }

            //邀请人获得奖励
            if (!empty($yaoqing_id)) {
                $yaoqing = $db->get_one("select user_id,user_name from {member} where user_name='" . $yaoqing_id . "' limit 1 ");

                $yaoqing_user_id=$yaoqing['user_id'];
                $yaoqing_user_name=$yaoqing['user_name'];
                $row = $db->get_one("select money_dj,money,suoding_money,duihuanjifen,dongjiejifen from {my_money} where user_id='{$yaoqing_user_id}' limit 1");
                $user_money = $row['money'];
                $user_money_dj = $row['money_dj'];
                $user_jifen = $row['duihuanjifen'];
                $user_jifen_dj = $row['dongjiejifen'];
                unset($row);

               $yaoqing_money=$paymoney*0.5;
                $db->query("update {my_money} set money=money+" . $yaoqing_money . "  where user_id='$yaoqing_user_id' limit 1");
                //用户金钱流水
                $arr = array(
                    'money' => $yaoqing_money,
                    'jifen' => 0,
                    'money_dj' =>0,
                    'jifen_dj' => 0,
                    'user_id' => $yaoqing_user_id,
                    'user_name' => $yaoqing_user_name,
                    'type' => 38,
                    's_and_z' => 1,
                    'time' => date('Y-m-d H:i:s'),
                    'zcity' => $city,
                    'dq_money' => $user_money + $yaoqing_money,
                    'dq_money_dj' => $dq_money_dj,
                    'dq_jifen' => $user_jifen,
                    'dq_jifen_dj' => $user_jifen_dj,
                    'beizhu' => $yaoqing['user_name'].'购买套餐，获得奖励'
                );
                $db->insert('{moneylog}', $arr);
                $yaoqing = null;
            }
            $row = $db->get_one("select money_dj,money,suoding_money,duihuanjifen,dongjiejifen from {my_money} where user_id='$user_id' limit 1");
            $user_money = $row['money'];
            $user_money_dj = $row['money_dj'];
            $user_jifen = $row['duihuanjifen'];
            $user_jifen_dj = $row['dongjiejifen'];
            unset($row);

            //更改帐户余额
            $db->query("update {my_money} set money_dj=money_dj-" . $paymoney . "  where user_id='$user_id' limit 1");
            //用户金钱流水
            $dq_money_dj = $user_money_dj - $paymoney;
            $arr = array(
                'money' => 0,
                'jifen' => 0,
                'money_dj' =>'-' . $paymoney,
                'jifen_dj' => 0,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'type' => 36,
                's_and_z' => 2,
                'time' => date('Y-m-d H:i:s'),
                'zcity' => $city,
                'dq_money' => $user_money,
                'dq_money_dj' => $dq_money_dj,
                'dq_jifen' => $user_jifen,
                'dq_jifen_dj' => $user_jifen_dj,
                'beizhu' => '购买套餐'
            );
            $db->insert('{moneylog}', $arr);


            $returnjifen = getjifen($money);
            $consume_id = webService('C_Consume', array("ID" => $web_id, "Money" => $returnjifen, "MoneyType" => 1, "Count" => 1));//返利全部
            $arr = array(
                'cai_id' => 0,
                'gong_id' => $user_id,
                'consume_id' => $consume_id,
                'type' => 1,
                'time' => date('Y-m-d H:i:s'),
                'status' => 0,
                'money' => $returnjifen,
                'jifen' => 0,
                'city' => $city,
                'gh_id' => 0
            );
            $db->insert('{webservice_list}', $arr);


            $remark = $a_username . '|' . $a_userid . "审核会员" . $user_name . "购买套餐";

                $arr = array(
                    'checktime' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'remark' => $remark
                );
                $db->update('{my_webserv}', $arr, "id=$id");



            adminlog("审核会员{$user_name}[{$user_id}]购买套餐！");

            $add_notice1 = array(
                'from_id' => 0,
                'to_id' => $user_id,
                'content' => "亲爱的用户$user_name：您好，我们很高兴的通知您，您已成功购买套餐！",
                'add_time' => time(),
                'last_update' => time(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3
            );
            $db->insert('{message}', $add_notice1);

            showMsg('操作成功！', $url);
            exit();
        }
    }
    header("location:$url");
    exit();
}

if ($_GET['status'] != '') {
    $status = (int)($_GET['status']);
    $sqlW .= " and status='$status'";
    $url .= '&status=' . $status;
}

if (!empty($_GET['user_name'])) {
    $user_name = checkPost(strip_tags($_GET['user_name']));
    $sqlW .= " and user_name='$user_name'";
    $url .= '&user_name=' . $user_name;
}
if (!empty($_GET['user_id'])) {
    $user_id = (int)($_GET['user_id']);
    $sqlW .= " and user_id='$user_id'";
    $url .= '&user_id=' . $user_id;
}
pageTop($modulename);
//webService('Z_Static_Regist',array("ID"=>'753bbdea-de35-4ec6-b8e5-87d15a50076b',"Money"=>20000,'Month'=>9));

$city_result = $db->get_all("select city_id,city_name from ecm_city order by city_id");
foreach ($city_result as $row) {
    $city[$row['city_id']] = substr($row['city_name'], 0, 4);
}
?>
<script language="javascript" src="include/js/jquery.js"></script>
<script language="javascript">
    function checkform() {
        if (document.forms[0].tuijianid.value == 0 || (document.forms[0].lishuid.value == 0 && document.forms[0].buytype[6].checked == false)) {
            if (window.confirm('推荐人或隶属人为空！确定要提交吗？')) {
                document.getElementById('btn_sub').disabled = true;
                return true;
            }
            else {
                return false;
            }
        }
        document.getElementById('btn_sub').disabled = true;
        return true;
    }
</script>
<?
if(empty($_GET['ui']))
{

?>
<div class="div_title"><?=getHeadTitle(array($modulename=>''))?>&nbsp;&nbsp;</div>
	<div style="margin-bottom:5px;">
	<form method="GET">
        用户ID<input type="text" name="user_id" size="8" value="<?=$user_id?>">&nbsp;
        用户名<input type="text" name="user_name" size="8" value="<?=$user_name?>">&nbsp;
        <select name="status">
        	<option value="" selected="selected">未/己审</option>
            <option value="0" <? if($_GET['status']==='0'){echo "selected";}?>>未审核</option>
            <option value="1" <? if($status==1){echo "selected";}?>>己审核</option>
        </select>



		<input type="submit" value="筛选条件">
		<input type="hidden" name="act" value="<?=$act?>">
	</form></div>
	<?
	$PageSize = 15;  //每页显示记录数
	$RecordCount = $tclass->getcount($sqlW);//获取总记录数
	if(!empty($page))
	{
		$StartRow=($page-1)*$PageSize;
	}
	else
	{
		$StartRow=0;
		$page=1;
	}
	if($RecordCount>0)
	{

		$result=$tclass->getall($StartRow,$PageSize,'id desc',$sqlW);
		?>
		<table cellpadding="4" cellspacing="1" width="100%" bgcolor="#CCCCCC">
		<form method="GET" id='form1' name='form1'>
		<input type="hidden" name="func">
        <input type="hidden" name="act" value='<?=$act?>'>
		<?
		echoTh(array('会员id','会员名','套餐','缴费','申请时间','审核时间','所属站','状态','操作'));
		$is_array=array('不','是');

		foreach ($result as $row)
		{
			$id=$row['id'];
			?>
			<tr <?=getChangeTr()?>>
            	<td align='center'><?=getuserno($row['user_id'])?></td>
            	<td align='center'><?=$row['user_name']?></td>
				<td align='left'>&nbsp;&nbsp;<?=$taocan_arr[$row['buytype']]['name']?>（<?=$taocan_arr[$row['buytype']]['price']?>万）</td>
				<td align="center"><?=$row['paymoney']/10000?>万</td>
                <td align="center"><?=$row['createdate']?></td>
                <td align="center"><?=$row['checktime']?></td>
				<td align="center">&nbsp;&nbsp;<?=$city[$row['city']];?></td>
				<td align="center"><?
                	if($row['status']==0)
					{
						echo '未审核';
					}
					else
					{
						echo '己审核';
					}
				?></td>
				<td align="center">
                    <?
                    if($row['status']==0)
					{
					?>
                    <a href='<?=$url?>&ui=edit&id=<?=$row['id']?>'>审核</a>
                    <?
					}
					?>
                    <a onclick="return confirm('确定要删除吗？')" href="<?=$url?>&func=del&id=<?=$row["id"]?>"></a>
                 </td>
			</tr>
			<?
		}
		?>
        </form></table>
		<div class="line"><?=page($RecordCount,$PageSize,$page,$url)?></div>
		<?php
	}
	else
	{
		echo "<div><br>&nbsp;&nbsp;无数据！</div>";
	}
}
else
{
	$id=intval($_GET['id']);
	echo '<form method="POST"  enctype="multipart/form-data" id="form1" onsubmit="return checkform();">';
	echo '<input type="hidden" name="url" value="'.$url.'">';
	if(empty($id))
	{
		$arr=array($modulename=>$url,'添加'.$modulename=>'');
		echo '<input type="hidden" name="func" value="add">';
	}
	else
	{
		$arr=array($modulename.'管理'=>$url,'编辑'.$modulename=>'');
		echo '<input type="hidden" name="func" value="edit">';
		echo "<input type='hidden' name='id' value='$id'>";

		$tclass->id=$id;
		$row=$tclass->getone();
		$user_id=$row['user_id'];
		$mem=$db->get_one("select a.money_dj,a.money,a.suoding_money,b.tuijianid,b.lishuid,b.yaoqing_id from {my_money} a join {member} b on a.user_id=b.user_id  where a.user_id='$user_id' limit 1");
		$user_money=$mem['money'];
		$user_money_dj=$mem['money_dj'];
		$user_suoding_money=$mem['suoding_money'];
		$tuijianid=(int)$mem['tuijianid'];
		$lishuid=(int)$mem['lishuid'];
		$yaoqing_id=$mem['yaoqing_id'];
		$mem=null;

		echo "<input type='hidden' name='user_id' value='$user_id'>";

	}
		?>
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="<?=$url?>">返回管理</a></div>




    <table class="infoTable" cellpadding="5">
      <tbody><tr>
        <th class="paddingT15"> 会员名:</th>
        <td class="paddingT15 wordSpacing5"><?=$row['user_name']?>  资金：<?=$user_money?>  冻结资金：<?=$user_money_dj?>  锁定资金：<?=$user_suoding_money?>

                  </td>
      </tr>


	  <tr>
        <th class="paddingT15"> 所属站:</th>
        <td class="paddingT15 wordSpacing5">
        <?
        foreach($city_result as $c)
		{
			$sel='';
			if($c['city_id']==$row['city']) echo $c['city_name'];

		}
		$city_result=null;
		?>

          </td>
      </tr>

      <tr>
        <th class="paddingT15"> 邀请人:</th>
        <td class="paddingT15 wordSpacing5">
        <?
       if($yaoqing_id){
            $yaoqing = $db->get_one("select user_id,user_name from {member} where user_name='" . $yaoqing_id . "' limit 1 ");
            echo getuserno($yaoqing['user_id']);
            echo '____'.$yaoqing['user_name'];
       }
		?>
          </td>
      </tr>

      <tr>
        <th class="paddingT15"> 套餐名称:</th>
        <td class="paddingT15 wordSpacing5">
        <?=$taocan_arr[$row['buytype']]['name']?>（<?=$taocan_arr[$row['buytype']]['price']?>万）
        </td>
      </tr>
      <tr><th>优惠价：</th><td>己冻结：<?=$row['paymoney']/10000?>万</td></tr>
      <tr><th>商城：</th><td>

      审核为付费商家<input type="checkbox" value="1" name="level[]" checked="checked"/>
      审核为代理商<input type="checkbox" value="2" name="level[]"/>

      </td></tr>


<tr>
        <th></th>

        <td class="ptb20"><input type="submit" value="提交" id="btn_sub"> </td>
      </tr>
   </table>

		</form>
		<?
}
?>