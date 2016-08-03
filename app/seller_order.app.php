<?php

/**
 *    买家的订单管理控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class Seller_orderApp extends StoreadminbaseApp
{
    function index()
    {
        /* 获取订单列表 */
        $this->_get_orders();
		
        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                         LANG::get('order_manage'), 'index.php?app=seller_order',
                         LANG::get('order_list'));

        /* 当前用户中心菜单 */
        $type = (isset($_GET['type']) && $_GET['type'] != '') ? trim($_GET['type']) : 'all_orders';
        $this->_curitem('order_manage');
        $this->_curmenu($type);
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('order_manage'));
		$this->order_mod=& m('order');
		$kaiguan=$this->order_mod->kg();
		$this->assign('kaiguan',$kaiguan);
        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
        /* 显示订单列表 */
        $this->display('seller_order.index.html');
    }

    /**
     *    查看订单详情
     *
     *    @author    Garbin
     *    @return    void
     */
    function view()
    {
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		$type=$_GET['type'];
		$model_order =& m('order');
		$kaiguan=$model_order->kg();
		$this->assign('kaiguan',$kaiguan);
		
		$user_id=$this->visitor->get('user_id');
		$canshu=$model_order->can();
		$bili=$canshu['jifenxianjin'];
		
		if($type==1)
		{
			$result=$model_order->getAll("select gh_id from ".DB_PREFIX."gonghuo where cangkuid='$user_id'");
		}
		else
		{
			$result=$model_order->getAll("select gh_id from ".DB_PREFIX."gonghuo where user_id='$user_id'");
		}
		
		$ghids=array();
		foreach($result as $row)
		{
			array_push($ghids,$row['gh_id']);
		}
		$ids=implode(',',$ghids);
		if(empty($ghids))
		{
			 $order_info  = $model_order->findAll(array(
            	'conditions'    => "order_alias.order_id={$order_id} AND seller_id=" . $this->visitor->get('manage_store'),
            	'join'          => 'has_orderextm',
        	));
		}
		else
		{
			$order_info  = $model_order->findAll(array(
            	'conditions'    => "order_alias.order_id={$order_id} AND (seller_id=" . $this->visitor->get('manage_store')." or order_alias.gh_id in($ids))",
            	'join'          => 'has_orderextm',
        	));
		}
   
        $order_info = current($order_info);
  if (!$order_info)
        {
            $this->show_warning('no_such_order');

            return;
        }

        /* 团购信息 */
        if ($order_info['extension'] == 'groupbuy')
        {
            $groupbuy_mod = &m('groupbuy');
            $group = $groupbuy_mod->get(array(
                'join' => 'be_join',
                'conditions' => 'order_id=' . $order_id,
                'fields' => 'gb.group_id',
            ));
            $this->assign('group_id',$group['group_id']);
        }

        /* 当前位置 */
        $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                         LANG::get('order_manage'), 'index.php?app=seller_order',
                         LANG::get('view_order'));

        /* 当前用户中心菜单 */
        $this->_curitem('order_manage');
        $this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('detail'));

        /* 调用相应的订单类型，获取整个订单详情数据 */
        $order_type =& ot($order_info['extension']);
        $order_detail = $order_type->get_order_detail($order_id, $order_info);
        $spec_ids = array();
        foreach ($order_detail['data']['goods_list'] as $key => $goods)
        {
            empty($goods['goods_image']) && $order_detail['data']['goods_list'][$key]['goods_image'] = Conf::get('default_goods_image');
            $spec_ids[] = $goods['spec_id'];

        }

        /* 查出最新的相应的货号 */
        $model_spec =& m('goodsspec');
        $spec_info = $model_spec->find(array(
            'conditions'    => $spec_ids,
            'fields'        => 'sku',
        ));
        foreach ($order_detail['data']['goods_list'] as $key => $goods)
        {
            $order_detail['data']['goods_list'][$key]['sku'] = $spec_info[$goods['spec_id']]['sku'];
        }

