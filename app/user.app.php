<?php

class UserApp extends MallbaseApp
{
    var $p2p_url = '';
    var $mall_url = '';

    function __construct()
    {
        $this->UserApp();
    }

    function UserApp()
    {
        $this->_city_mod =& m('city');
        $cityrow = $this->_city_mod->get_cityrow();
        $this->p2p_url = $cityrow['p2p_url'];
        $this->mall_url = $cityrow['mall_url'];
        parent::__construct();
    }

    function index()
    {
        //loaner8
        //$user_id = DeCode(3285,'E');
        $user_id = DeCode(3285, 'E');
        $user_id = 'wvmEtJgwZwo8E6Io';
        //echo $user_id;
        $data = array('user_id' => $user_id);
        //$this->i_accountl2m(3285,100);
        //$this->i_accountm2l(3285,1);
        //查询用户在借贷平台的帐户情况
        //http://zhuzhan.cn/index.php?app=user&act=get_p2p_account
        //l2m
        //http://zhuzhan.cn/index.php?app=user&act=i_accountl2m&user_id=wvmEtJgwZwo8E6Io&money=10

        //$this->i_awardl2m(3285,1);

        //echo $this->i_accountm2l(350,100);

        //echo getHTML('http://zhuzhan.cn/index.php?app=user&act=account',$data);
    }

    function account()
    {
        $user_id = (int)$_REQUEST['user_id'];

        $member_mod = &m('my_money');
        $result = $member_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");

        $row = array();
        $row['user_id'] = $user_id;
        if (empty($result)) {
            $row['money'] = 0;
            $row['money_dj'] = 0;
            $row['jifen'] = 0;
            $row['jifen_dj'] = 0;
        } else {
            $row['money'] = (float)$result['money'];
            $row['money_dj'] = (float)$result['money_dj'];
            $row['jifen'] = (float)$result['duihuanjifen'];
            $row['jifen_dj'] = (float)$result['dongjiejifen'];
        }
        $row = json_encode($row);
        $callback = $_REQUEST['jsoncallback'];
        if (empty($callback)) {
            echo urldecode($row);
        } else {
            echo $callback . '(' . urldecode($row) . ')';
        }
        exit();
    }

    /*
查询用户在借贷平台的帐户情况：
请求链接：http://hndai.p2p.com/index.php?user&q=code/account/i_user_info
传入数据：
user_id:注册时通过web_service生成的用户id
target:操作完成后转向的链接

返回数据：
result：为1时表示操作成功，其它值都表示出现错误
user_id
account_total：帐户余额
account_cash：可提现（可转入商城）金额
award：充值奖励已赚利息（可单向转入商城）
*/
    function get_p2p_account()
    {
        $user_id = $this->visitor->get('user_id');
        $user_id = DeCode($user_id, 'E');//加密
        $data = array('user_id' => $user_id);
        $res = getHTML('http://' . $this->p2p_url . '/index.php?user&q=code/account/i_user_info', $data);
        //print_r($res);
        /*$res=json_decode($res,true);
        if($res['result']==1)
        if(!empty($callback))
        {
            echo $res['account_total']."[#]".$res['account_cash'];
        }
        else
        {
            echo '0[#]0';
        }*/
        //$res=json_decode($res,true);
        //$res=json_encode($res);
        $callback = $_REQUEST['jsoncallback'];
        if (empty($callback)) {
            echo $res;
        } else {
            echo $callback . '(' . $res . ')';
        }
        exit();
    }

    function get_mall_account()
    {
        $user_id = $this->visitor->get('user_id');
        $data = array('user_id' => $user_id);
        $res = getHTML('http://' . $this->mall_url . '/index.php?app=user&act=account', $data);
        $callback = $_REQUEST['jsoncallback'];
        if (empty($callback)) {
            echo $res;

        } else {
            echo $callback . '(' . $res . ')';

        }
        exit();
    }

