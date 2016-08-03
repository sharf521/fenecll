<?php
if (!defined('BASEPATH'))    exit('No direct script access allowed');

class webserv extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->db3 = $this->load->database('mssql', TRUE);
        
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

        $fields = " MallRebatesID,UserID,Money,MoneyType,RebatesMoney,RebatesStatus,Aside4,Aside5";
        $where='1=1';
        $where.=$this->get_where_mallrebates($_POST);
		
		
        if($where=='1=1')
        {
            $sql = "SELECT TOP $rp $fields,convert(char,ConsumptionTime,120) as ConsumptionTime FROM MallRebates WHERE MallRebatesID NOT IN (SELECT TOP " . $rp * ($page - 1) . "MallRebatesID FROM MallRebates ORDER BY $sortname $sortorder)ORDER BY $sortname $sortorder";
            $result = $this->db3->query($sql);
          /*  $query = $this->db3->query("select count(*) as total from MallRebates");
            $row = $query->row_array();
            $total = $row['total'];*/        
            $total=$this->db3->count_all('MallRebates');
        }
        else 
        {
              $rp=1000;
              $sql="select top $rp $fields,convert(char,ConsumptionTime,120) as ConsumptionTime FROM MallRebates WHERE $where ORDER BY $sortname $sortorder";
              $result = $this->db3->query($sql);
              $total=$result->num_rows();              
        }
		$result = $result->result_array();
		
        $jsonData = array('total' => $total,'page'=>$page, 'result' => $result);

        //print_r($jsonData);
		echo '[#]';
        echo serialize($jsonData);
		echo '[#]';
		$sql="select sum(Money) as Money,MoneyType,sum(RebatesMoney) as RebatesMoney,sum(convert(float,Aside5)) Aside5 FROM MallRebates WHERE $where group by MoneyType";
        $result = $this->db3->query($sql);
		$result = $result->result_array();
		echo serialize($result);
    }
    function get_where_mallrebates($_GET)
    {
        $starttime=$_GET['starttime'];
        $endtime=$_GET['endtime'];    
        $web_id=trim($_GET['web_id']);
        if(!empty($starttime))
        {
            $where.=" and ConsumptionTime>'$starttime'";
        }        
        if(!empty($endtime))
        {
            $where.=" and ConsumptionTime<'$endtime'";
        }
        if(!empty($web_id))
        {
            $where.=" and UserID='$web_id'";
        }
        if(!empty($_GET['webids']))
        {
            $where.=" and UserID in(".$_GET['webids'].")";
        }
        return $where;
    }
}