$buyer_id=$order_info['buyer_id'];
$result=$model_spec->getrow("select * from ".DB_PREFIX."member where user_id='$buyer_id' limit 1");
$vip=$result['vip'];
/*if($vip==1)
{
	 $order_info['shipping_jifen']=$order_info['shipping_fee']*$bili*(1+$canshu['lv21']);
}
else
{
	$order_info['shipping_jifen']=$order_info['shipping_fee']*$bili*(1+$canshu['lv31']);
}*/
		

		 /*if($order_info['discount_jifen']==0.00)
		 {
			$order_info['jifen']=$order_info['youhuidiscount_jifen'];
		 }
		 else
		 {*/
		 	$order_info['jifen']=$order_info['discount_jifen']+$order_info['youhuidiscount_jifen'];
		 /*}*/
	
 		if($order_info['pay_time']!="")
		{
			$order_info['pay_time']=date('Y-m-d H:i:s',$order_info['pay_time']);
		}
        $this->assign('order', $order_info);
        $this->assign($order_detail['data']);
        $this->display('seller_order.view.html');
    }
    /**
     *    收到货款
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function received_pay()
    {
        list($order_id, $order_info)    = $this->_get_valid_order_info(ORDER_PENDING);
        if (!$order_id)
        {
            echo Lang::get('no_such_order');

            return;
        }
        if (!IS_POST)
        {
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('order', $order_info);
            $this->display('seller_order.received_pay.html');
        }
        else
        {
            $model_order    =&  m('order');
            $model_order->edit(intval($order_id), array('status' => ORDER_ACCEPTED, 'pay_time' => gmtime()));
            if ($model_order->has_error())
            {
                $_errors = $model_order->get_error();
                $error = current($_errors);
                $this->pop_warning(Lang::get($error['msg']));

                return;
            }

            #TODO 发邮件通知
            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $order_log->add(array(
                'order_id'  => $order_id,
                'operator'  => addslashes($this->visitor->get('user_name')),
                'order_status' => order_status($order_info['status']),
                'changed_status' => order_status(ORDER_ACCEPTED),
                'remark'    => $_POST['remark'],
                'log_time'  => gmtime(),
            ));

            /* 发送给买家邮件，提示等待安排发货 */
            $model_member =& m('member');
            $buyer_info   = $model_member->get($order_info['buyer_id']);
            $mail = get_mail('tobuyer_offline_pay_success_notify', array('order' => $order_info));
           // $this->_mailto($buyer_info['email'], addslashes($mail['subject']), addslashes($mail['message']));
			//sendmail(addslashes($mail['subject']),addslashes($mail['message']),$buyer_info['email']);

            $new_data = array(
                'status'    => Lang::get('order_accepted'),
                'actions'   => array(
                    'cancel',
                    'shipped'
                ), //可以取消可以发货
            );

            $this->pop_warning('ok');
        }

    }

    /**
     *    货到付款的订单的确认操作
     *
     *    @author    Garbin
     *    @param    none
     *    @return    void
     */
    function confirm_order()
    {
        list($order_id, $order_info)    = $this->_get_valid_order_info(ORDER_SUBMITTED);
        if (!$order_id)
        {
            echo Lang::get('no_such_order');

            return;
        }
        if (!IS_POST)
        {
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('order', $order_info);
            $this->display('seller_order.confirm.html');
        }
        else
        {
            $model_order    =&  m('order');
            $model_order->edit($order_id, array('status' => ORDER_ACCEPTED));
            if ($model_order->has_error())
            {
                $_errors = $model_order->get_error();
                $error = current($_errors);
                $this->pop_warning(Lang::get($error['msg']));

                return;
            }

            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $order_log->add(array(
                'order_id'  => $order_id,
                'operator'  => addslashes($this->visitor->get('user_name')),
                'order_status' => order_status($order_info['status']),
                'changed_status' => order_status(ORDER_ACCEPTED),
                'remark'    => $_POST['remark'],
                'log_time'  => gmtime(),
            ));

            /* 发送给买家邮件，订单已确认，等待安排发货 */
            $model_member =& m('member');
            $buyer_info   = $model_member->get($order_info['buyer_id']);
            $mail = get_mail('tobuyer_confirm_cod_order_notify', array('order' => $order_info));
            //$this->_mailto($buyer_info['email'], addslashes($mail['subject']), addslashes($mail['message']));

//sendmail(addslashes($mail['subject']),addslashes($mail['message']),$buyer_info['email']);
            $new_data = array(
                'status'    => Lang::get('order_accepted'),
                'actions'   => array(
                    'cancel',
                    'shipped'
                ), //可以取消可以发货
            );

            $this->pop_warning('ok');;
        }
    }

    /**
     *    调整费用
     *
     *    @author    Garbin
     *    @return    void
     */
    function adjust_fee()
    {
        list($order_id, $order_info)    = $this->_get_valid_order_info(array(ORDER_SUBMITTED, ORDER_PENDING));
		
        if (!$order_id)
        {
            echo Lang::get('no_such_order');

            return;
        }
        $model_order    =&  m('order');
        $model_orderextm =& m('orderextm');
        $shipping_info   = $model_orderextm->get($order_id);
		$order_row=$model_order->getrow("select * from ".DB_PREFIX."order where order_id='$order_id' limit 1");
		$buyer_id=$order_row['buyer_id'];
		$coupon_id=$order_row['coupon_id'];
		$youhui_id=$order_row['youhui_id'];
		$member_row=$model_order->getrow("select * from ".DB_PREFIX."member where user_id='$buyer_id' limit 1");
		$canshu=$model_order->can();
		
		$youhui=$model_order->getrow("select * from ".DB_PREFIX."youhuiquan where youhui_id='$youhui_id' limit 1");
		$coupon=$model_order->getrow("select * from ".DB_PREFIX."coupon where coupon_id='$coupon_id' limit 1");
		
        if (!IS_POST)
        {
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('order', $order_info);
            $this->assign('shipping', $shipping_info);
			$this->assign('member_row',$member_row);
			
			$this->assign('coupon',$coupon);
			$this->assign('youhui',$youhui);
			$this->assign('canshu',$canshu);
            $this->display('seller_order.adjust_fee.html');
        }
        else
        {
			$vip=$member_row['vip'];
			$lv21=$canshu['lv21'];
			$lv31=$canshu['lv31'];
			
            /* 配送费用 */
            $shipping_fee = isset($_POST['shipping_fee']) ? abs(floatval($_POST['shipping_fee'])) : 0;
            /* 折扣金额 */
			if($order_row['daishou']==1)
			{
				$goods_amount=$order_row['goods_amount'];
			}
			else
			{
            $goods_amount     = isset($_POST['goods_amount'])     ? abs(floatval($_POST['goods_amount'])) : 0;
			}
			
			// $order_amount     = isset($_POST['order_amount'])     ? abs(floatval($_POST['order_amount'])) : 0;
			 // $order_jifen     = isset($_POST['order_jifen'])     ? abs(floatval($_POST['order_jifen'])) : 0;
			
            /* 订单实际总金额 */
            $order_amount = $goods_amount + $shipping_fee;

            if ($order_amount < 0.01)
            {
                /* 若商品总价＋配送费用扣队折扣小于等于0，则不是一个有效的数据 */
                $this->pop_warning('invalid_fee');

                return;
            }
			
			$ord = $goods_amount + $shipping_fee-$coupon['coupon_value']-$youhui['youhui_jine'];
			if($ord < 0.01 )
			{
				$this->pop_warning('invalid_fee');
				return;
			}
			
			
			if($vip==1)
			{
				$goods_jifen=$goods_amount*$canshu['jifenxianjin']*(1+$lv21);
				$order_jifen=$order_amount*$canshu['jifenxianjin']*(1+$lv21)-$coupon['coupon_jifen']-$youhui['youhui_jifen'];
				$fanhuan_jia=$order_amount*$canshu['jifenxianjin']-$coupon['coupon_jifen']-$youhui['youhui_jifen'];
				$fee=$shipping_fee*$canshu['jifenxianjin']*(1+$lv21);
			}
			else
			{
				$goods_jifen=$goods_amount*$canshu['jifenxianjin']*(1+$lv31);
				$order_jifen=$order_amount*$canshu['jifenxianjin']*(1+$lv31)-$coupon['coupon_jifen']-$youhui['youhui_jifen'];
				$fanhuan_jia=$order_amount*$canshu['jifenxianjin']-$coupon['coupon_jifen']-$youhui['youhui_jifen'];
				$fee=$shipping_fee*$canshu['jifenxianjin']*(1+$lv31);
			}
	
            $data = array(
                'goods_amount'  => $goods_amount,    //修改商品总价
				'goods_jifen'  => $goods_jifen,    //修改商品总积分
                'order_amount'  => $order_amount,    //修改订单实际总金额
				'order_jifen'  => $order_jifen,     //修改订单实际总积分
				'fanhuan_jia'  => $fanhuan_jia     //修改订单实际返还积分
            );
			$remark='';
            if ($shipping_fee != $shipping_info['shipping_fee'])
            {
                /* 若运费有变，则修改运费 */
				$remark=Lang::get('xiugaiyunfei');
				$remark=str_replace('{1}',$shipping_info['fee'],$remark);
				$remark=str_replace('{2}',$fee,$remark);
                $model_extm =& m('orderextm');
                $model_extm->edit($order_id, array('shipping_fee' => $shipping_fee,'fee'=>$fee));
            }
			if($goods_amount!=$order_row['goods_amount'])//若商品价格有变
			{
				$remark=Lang::get('xiugaishangpin');
				$remark=str_replace('{3}',$order_row['goods_jifen'],$remark);
				$remark=str_replace('{4}',$goods_jifen,$remark);
			}
			if($shipping_fee != $shipping_info['shipping_fee'] && $goods_amount!=$order_row['goods_amount'])
			{
				$remark=Lang::get('tongshixiugai');
				$remark=str_replace('{5}',$order_row['order_jifen'],$remark);
				$remark=str_replace('{6}',$order_jifen,$remark);
			}
			
			
            $model_order->edit($order_id, $data);

            if ($model_order->has_error())
            {
                $_errors = $model_order->get_error();
                $error = current($_errors);
                $this->pop_warning(Lang::get($error['msg']));

                return;
            }
            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $order_log->add(array(
                'order_id'  => $order_id,
                'operator'  => addslashes($this->visitor->get('user_name')),
                'order_status' => order_status($order_info['status']),
                'changed_status' => order_status($order_info['status']),
                'remark'    => $remark,
                'log_time'  => gmtime(),
            ));

            /* 发送给买家邮件通知，订单金额已改变，等待付款 */
            $model_member =& m('member');
            $buyer_info   = $model_member->get($order_info['buyer_id']);
            $mail = get_mail('tobuyer_adjust_fee_notify', array('order' => $order_info));
           // $this->_mailto($buyer_info['email'], addslashes($mail['subject']), addslashes($mail['message']));
		//sendmail(addslashes($mail['subject']),addslashes($mail['message']),$buyer_info['email']);

            $new_data = array(
                'order_amount'  => price_format($order_amount),
            );

            $this->pop_warning('ok');
        }
    }

    

    /**
     *    取消订单
     *
     *    @author    Garbin
     *    @return    void
     */
    function cancel_order()
    {
        /* 取消的和完成的订单不能再取消 */
        //list($order_id, $order_info)    = $this->_get_valid_order_info(array(ORDER_SUBMITTED, ORDER_PENDING, ORDER_ACCEPTED, ORDER_SHIPPED));
        $order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';
        if (!$order_id)
        {
            echo Lang::get('no_such_order');
        }
        //$status = array(ORDER_SUBMITTED, ORDER_PENDING, ORDER_ACCEPTED, ORDER_SHIPPED);
		$status = array(ORDER_SUBMITTED, ORDER_PENDING, ORDER_ACCEPTED);
        $order_ids = explode(',', $order_id);
        if ($ext)
        {
            $ext = ' AND ' . $ext;
        }

        $model_order    =&  m('order');
        /* 只有已发货的货到付款订单可以收货 */
        $order_info     = $model_order->find(array(
            'conditions'    => "order_id" . db_create_in($order_ids) . " AND seller_id=" . $this->visitor->get('manage_store') . " AND status " . db_create_in($status) . $ext,
        ));
	
		
        $ids = array_keys($order_info);
        if (!$order_info)
        {
            echo iconv('Gbk','Utf-8',Lang::get('meiyoudingdan'));

            return;
        }
        if (!IS_POST)
        {
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('orders', $order_info);
            $this->assign('order_id', count($ids) == 1 ? current($ids) : implode(',', $ids));
            $this->display('seller_order.cancel.html');
        }
        else
        {
            $model_order    =&  m('order');
            foreach ($ids as $val)
            {
                $id = intval($val);
                $model_order->edit($id, array('status' => ORDER_CANCELED));
                if ($model_order->has_error())
                {
                    //$_erros = $model_order->get_error();
                    //$error = current($_errors);
                    //$this->json_error(Lang::get($error['msg']));
                    //return;
                    continue;
                }

/* by:xiaohei QQ:77491010 商付通 更新商付通定单状态 开始*****************************************************/
$this->my_money_mod =& m('my_money');
$this->my_moneylog_mod =& m('my_moneylog');
$this->moneylog_mod =& m('moneylog');
$this->accountlog_mod =& m('accountlog');
$this->canshu_mod =& m('canshu');
$this->youhuilist_mod =& m('youhuilist');
$canshu=$this->my_money_mod->can();
$daishou=$canshu['daishou'];
$zong_money=$canshu['zong_money'];
$zong_jifen=$canshu['zong_jifen'];

$result=$this->my_moneylog_mod->getAll("select * from ".DB_PREFIX."my_moneylog where order_id='$id' and (caozuo='10' or caozuo='20')");
$riqi=date('Y-m-d H:i:s');
//更新总账户资金
	foreach($result as $my_moneylog_row)
	{
		$user_id=$my_moneylog_row['user_id'];
		$youhui_id=$my_moneylog_row['youhui_id'];//优惠券id
		$bianhao=$my_moneylog_row['bianhao'];//优惠券编号
		$city=$my_moneylog_row['city'];
		$user_name=$my_moneylog_row['user_name'];
		$money=$my_moneylog_row['money'];
		$money_dj=$my_moneylog_row['money_dj'];
		$money16=$money_dj*$daishou;
		$dongjiejifen=$my_moneylog_row['dongjiejifen'];
		$jifen16=$dongjiejifen*$daishou;
		$seller_id=$my_moneylog_row['seller_id'];
		$seller_name=$my_moneylog_row['seller_name'];
		if($my_moneylog_row['user_id']==$my_moneylog_row['buyer_id'] && $my_moneylog_row['s_and_z']==2)//买家
		{
			$buy_money_row=$this->my_money_mod->getrow("select * from ".DB_PREFIX."my_money where user_id='$user_id'");
			$buy_money=$buy_money_row['money'];//买家的钱			
			$new_buy_money = $buy_money+$money_dj;	
			$buy_jifen=$buy_money_row['duihuanjifen'];//买家的积分	
			$new_buy_jifen = $buy_jifen+$dongjiejifen;	
			$buy_moneydj=$buy_money_row['money_dj'];//买家的冻结金钱	
			$buy_dongjiejifen=$buy_money_row['dongjiejifen'];//买家的冻结积分		
			//更新数据
			$this->my_money_mod->edit('user_id='.$user_id,array('money'=>$new_buy_money,'duihuanjifen'=>$new_buy_jifen));
			
			//添加moneylog日志	
			//$beizhu=$user_name.Lang::get('zengjia');
			$beizhu=Lang::get('dingdan').$order_id;
			$buy_money=array(
			'money'=>$money_dj,
			'jifen'=>$dongjiejifen,
			'time'=>$riqi,
			'user_name'=>$user_name,
			'user_id'=>$user_id,
			'zcity'=>$city,
			'type'=>18,
			's_and_z'=>1,
			'beizhu'=>$beizhu,
			'dq_money'=>$new_buy_money,
			'dq_money_dj'=>$buy_moneydj,
			'dq_jifen'=>$new_buy_jifen,
			'dq_jifen_dj'=>$buy_dongjiejifen,	
	
			);
			$this->moneylog_mod->add($buy_money);	
			//更新总账户
			//$beizhu1=Lang::get('yinquxiaoyonghu').$seller_name.Lang::get('dedingdan');
			$beizhu1=Lang::get('dingdan').$order_id;
					$addaccount=array(
					'money'=>'-'.$money_dj,
					'jifen'=>'-'.$dongjiejifen,
					'time'=>$riqi,
					'user_name'=>$seller_name,
					'user_id'=>$seller_id,
					'zcity'=>$city,
					'type'=>18,
					's_and_z'=>2,
					'beizhu'=>$beizhu1,
					'dq_money'=>$zong_money-$money_dj,
					'dq_jifen'=>$zong_jifen-$dongjiejifen
					 );
					 $new_account_zongjifen=$zong_jifen-$dongjiejifen;
					 $new_account_zongmoney=$zong_money-$money_dj;
			
				$this->accountlog_mod->add($addaccount);
				$this->canshu_mod->edit('id=1',array("zong_jifen"=> $new_account_zongjifen,"zong_money"=>$new_account_zongmoney));
				
				if(!empty($bianhao))
				{
					$this->youhuilist_mod->edit('bianhao='.$bianhao,array("status"=>'yes'));
				}
			
		
		}
		/*else//卖家和供货商
		{
			$sell_money_row=$this->my_money_mod->getrow("select * from ".DB_PREFIX."my_money where user_id='$user_id'");
			$sell_money=$sell_money_row['money_dj'];//卖家的冻结资金
			$new_sell_money = $sell_money-$money_dj;
			$sell_jifen=$sell_money_row['dongjiejifen'];//卖家的冻结积分
			$new_sell_jifen = $sell_jifen-$dongjiejifen;
			$sell_mon=$sell_money_row['money'];//卖家的资金
			$sell_duihuanjifen=$sell_money_row['duihuanjifen'];//卖家的积分
			$this->my_money_mod->edit('user_id='.$user_id,array('money_dj'=>$new_sell_money,'dongjiejifen'=>$new_sell_jifen));
		    $beizhu=$user_name.Lang::get('jianshao');
				
			$sell_money=array(
			'money_dj'=>'-'.$money_dj,
			'jifen_dj'=>'-'.$dongjiejifen,
			'time'=>$riqi,
			'user_name'=>$user_name,
			'user_id'=>$user_id,
			'zcity'=>$city,
			'type'=>27,
			's_and_z'=>2,
			'beizhu'=>$beizhu,
			'dq_money'=>$sell_mon,
			'dq_money_dj'=>$new_sell_money,
			'dq_jifen'=>$sell_duihuanjifen,
			'dq_jifen_dj'=>$new_sell_jifen,	
			);	
			$this->moneylog_mod->add($sell_money);		
		}*/
		
	}	
$this->my_moneylog_mod->edit('order_id='.$id,array('caozuo'=>30));
	/*	$money=$my_moneylog_row['money'];//定单价格
		
		$buy_user_id=$my_moneylog_row['buyer_id'];//买家ID
		$sell_user_id=$my_moneylog_row['seller_id'];//卖家ID
		if($my_moneylog_row['order_id']=$id)
		{
			$buy_money_row=$this->my_money_mod->getrow("select * from ".DB_PREFIX."my_money where user_id='$buy_user_id'");
			$buy_money=$buy_money_row['money'];//买家的钱
			$sell_money_row=$this->my_money_mod->getrow("select * from ".DB_PREFIX."my_money where user_id='$sell_user_id'");
			$sell_money=$sell_money_row['money_dj'];//卖家的冻结资金
			$new_buy_money = $buy_money+$money;
			$new_sell_money = $sell_money-$money;
			//更新数据
			$this->my_money_mod->edit('user_id='.$buy_user_id,array('money'=>$new_buy_money));
			$this->my_money_mod->edit('user_id='.$sell_user_id,array('money_dj'=>$new_sell_money));
			//更新商付通log为 定单已取消
			$this->my_moneylog_mod->edit('order_id='.$id,array('caozuo'=>30));
			

		}*/
	/* by:xiaohei QQ:77491010 商付通 更新商付通定单状态 结束*************/

                /* 加回订单商品库存 */
                $model_order->change_stock('+', $id);
                $cancel_reason = (!empty($_POST['remark'])) ? $_POST['remark'] : $_POST['cancel_reason'];
                /* 记录订单操作日志 */
                $order_log =& m('orderlog');
                $order_log->add(array(
                    'order_id'  => $id,
                    'operator'  => addslashes($this->visitor->get('user_name')),
                    'order_status' => order_status($order_info[$id]['status']),
                    'changed_status' => order_status(ORDER_CANCELED),
                    'remark'    => $cancel_reason,
                    'log_time'  => gmtime(),
                ));

                /* 发送给买家订单取消通知 */
                $model_member =& m('member');
                $buyer_info   = $model_member->get($order_info[$id]['buyer_id']);
                $mail = get_mail('tobuyer_cancel_order_notify', array('order' => $order_info[$id], 'reason' => $_POST['remark']));
                //$this->_mailto($buyer_info['email'], addslashes($mail['subject']), addslashes($mail['message']));
				//sendmail(addslashes($mail['subject']),addslashes($mail['message']),$buyer_info['email']);

                $new_data = array(
                    'status'    => Lang::get('order_canceled'),
                    'actions'   => array(), //取消订单后就不能做任何操作了
                );
			$this->message_mod=& m('message');
			$notice=Lang::get('quxiaoding');
			$notice=str_replace('{1}',$order_info[$id]['buyer_name'],$notice);
			$notice=str_replace('{2}',$order_info[$id]['order_sn'],$notice);	
			$add_notice=array(
			'from_id'=>0,
			'to_id'=>$order_info[$id]['buyer_id'],
			'content'=>$notice,  
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3,
			);				
			$this->message_mod->add($add_notice);
				
				
				
        	}
            $this->pop_warning('ok', 'seller_order_cancel_order');
        }

    }

    /**
     *    完成交易(货到付款的订单)
     *
     *    @author    Garbin
     *    @return    void
     */
    function finished()
    {
        list($order_id, $order_info)    = $this->_get_valid_order_info(ORDER_SHIPPED, 'payment_code=\'cod\'');
        if (!$order_id)
        {
            echo Lang::get('no_such_order');

            return;
        }
        if (!IS_POST)
        {
            header('Content-Type:text/html;charset=' . CHARSET);
            /* 当前用户中心菜单 */
            $this->_curitem('seller_order');
            /* 当前所处子菜单 */
            $this->_curmenu('finished');
            $this->assign('_curmenu','finished');
            $this->assign('order', $order_info);
            $this->display('seller_order.finished.html');
        }
        else
        {
            $now = gmtime();
            $model_order    =&  m('order');
            $model_order->edit($order_id, array('status' => ORDER_FINISHED, 'pay_time' => $now, 'finished_time' => $now));
            if ($model_order->has_error())
            {
                $_errors = $model_order->get_error();
                $error = current($_errors);
                $this->pop_warning(Lang::get($error['msg']));

                return;
            }

            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $order_log->add(array(
                'order_id'  => $order_id,
                'operator'  => addslashes($this->visitor->get('user_name')),
                'order_status' => order_status($order_info['status']),
                'changed_status' => order_status(ORDER_FINISHED),
                'remark'    => $_POST['remark'],
                'log_time'  => gmtime(),
            ));

            /* 更新累计销售件数 */
            $model_goodsstatistics =& m('goodsstatistics');
            $model_ordergoods =& m('ordergoods');
            $order_goods = $model_ordergoods->find("order_id={$order_id}");
            foreach ($order_goods as $goods)
            {
                $model_goodsstatistics->edit($goods['goods_id'], "sales=sales+{$goods['quantity']}");
            }
            
            
            /* 发送给买家交易完成通知，提示评论 */
            $model_member =& m('member');
            $buyer_info   = $model_member->get($order_info['buyer_id']);
            $mail = get_mail('tobuyer_cod_order_finish_notify', array('order' => $order_info));
           // $this->_mailto($buyer_info['email'], addslashes($mail['subject']), addslashes($mail['message']));
//sendmail(addslashes($mail['subject']),addslashes($mail['message']),$buyer_info['email']);
            $new_data = array(
                'status'    => Lang::get('order_finished'),
                'actions'   => array(), //完成订单后就不能做任何操作了
            );

            $this->pop_warning('ok');
        }

    }

    /**
     *    获取有效的订单信息
     *
     *    @author    Garbin
     *    @param     array $status
     *    @param     string $ext
     *    @return    array
     */
    function _get_valid_order_info($status, $ext = '')
    {
		$model_order    =&  m('order');
		
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		
		$ship_order=$model_order->getrow("select * from ".DB_PREFIX."order where order_id='$order_id' limit 1");
		$is_gh=$ship_order['is_gh'];
		$seller_id=$ship_order['seller_id'];
		
        if (!$order_id)
        {

            return array();
        }
        if (!is_array($status))
        {
            $status = array($status);
        }

        if ($ext)
        {
            $ext = ' AND ' . $ext;
        }

        
        /* 只有已发货的货到付款订单可以收货 */
		if($is_gh==1)
		{
			 $order_info     = $model_order->get(array(
            'conditions'    => "order_id={$order_id} AND seller_id='$seller_id' AND status " . db_create_in($status) . $ext,
        ));
		}
		else
		{	
        $order_info     = $model_order->get(array(
            'conditions'    => "order_id={$order_id} AND seller_id=" . $this->visitor->get('manage_store') . " AND status " . db_create_in($status) . $ext,
        ));
		}
        if (empty($order_info))
        {

            return array();
        }


        return array($order_id, $order_info);
    }
    /**
     *    获取订单列表
     *
     *    @author    Garbin
     *    @return    void
     */
    function _get_orders()
    {
        $page = $this->_get_page();
        $model_order =& m('order');

        !$_GET['type'] && $_GET['type'] = 'all_orders';

        $conditions = '';

        // 团购订单
        if (!empty($_GET['group_id']) && intval($_GET['group_id']) > 0)
        {
            $groupbuy_mod = &m('groupbuy');
            $order_ids = $groupbuy_mod->get_order_ids(intval($_GET['group_id']));
            $order_ids && $conditions .= ' AND order_alias.order_id' . db_create_in($order_ids);
        }

        $conditions .= $this->_get_query_conditions(array(
            array(      //按订单状态搜索
                'field' => 'status',
                'name'  => 'type',
                'handler' => 'order_status_translator',
            ),
            array(      //按买家名称搜索
                'field' => 'buyer_name',
                'equal' => 'LIKE',
            ),
            array(      //按下单时间搜索,起始时间
                'field' => 'add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),
            array(      //按下单时间搜索,结束时间
                'field' => 'add_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'=> 'gmstr2time_end',
            ),
            array(      //按订单号
                'field' => 'order_sn',
            ),
        ));

        /* 查找订单 */
        $orders = $model_order->findAll(array(
            'conditions'    => "seller_id=" . $this->visitor->get('manage_store') . "{$conditions}",
            'count'         => true,
            'join'          => 'has_orderextm',
            'limit'         => $page['limit'],
            'order'         => 'add_time DESC',
            'include'       =>  array(
                'has_ordergoods',       //取出商品
            ),
        ));
        foreach ($orders as $key1 => $order)
        {
            foreach ($order['order_goods'] as $key2 => $goods)
            {
                empty($goods['goods_image']) && $orders[$key1]['order_goods'][$key2]['goods_image'] = Conf::get('default_goods_image');
				
            }
			$sellerid=$order['seller_id'];
			$sell_money = $model_order->getRow("select * from ".DB_PREFIX."my_money where user_id='$sellerid' limit 1");
			$duihuanjifen=$sell_money['duihuanjifen'];
			$suodingjifen=$sell_money['suodingjifen'];
			$orders[$key1]['keyongjifen']=$duihuanjifen-$suodingjifen;
			 //$orders[$key1]['fanhuan_jia']=round($order['fanhuan_jia'],2); 
        }

        $page['item_count'] = $model_order->getCount();
        $this->_format_page($page);
        $this->assign('types', array('all' => Lang::get('all_orders'),
                                     'pending' => Lang::get('pending_orders'),
                                     'submitted' => Lang::get('submitted_orders'),
                                     'accepted' => Lang::get('accepted_orders'),
                                     'shipped' => Lang::get('shipped_orders'),
                                     'finished' => Lang::get('finished_orders'),
                                     'canceled' => Lang::get('canceled_orders')));
        $this->assign('type', $_GET['type']);
        $this->assign('orders', $orders);
        $this->assign('page_info', $page);
    }
    /*三级菜单*/
    function _get_member_submenu()
    {
        $array = array(
            array(
                'name' => 'all_orders',
                'url' => 'index.php?app=seller_order&amp;type=all_orders',
            ),
            array(
                'name' => 'pending',
                'url' => 'index.php?app=seller_order&amp;type=pending',
            ),
            array(
                'name' => 'submitted',
                'url' => 'index.php?app=seller_order&amp;type=submitted',
            ),
            array(
                'name' => 'accepted',
                'url' => 'index.php?app=seller_order&amp;type=accepted',
            ),
            array(
                'name' => 'shipped',
                'url' => 'index.php?app=seller_order&amp;type=shipped',
            ),
            array(
                'name' => 'finished',
                'url' => 'index.php?app=seller_order&amp;type=finished',
            ),
            array(
                'name' => 'canceled',
                'url' => 'index.php?app=seller_order&amp;type=canceled',
        ),
        );
        return $array;
    }
	function gonghuo_order()
	{
	 $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('gonghuoorder'));

            /* 当前用户中心菜单 */
    $this->_curitem('gonghuoorder');
	$page = $this->_get_page();
    $model_order =& m('order');
	$this->gonghuo_mod=& m('gonghuo');
	$user_id=$this->visitor->get('user_id');
	
	$row=$this->gonghuo_mod->getRow("select is_cangku from ".DB_PREFIX."store where store_id='$user_id' limit 1");
	$this->assign('row',$row);
	$this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
	
	
	
	$gh_order=$this->gonghuo_mod->getAll("select *, o.status s from ".DB_PREFIX."order o left join ". DB_PREFIX ."order_goods og on o.order_id=og.order_id left join ". DB_PREFIX ."gonghuo gh on gh.gh_id=o.gh_id where gh.user_id='$user_id' order by o.pay_time desc limit {$page['limit']} ");
	
$coun=$this->gonghuo_mod->getAll("select *, o.status s from ".DB_PREFIX."order o left join ". DB_PREFIX ."order_goods og on o.order_id=og.order_id left join ". DB_PREFIX ."gonghuo gh on gh.gh_id=o.gh_id where gh.user_id='$user_id' order by o.pay_time desc ");

	 $page['item_count'] = count($coun);
        $this->_format_page($page);
		$this->assign('page_info', $page);
		$this->assign('gh_order', $gh_order);
		$this->display('gonghuo_order.html');
	
	}
	function gh_querenfahuo()
	{
		$order_id =  intval($_GET['order_id']);
		$this->gonghuo_mod=& m('order');
		$this->gonghuo_mod->edit('order_id='.$order_id, array('gh_status'=>1,'gh_riqi'=>date('Y-m-d H:i:s')));
		
		 $this->show_message('fahuochenggong');
		        return;
	}
	
	
	/**
     *    待发货的订单发货
     *
     *    @author    Garbin
     *    @return    void
     */
    function shipped()
    {
		$this->message_mod=& m('message');
		$this->moneylog_mod=& m('moneylog');
		$this->accountlog_mod=& m('accountlog');
		$this->canshu_mod=& m('canshu');
		$this->my_money_mod =& m('my_money');
		$this->webservice_list_mod=& m('webservice_list');
        list($order_id, $order_info)    = $this->_get_valid_order_info(array(ORDER_ACCEPTED, ORDER_SHIPPED));
        if (!$order_id)
        {
            echo Lang::get('no_such_order');

            return;
        }
        $model_order    =&  m('order');

        if (!IS_POST)
        {
            /* 显示发货表单 */
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->assign('order', $order_info);
            $this->display('seller_order.shipped.html');
        }
        else
        {
			
			
			if (!$_POST['invoice_no'])
            {
                $this->pop_warning('invoice_no_empty');

                return;
            }
			
			
			$orderrow=$this->message_mod->getRow("select * from ".DB_PREFIX."order  where order_id='$order_id' limit 1");
			
			$baojia=$orderrow['baojia'];
			$daishou=$orderrow['daishou'];
			$sellerid=$orderrow['seller_id'];
			$gh_id=$orderrow['gh_id'];
			$riqi=date('Y-m-d H:i:s');
			$canshu=$this->message_mod->can();
			if($daishou==1)//采购的商品
			{
				 $wuliufei=abs((float)$_POST['wuliufei']);//钱
				 $wuliufei=$wuliufei*$canshu['jifenxianjin'];
				 $yuanjia=$wuliufei;
				 $row_kaiguan=$this->message_mod->getRow("select webservice from ".DB_PREFIX."kaiguan limit 1");
				 $mem=$this->message_mod->getRow("select web_id,city from ".DB_PREFIX."member where user_id='$sellerid' limit 1");
	 		     $webservice=$row_kaiguan['webservice'];
				 $beizhu=Lang::get('dingdanhaowei').$order_id;
				if($orderrow['chengjie']==1)//受到惩戒
				{
					$wuliufei=$wuliufei*(1+$canshu['chengjie']);
					$beizhu=Lang::get('dingdanhaowei').$order_id.Lang::get('shoudaochengjie');
				}
				if($baojia==1)
				{
					$wuliufei=$wuliufei*(1+$canshu['lv31']);
					//$tai=$wuliufei*$canshu['lv31'];
					if($webservice=="yes")
					{
						$post_data = array(
						"ID"=>$mem['web_id'],
						"Money"=>$yuanjia,
						"MoneyType"=>1,
						"Count"=>1
						); 
						if($yuanjia>=1)
						{
							$web_id= webService('C_Consume',$post_data);
							$daa=array(
								"gong_id"=>$sellerid,
								"type"=>1,
								"money"=>$yuanjia,
								"time"=>date('Y-m-d H:i:s'),
								"consume_id"=>$web_id,
								"status"=>0,
								"city"=>$mem['city']
								);
								$this->webservice_list_mod->add($daa);	
						}	
										
					}
					$beizhu=Lang::get('dingdanhaowei').$order_id.Lang::get('baojia');		
				}
				
				$result=$this->message_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$sellerid' limit 1");
				
				$gh=$this->message_mod->getRow("select user_name from ".DB_PREFIX."gonghuo where gh_id='$gh_id' limit 1");
				
				$new_duihuanjifen=$result['duihuanjifen']-$wuliufei;
				$new_zongjifen=$canshu['zong_jifen']+$wuliufei;
				//添加用户日志
				$sell_money=array(
				'jifen'=>'-'.$wuliufei,
			   // 'money'=>$order_amount,
				'time'=>$riqi,
				'user_name'=>$result['user_name'],
				'user_id'=>$sellerid,
				'zcity'=>$result['city'],
				'type'=>123,
				's_and_z'=>2,
				'beizhu'=>$beizhu,
				'orderid'=>$order_id,
				'dq_money'=>$result['money'],
				'dq_money_dj'=>$result['money_dj'],
				'dq_jifen'=>$new_duihuanjifen,
				'dq_jifen_dj'=>$result['dongjiejifen']				
			  );
				$beizhu1=Lang::get('dingdanhaowei').$order_id;
				$addaccount=array(
					'jifen'=>$wuliufei,
					//'money'=>'-'.$gonghuo_money,
					'time'=>$riqi,
					'user_name'=>$result['user_name'],
					'user_id'=>$sellerid,
					'zcity'=>$result['city'],
					'type'=>123,
					's_and_z'=>1,
					'beizhu'=>$beizhu1,
					'dq_money'=>$canshu['zong_money'],
					'dq_jifen'=>$new_zongjifen,
					'xiaofei'=>$orderrow['buyer_name'],
					'shangjia'=>$result['user_name'],
					'gonghuoshang'=>$gh['user_name']
				);		
				$this->my_money_mod->edit('user_id='.$sellerid,array('duihuanjifen'=>$new_duihuanjifen));
				$this->moneylog_mod->add($sell_money);
				$this->accountlog_mod->add($addaccount);
				$this->canshu_mod->edit('id=1',array('zong_jifen'=>$new_zongjifen));
				
			}
			
			$riqi=date('Y-m-d H:i:s');
            $edit_data = array('status' => ORDER_SHIPPED, 'invoice_no' => $_POST['invoice_no'],'gh_status'=>1,'gh_riqi'=>$riqi);
            $is_edit = true;
            if (empty($order_info['invoice_no']))
            {
//by:xiaohei QQ:77491010 商付通 更新商付通定单状态 简写到一句
if($order_info['payment_code']=='sft'){$my_moneylog=& m('my_moneylog')->edit('order_id='.$order_id,array('caozuo'=>20));}
//by:xiaohei QQ:77491010 商付通 更新商付通定单状态 结束
			
                /* 不是修改发货单号 */
                $edit_data['ship_time'] = gmtime();
                $is_edit = false;
            }
            $model_order->edit(intval($order_id), $edit_data);
            if ($model_order->has_error())
            {
                $_errors = $model_order->get_error();
                $error = current($_errors);
                $this->pop_warning(Lang::get($error['msg']));

                return;
            }

            #TODO 发邮件通知
            /* 记录订单操作日志 */
            $order_log =& m('orderlog');
            $order_log->add(array(
                'order_id'  => $order_id,
                'operator'  => addslashes($this->visitor->get('user_name')),
                'order_status' => order_status($order_info['status']),
                'changed_status' => order_status(ORDER_SHIPPED),
                'remark'    => $_POST['remark'],
                'log_time'  => gmtime(),
            ));


            /* 发送给买家订单已发货通知 */
            $model_member =& m('member');
            $buyer_info   = $model_member->get($order_info['buyer_id']);
            $order_info['invoice_no'] = $edit_data['invoice_no'];
            $mail = get_mail('tobuyer_shipped_notify', array('order' => $order_info));
           // $this->_mailto($buyer_info['email'], addslashes($mail['subject']), addslashes($mail['message']));
		//sendmail(addslashes($mail['subject']),addslashes($mail['message']),$buyer_info['email']);

            $new_data = array(
                'status'    => Lang::get('order_shipped'),
                'actions'   => array(
                    'cancel',
                    'edit_invoice_no'
                ), //可以取消可以发货
            );
            if ($order_info['payment_code'] == 'cod')
            {
                $new_data['actions'][] = 'finish';
            }
			$notice=Lang::get('fahuo');
			$notice=str_replace('{1}',$order_info['buyer_name'],$notice);	
			//$beizhu="<a href='index.php?type=shipped&app=buyer_order&act=index' target='_blank'>".$notice."</a>";
			$add_notice=array(
			'from_id'=>0,
			'to_id'=>$order_info['buyer_id'],
			'content'=>$notice,  
			'add_time'=>gmtime(),
			'last_update'=>gmtime(),
			'new'=>1,
			'parent_id'=>0,
			'status'=>3,
			);
			$this->message_mod->add($add_notice);
			
            $this->pop_warning('ok');
        }
    }
	
	
	function cangku_order()
	{
	 $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('cangkuorder'));

            /* 当前用户中心菜单 */
    $this->_curitem('gonghuoorder');
	$page = $this->_get_page();
    $model_order =& m('order');
	$this->gonghuo_mod=& m('gonghuo');
	$user_id=$this->visitor->get('user_id');
	
	$this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
	
	
	
	$gh_order=$this->gonghuo_mod->getAll("select *, o.status s from ".DB_PREFIX."order o left join ". DB_PREFIX ."order_goods og on o.order_id=og.order_id left join ". DB_PREFIX ."gonghuo gh on gh.gh_id=o.gh_id where gh.cangkuid='$user_id' and o.tongzhi_cangku=1 order by o.pay_time desc limit {$page['limit']} ");