    /*
    2.转入资金
请求链接：http://hndai.p2p.com/index.php?user&q=code/account/i_accountl2m
传入数据：
user_id:注册时通过web_service生成的用户id
target:操作完成后转向的链接
op_id：商城的交易流水
mall_key：交易流水的加密值
money：操作金额
    */
    function i_accountl2m($user_id = 0, $money = 0)
    {
        $user_id = $_REQUEST['user_id'];
        $money = (float)$_REQUEST['money'];
        //验证是否是合法请求
        if ($_REQUEST['mall_key'] != DeCode($_REQUEST['op_id'], 'E') || $money <= 0) {
            echo '{"result":0,"error":"no_check"}';
            die();
        } else {
            $restr = '{"result":1,"user_id":"' . $user_id . '","money":"' . $money . '"}';
            $moneylog_mod =& m('moneylog');
            $row = $moneylog_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
            if ($row) {
                $array_log = array(
                    'jifen' => 0,
                    'money' => $money,
                    'time' => date('Y-m-d H:i:s'),
                    'user_name' => $row['user_name'],
                    'user_id' => $user_id,
                    'zcity' => $row['city'],
                    'type' => intval($_REQUEST['type']),
                    's_and_z' => 1,
                    'beizhu' => '',
                    'orderid' => '',
                    'dq_money' => $row['money'] + $money,
                    'dq_money_dj' => $row['money_dj'],
                    'dq_jifen' => $row['duihuanjifen'],
                    'dq_jifen_dj' => $row['dongjiejifen']
                );
                $moneylog_mod->add($array_log);//资金流水
                $sql = "update " . DB_PREFIX . "my_money set money=money+$money where user_id='$user_id' limit 1";
                $moneylog_mod->db->query($sql);
                echo $restr;
            } else {
                echo '{"result":0,"error":"no_user"}';
            }
            $row = null;
        }
    }


    /*
    转出资金
    */
    function i_accountm2l()
    {
        global $p2purl;
        $this->my_money_mod =& m('my_money');
        $user_id = $this->visitor->get('user_id');
        $money = trim($_POST['money']);
        $zhuanzhang = trim($_POST['zhuanzhang']);
        $money = (float)$money;
        if ($money <= 0) return 0;
        //验证帐号余额
        $moneylog_mod =& m('moneylog');
        $row = $moneylog_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id='$user_id' limit 1");
        $user_zf_pass = $row['zf_pass'];
        $zf_pass = md5(trim($_POST['zf_pass']));
        if ($user_zf_pass != $zf_pass) {
            $this->show_warning('zhifumimayanzhengshibai');
            return;
        }
        //echo $user_id;
        $op_id = date('Ymdhis') . rand(100, 999);
        $data = array(
            'user_id' => $user_id,
            'money' => $money,
            'op_id' => $op_id,
            'mall_key' => DeCode($op_id, 'E')
        );
        if ($row) {
            if ($money > $row['money']) {
                $this->show_warning(iconv('utf-8', 'gb2312', "帐户余额不足！"));
                return;//余额不足
            } else {
                if ($zhuanzhang == 1)//向借贷平台转账
                {
                    $result = getHTML('http://' . $this->p2p_url . '/index.php?user&q=code/account/i_accountm2l&type=1', $data);
                    $typee = 111;
                }
                if ($zhuanzhang == 2)//向供销平台转账
                {
                    $result = getHTML("http://" . $this->mall_url . "/index.php?app=user&act=i_accountl2m&type=117", $data);
                    $typee = 119;
                }
                $res = json_decode($result, true);
                //print_r($res);
                if ($res['result'] == 1) {
                    //资金流水
                    $array_log = array(
                        'jifen' => 0,
                        'money' => '-' . $money,
                        'time' => date('Y-m-d H:i:s'),
                        'user_name' => $row['user_name'],
                        'user_id' => $user_id,
                        'zcity' => $row['city'],
                        'type' => $typee,
                        's_and_z' => 2,
                        'beizhu' => '',
                        'orderid' => '',
                        'dq_money' => $row['money'] - $money,
                        'dq_money_dj' => $row['money_dj'],
                        'dq_jifen' => $row['duihuanjifen'],
                        'dq_jifen_dj' => $row['dongjiejifen']
                    );
                    $moneylog_mod->add($array_log);//资金流水
                    $sql = "update " . DB_PREFIX . "my_money set money=money-$money where user_id='$user_id' limit 1";
                    $moneylog_mod->db->query($sql);
                    $this->show_message('zhuanzhangchenggong', 'back_list', 'index.php?app=member');
                } else {
                    if ($res['error'] == 'no_user') {
                        $this->show_warning(iconv('utf-8', 'gb2312', "用户还没有激活，请登陆激活后再试！"));
                    } elseif ($res['error'] == 'no_check') {
                        $this->show_warning(iconv('utf-8', 'gb2312', "验证失败！"));
                    } else {
                        $this->show_warning('zhuanzhangshibai');
                    }
                }
            }

        }

    }

