<?php
if (!defined('BASEPATH'))    exit('No direct script access allowed');

class webserv extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->db1 = $this->load->database('fenecll', TRUE);
        $this->db2 = $this->load->database('kuaicai', TRUE);
        //$this->db3 = $this->load->database('mssql', TRUE);
        $this->load->model('member_model');
    }

    public function index()
    {
        
    }

    public function mallrebates()
    {
		global $_S;
		
		$page = empty($_POST['page']) ? 1 : $_POST['page'];
        $rp = empty($_POST['rp']) ? 10 : $_POST['rp'];
        $sortname = empty($_POST['sortname']) ? 'ConsumptionTime' : $_POST['sortname'];
        $sortorder = empty($_POST['sortorder']) ? 'desc' : $_POST['sortorder'];
		
		
		$data=array(
			'page'=>$page,
			'rp'=>$rp,
			'sortname'=>$sortname,
			'sortorder'=>$sortorder
		);
		$data=array_merge($data,$this->get_param_mallrebates());
		
		
		$url='http://192.168.1.11/ci_mssql/index.php/webserv/mallrebates/';
		$str=sock_open($url,$data);
		$str=explode('[#]',$str);
		
		$jsonData=unserialize($str[1]);
		
		//echo $str[2];

		$list=$jsonData['result'];
		
		if(!empty($list))
		{
		$list = $this->member_model->replacelist_fenecll($list, 1000);
		$list = $this->member_model->replacelist_kuaicai($list, 1000);
		$list = $this->member_model->replacelist_p2p($list, 1000);
		
		

        $arr_type = array('12%', '16%', '双队列');
        foreach ($list as $i=>$row)
        {
            if ($row['MoneyType'] == 0)
            {
                $inmoney = $row['Money'] * 0.15;
            } elseif ($row['MoneyType'] == 1)
            {
                $inmoney = $row['Money'] * 0.16;
            } elseif ($row['MoneyType'] == 2)
            {
                $inmoney = $row['Money'] * 0.31;
            }
			$Aside4=$row['Aside4'];
			if($row['RebatesStatus']==0) $Aside4='';
			
			$jsonData['result'][$i]['inMoney']=$inmoney;
			$jsonData['result'][$i]['MoneyType']=$arr_type[$row['MoneyType']];
			$jsonData['result'][$i]['sitename']=$_S['site'][$row['siteid']];
			if($row['RebatesStatus']==1)
			{
				$jsonData['result'][$i]['RebatesStatus']='己结束';	
			}
			else
			{
				$jsonData['result'][$i]['RebatesStatus']='正常';
				$jsonData['result'][$i]['Aside4']='';
			}
						
			$jsonData['result'][$i]['user_id']=$row['user_id'];
			$jsonData['result'][$i]['user_name']=$row['user_name'];			
            /*$entry = array('id' => $row['MallRebatesID'],
                'cell' => array(
                    'user_id' => $row['user_id'],
                    'user_name' => $row['user_name'],
                    'MoneyType' => $arr_type[$row['MoneyType']],
                    'inMoney' => $inmoney,
                    'sitename' => $_S['site'][$row['siteid']],
                    'Money' => $row['Money'],
                    'RebatesMoney' => $row['RebatesMoney'],
                    'MallRebatesID' => $row['MallRebatesID'],
                    'ConsumptionTime' => $row['ConsumptionTime'],
                    'RebatesStatus' => $arr_status[$row['RebatesStatus']],
                    'UserID' => addslashes($row['UserID']),
					'Aside4'=>$Aside4,
					'Aside5'=>$row['Aside5']
                ),
            );
            $jsonData['rows'][] = $entry;*/
        }
		}
		echo '[#]';
        echo json_encode($jsonData);
		echo '[#]';
		$jsonData=unserialize($str[2]);
		$Moneys=0;
		$inMoneys=0;
		$RebatesMoneys=0;
		$Aside5=0;
		if(empty($jsonData)){$jsonData=array();}
		foreach($jsonData as $i=>$row)
		{
			if ($row['MoneyType'] == 0)
            {
                $inmoney = $row['Money'] * 0.15;
            } elseif ($row['MoneyType'] == 1)
            {
                $inmoney = $row['Money'] * 0.16;
            } elseif ($row['MoneyType'] == 2)
            {
                $inmoney = $row['Money'] * 0.31;
            }
			$jsonData[$i]['inMoney']=$inmoney;
			$jsonData[$i]['MoneyType']=$arr_type[$row['MoneyType']];
			
			$Moneys+=$row['Money'];
			$inMoneys+=$inmoney;
			$RebatesMoneys+=$row['RebatesMoney'];
			$Aside5+=$row['Aside5'];
		}
		$jsonData[$i+1]['MoneyType']='合计';
		$jsonData[$i+1]['Money']=$Moneys;
		$jsonData[$i+1]['inMoney']=$inMoneys;
		$jsonData[$i+1]['RebatesMoney']=$RebatesMoneys;
		$jsonData[$i+1]['Aside5']=$Aside5;
		echo json_encode($jsonData);
    }	
    function get_param_mallrebates()
    {
		$data=array();
        parse_str($_SERVER['QUERY_STRING'], $_GET);
        $_GET = array_map('urldecode', $_GET);
		
		//$_GET=$this->input->get();//汉字可能有问题
		//print_r($_GET);
        $user_name = iconv('gbk', 'utf-8', $_GET['user_name']);
        $user_id=(int)$_GET['user_id'];
        $data['starttime']=$_GET['starttime'];
        $data['endtime']=$_GET['endtime'];
        $data['web_id']=trim($_GET['web_id']);

        if(!empty($user_name))
        {
            $d['user_name']=$user_name;
        }
        if(!empty($user_id))
        {
            $d['user_id']=$user_id;
        }
        if(!empty($d))
        {
            $arr_webs=$this->member_model->get_webids($d);
            if(!empty($arr_webs))
            {
                $data['webids']="'".  implode("','", $arr_webs)."'";
            }
        }
        return $data;
    }
}