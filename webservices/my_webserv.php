<?php
require('./include/my_webserv.class.php');
$tclass = new my_webserv();
require('./include/member.class.php');
$member = new member();
$modulename = '�����ײ����';
$page = intval($_GET['page']);
$url = "?act=$act&page=$page";
$sqlW = '1=1';


        $taocan_arr = array(
            array('buytype' => 0, 'name' =>'����������', 'price' => 12.98,'price_dj'=>6.4),
            array('buytype' => 1, 'name' =>'��ʯ������', 'price' => 6.4,'price_dj'=>3.2),
            array('buytype' => 2, 'name' =>'���ƺ�����', 'price' => 3.2,'price_dj'=>0.72),
            array('buytype' => 3, 'name' =>'��Ӣ�����', 'price' => 1.6,'price_dj'=>0.51),
        );

if (isset($_REQUEST['func'])) {
    $func = $_REQUEST['func'];
    if ($func == 'del') {
        $tclass->delete(intval($_GET['id']));
    } elseif ($func == 'edit') {
        if ($func == 'edit') {
            if ($_S['kaiguan']['webservice'] != 'yes') {
                showMsg('WebServices�ӿڿ���û�п�����');
                exit();
            }
            $id = $_POST['id'];
            $user_id = $_POST['user_id'];


            $member = $db->get_one("select a.web_id,a.yaoqing_id,a.user_name,a.city,b.buytype,b.paymoney,b.status from {member} a join {my_webserv} b on a.user_id=b.user_id where a.user_id=$user_id limit 1");

            if (!$member) {
                showMsg($user_id . '�û������ڣ�');
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
                showMsg('��Ҫ�ظ���ˣ�');
                exit();
            }

            $level = $_POST['level'];
            if ($level) {
                $level = implode(',', $level);
                $db->query("update {member} set level='$level' where user_id=$user_id limit 1");
            }

            //�����˻�ý���
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
                //�û���Ǯ��ˮ
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
                    'beizhu' => $yaoqing['user_name'].'�����ײͣ���ý���'
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

            //�����ʻ����
            $db->query("update {my_money} set money_dj=money_dj-" . $paymoney . "  where user_id='$user_id' limit 1");
            //�û���Ǯ��ˮ
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
                'beizhu' => '�����ײ�'
            );
            $db->insert('{moneylog}', $arr);


            $returnjifen = getjifen($money);
            $consume_id = webService('C_Consume', array("ID" => $web_id, "Money" => $returnjifen, "MoneyType" => 1, "Count" => 1));//����ȫ��
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


            $remark = $a_username . '|' . $a_userid . "��˻�Ա" . $user_name . "�����ײ�";

                $arr = array(
                    'checktime' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'remark' => $remark
                );
                $db->update('{my_webserv}', $arr, "id=$id");



            adminlog("��˻�Ա{$user_name}[{$user_id}]�����ײͣ�");

            $add_notice1 = array(
                'from_id' => 0,
                'to_id' => $user_id,
                'content' => "�װ����û�$user_name�����ã����Ǻܸ��˵�֪ͨ�������ѳɹ������ײͣ�",
                'add_time' => time(),
                'last_update' => time(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3
            );
            $db->insert('{message}', $add_notice1);

            showMsg('�����ɹ���', $url);
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
            if (window.confirm('�Ƽ��˻�������Ϊ�գ�ȷ��Ҫ�ύ��')) {
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
        �û�ID<input type="text" name="user_id" size="8" value="<?=$user_id?>">&nbsp;
        �û���<input type="text" name="user_name" size="8" value="<?=$user_name?>">&nbsp;
        <select name="status">
        	<option value="" selected="selected">δ/����</option>
            <option value="0" <? if($_GET['status']==='0'){echo "selected";}?>>δ���</option>
            <option value="1" <? if($status==1){echo "selected";}?>>�����</option>
        </select>



		<input type="submit" value="ɸѡ����">
		<input type="hidden" name="act" value="<?=$act?>">
	</form></div>
	<?
	$PageSize = 15;  //ÿҳ��ʾ��¼��
	$RecordCount = $tclass->getcount($sqlW);//��ȡ�ܼ�¼��
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
		echoTh(array('��Աid','��Ա��','�ײ�','�ɷ�','����ʱ��','���ʱ��','����վ','״̬','����'));
		$is_array=array('��','��');

		foreach ($result as $row)
		{
			$id=$row['id'];
			?>
			<tr <?=getChangeTr()?>>
            	<td align='center'><?=getuserno($row['user_id'])?></td>
            	<td align='center'><?=$row['user_name']?></td>
				<td align='left'>&nbsp;&nbsp;<?=$taocan_arr[$row['buytype']]['name']?>��<?=$taocan_arr[$row['buytype']]['price']?>��</td>
				<td align="center"><?=$row['paymoney']/10000?>��</td>
                <td align="center"><?=$row['createdate']?></td>
                <td align="center"><?=$row['checktime']?></td>
				<td align="center">&nbsp;&nbsp;<?=$city[$row['city']];?></td>
				<td align="center"><?
                	if($row['status']==0)
					{
						echo 'δ���';
					}
					else
					{
						echo '�����';
					}
				?></td>
				<td align="center">
                    <?
                    if($row['status']==0)
					{
					?>
                    <a href='<?=$url?>&ui=edit&id=<?=$row['id']?>'>���</a>
                    <?
					}
					?>
                    <a onclick="return confirm('ȷ��Ҫɾ����')" href="<?=$url?>&func=del&id=<?=$row["id"]?>"></a>
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
		echo "<div><br>&nbsp;&nbsp;�����ݣ�</div>";
	}
}
else
{
	$id=intval($_GET['id']);
	echo '<form method="POST"  enctype="multipart/form-data" id="form1" onsubmit="return checkform();">';
	echo '<input type="hidden" name="url" value="'.$url.'">';
	if(empty($id))
	{
		$arr=array($modulename=>$url,'���'.$modulename=>'');
		echo '<input type="hidden" name="func" value="add">';
	}
	else
	{
		$arr=array($modulename.'����'=>$url,'�༭'.$modulename=>'');
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
		<div class="div_title"><?=getHeadTitle($arr)?>&nbsp;&nbsp;<a href="<?=$url?>">���ع���</a></div>




    <table class="infoTable" cellpadding="5">
      <tbody><tr>
        <th class="paddingT15"> ��Ա��:</th>
        <td class="paddingT15 wordSpacing5"><?=$row['user_name']?>  �ʽ�<?=$user_money?>  �����ʽ�<?=$user_money_dj?>  �����ʽ�<?=$user_suoding_money?>

                  </td>
      </tr>


	  <tr>
        <th class="paddingT15"> ����վ:</th>
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
        <th class="paddingT15"> ������:</th>
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
        <th class="paddingT15"> �ײ�����:</th>
        <td class="paddingT15 wordSpacing5">
        <?=$taocan_arr[$row['buytype']]['name']?>��<?=$taocan_arr[$row['buytype']]['price']?>��
        </td>
      </tr>
      <tr><th>�Żݼۣ�</th><td>�����᣺<?=$row['paymoney']/10000?>��</td></tr>
      <tr><th>�̳ǣ�</th><td>

      ���Ϊ�����̼�<input type="checkbox" value="1" name="level[]" checked="checked"/>
      ���Ϊ������<input type="checkbox" value="2" name="level[]"/>

      </td></tr>


<tr>
        <th></th>

        <td class="ptb20"><input type="submit" value="�ύ" id="btn_sub"> </td>
      </tr>
   </table>

		</form>
		<?
}
?>