$coun=$this->gonghuo_mod->getAll("select *, o.status s from ".DB_PREFIX."order o left join ". DB_PREFIX ."order_goods og on o.order_id=og.order_id left join ". DB_PREFIX ."gonghuo gh on gh.gh_id=o.gh_id where gh.cangkuid='$user_id' and o.tongzhi_cangku=1 order by o.pay_time desc ");

	 $page['item_count'] = count($coun);
        $this->_format_page($page);
		$this->assign('page_info', $page);
		$this->assign('gh_order', $gh_order);
		$this->display('cangku_order.html');
	
	}
	
	function tongzhifahuo()
	{
		$order_id=$_GET['id'];
		$this->order_mod=& m('order');
		if($_POST)
		{
			$id=$_POST['orid'];
			$cj=$_POST['cj'];
			$baojia=$_POST['baojia'];
			if($cj==1)//惩戒
			{
				$this->order_mod->edit('order_id='.$id,array('chengjie'=>1));
			}
			
			$this->order_mod->edit('order_id='.$id,array('tongzhi_cangku'=>1,'baojia'=>$baojia));
			
			$this->pop_warning('ok');
		}
		else
		{
			/* 当前位置 */
			$this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
							 LANG::get('order_manage'), 'index.php?app=seller_order',
							 LANG::get('order_list'));
	
			/* 当前用户中心菜单 */
			$type = (isset($_GET['type']) && $_GET['type'] != '') ? trim($_GET['type']) : 'all_orders';
			$this->_curitem('order_manage');
			$this->_curmenu($type);
			$this->assign('page_title', Lang::get('member_center') . ' - ' . Lang::get('order_manage'));		
			
			
			$orders=$this->order_mod->getRow("select o.seller_id,oe.shipping_fee,oe.fee from ".DB_PREFIX."order o left join ".DB_PREFIX."order_extm oe on o.order_id=oe.order_id where o.order_id='$order_id' limit 1");
			$sellerid=$orders['seller_id'];
			$row=$this->order_mod->getRow("select * from ".DB_PREFIX."my_money where user_id='$sellerid' limit 1");
			$duihuanjifen=$row['duihuanjifen'];
			$suodingjifen=$row['suoding_jifen'];
			$fee=$orders['fee'];//运费积分
			$keyongjifen=$duihuanjifen-$suodingjifen;
			$aa=array();
			$aa['keyongjifen']=$keyongjifen;
			$aa['fee']=$fee;
			$this->assign('aa',$aa);
			$this->assign('orderid',$order_id);
			$this->display('tongzhi_fahuo.html');
		}
	}
	function cangkulist()
	{
		$this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('cangkulist'));

            /* 当前用户中心菜单 */
		$this->_curitem('gonghuoorder');
		$this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));
		
		$conditions=" and 1=1 ";
		$ghusername=$_GET['ghusername'];
		$this->assign('ghusername',$ghusername);
		if($ghusername!='')
		{
			$conditions.=" and user_name like '%$ghusername%' ";
		}
		$add_time_from=$_GET['add_time_from'];
		$add_time_to=$_GET['add_time_to'];
		if($add_time_from!='')
		{
			$conditions.=" and riqi >= '$add_time_from' ";
			$this->assign('add_time_from',$add_time_from);
		}
		if($add_time_to!='')
		{
			$conditions.=" and riqi <= '$add_time_to 24:59:59' ";
			$this->assign('add_time_from',$add_time_from);
		}
		$zong_kucunq=$_GET['zong_kucunq'];
		$zong_kucunz=$_GET['zong_kucunz'];
		if($zong_kucunq!='')
		{
			$conditions.=" and zong_kucun >= '$zong_kucunq' ";
			$this->assign('zong_kucunq',$zong_kucunq);
		}
		if($zong_kucunz!='')
		{
			$conditions.=" and zong_kucun <= '$zong_kucunz' ";
			$this->assign('zong_kucunz',$zong_kucunz);
		}
		$shengyu_kucunq=$_GET['shengyu_kucunq'];
		$shengyu_kucunz=$_GET['shengyu_kucunz'];
		if($shengyu_kucunq!='')
		{
			$conditions.=" and shengyu_kucun >= '$shengyu_kucunq' ";
			$this->assign('shengyu_kucunq',$shengyu_kucunq);
		}
		if($shengyu_kucunz!='')
		{
			$conditions.=" and shengyu_kucun <= '$shengyu_kucunz' ";
			$this->assign('shengyu_kucunz',$shengyu_kucunz);
		}
		
		$page = $this->_get_page();
		$this->gonghuo_mod=& m('gonghuo');
		$user_id=$this->visitor->get('user_id');
		$row=$this->gonghuo_mod->getAll("select g.*,s.shipping_name from ".DB_PREFIX."gonghuo g left join ".DB_PREFIX."shippings s on g.shipping_id=s.shipping_id  where cangkuid='$user_id' ".$conditions." order by gh_id desc ");
		$page['item_count'] = count($row);
        $this->_format_page($page);
		$this->assign('page_info', $page);
		$this->assign('row',$row);
		$this->display('cangkulist.html');
	}
	function cangku_edit()
	{
		$gh_id=$_GET['id'];
		$userid=$this->visitor->get('user_id');
		$this->gonghuo_mod=& m('gonghuo');
		if($_POST)
		{
			$ghid=$_POST['ghid'];
			$shipping_id=$_POST['shipping_id'];
			
			$this->gonghuo_mod->edit('gh_id='.$ghid,array('shipping_id'=>$shipping_id,'status'=>7));
			$this->show_message('caozuochenggong','','index.php?app=seller_order&act=cangkulist');
		}
		else
		{
			$this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('gonghuo'), 'index.php?app=my_theme&act=gonghuo',
                             LANG::get('cangkulist'));

            /* 当前用户中心菜单 */
		$this->_curitem('gonghuoorder');
			$row=$this->gonghuo_mod->getAll("select * from ".DB_PREFIX."shippings where store_id='$userid' ");
			$gh=$this->gonghuo_mod->getRow("select * from ".DB_PREFIX."gonghuo where gh_id='$gh_id' limit 1 ");
			$this->assign('row',$row);
			$this->assign('gh',$gh);
			
			$this->display('cangku_edit.html');
		}
	}
	
	
}

?>