    /*
    4.将充值奖励所赚利息转入商城：
请求链接：http://hndai.p2p.com/index.php?user&q=code/account/i_awardl2m
传入数据：
user_id:注册时通过web_service生成的用户id
target:操作完成后转向的链接
op_id：商城的交易流水
mall_key：交易流水的加密值
money：操作金额
    */

    function i_awardl2m($user_id = 0, $money = 0)
    {
        if ($user_id == 0 || $money == 0) {
            $user_id = $_REQUEST['user_id'];
            $money = (float)$_REQUEST['money'];
        } else {
            $user_id = DeCode($user_id, 'E');
        }
        $restr = '{"result":1,"user_id":"' . $user_id . '","money":"' . $money . '"}';

        $op_id = date('Ymdhis') . rand(100, 999);
        $data = array(
            'user_id' => $user_id,
            'money' => $money,
            'op_id' => $op_id,
            'mall_key' => DeCode($op_id, 'E')
        );
        $user_id = (int)DeCode($user_id, 'D');
        $moneylog_mod =& m('moneylog');
        $row = $moneylog_mod->getRow("select * from " . DB_PREFIX . "my_money where user_id=$user_id limit 1");
        if ($row) {
            $array_log = array(
                'jifen' => 0,
                'money' => $money,
                'time' => date('Y-m-d H:i:s'),
                'user_name' => $row['user_name'],
                'user_id' => $user_id,
                'zcity' => $row['city'],
                'type' => 112,
                's_and_z' => 1,
                'beizhu' => '',
                'orderid' => '',
                'dq_money' => $row['money'] + $money,
                'dq_money_dj' => $row['money_dj'],
                'dq_jifen' => $row['duihuanjifen'],
                'dq_jifen_dj' => $row['dongjiejifen']
            );
            $moneylog_mod->add($array_log);//资金流水
            $sql = "update " . DB_PREFIX . "my_money set money=money+$money where user_id='$user_id' limit 1";
            $moneylog_mod->db->query($sql);
            echo $restr;
        } else {
            $restr = '{"result":0,"error":"no_user"}';
        }
        $row = null;
    }

    function get_rebateMoney()
    {
        $user_id = $this->visitor->get('user_id');
        $db = &db();

        $row=$db->getRow("select web_id from " . DB_PREFIX . "member where user_id=$user_id limit 1");
        if(empty($row['web_id'])){
            echo '0[#]0';
        }else{
            $url="http://".WEBSERV_IP1."/money.asp?web_id={$row['web_id']}";
            $html=getHTML($url);
            $arr=str_split($html,'[#]');
            echo floatval($arr[0]).'[#]'.floatval($arr[1]);
        }
    }

    function login()
    {
        $this->member_mod =& m('member');
        $user_id = $_REQUEST['user_id'];
        $lei = $_REQUEST['lei'];
        if (empty($lei)) {
            $user_id = (int)DeCode($user_id, 'D');
        }
        $row = $this->member_mod->getRow("select * from " . DB_PREFIX . "member where user_id='$user_id' limit 1");
        if (empty($row)) {
            $this->show_warning(iconv('utf-8', 'gb2312', "用户不存在，请登陆激活后再试！"));
            return;
        }
        $ms =& ms(); //连接用户中心
        $this->_do_login($user_id);
        $synlogin = $ms->user->synlogin($user_id);
        header("location:index.php?app=member");
    }

    function login_myother()
    {
        $user_id = $this->visitor->get('user_id');
        if (empty($user_id)) {
            echo 'error';
            exit;
        }
        $usid = urlencode(DeCode($user_id, 'E'));
        /*$this->_city_mod =& m('city');
        $cityrow=$this->_city_mod->get_cityrow();*/
        if ($_GET['t'] == 'm') {
            $url = "http://" . $this->mall_url . "/index.php?app=user&act=login&user_id={$usid}";
        } elseif ($_GET['t'] == 'p') {
            $url = "http://" . $this->p2p_url . "/index.action?user&q=action/login&user_id={$usid}";
        }
        header("location:$url");

    }
}

?>
