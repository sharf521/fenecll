<?php

class StoreApp extends StorebaseApp
{
    function index()
    {
	    
		$this->kaiguan_mod=& m('kaiguan');
		$kaiguan=$this->kaiguan_mod->kg();
        /* 店铺信息 */
        $_GET['act'] = 'index';
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);

        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
		
        $this->set_store($id);
        $store = $this->get_store_data();

        //当前域名处理  如果有 www 则去掉
        $domain=$_SERVER['HTTP_HOST'];
        $domain=explode(".",$domain);
        if($domain[0]=="www")
        {
            unset($domain[0]);
        }
        $domain=implode(".",$domain);


        /*if($store['domain']=='')
        {
            $this->show_warning(iconv("utf-8","gb2312//IGNORE",'店铺信息不全，未设置二级域名'));
            return;
        }*/

        //跳转到对应店铺二级域名
        if($store['domain']!='')
        {
            header("Location: http://".$store['domain'].".". $domain);exit;
        }
        else
        {
            header("Location: http://shop-".$store['store_id'].".". $domain);exit;
        }



        $this->assign('def', 1);
        $this->assign('store', $store);
		$this->assign('kaiguan', $kaiguan);
        /* 取得友情链接 */
        $this->assign('partners', $this->_get_partners($id));

        /* 取得推荐商品 */
        $this->assign('recommended_goods', $this->_get_recommended_goods($id));
		
        $this->assign('new_groupbuy', $this->_get_new_groupbuy($id));

        /* 取得最新商品 */
        $this->assign('new_goods', $this->_get_new_goods($id));
		$this->store_views($id);//店铺点击量

        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store', $store['store_name']);
        $this->assign('page_title', $store['store_name'] . ' - ' );
        $this->display('store.index.html');
    }

    function search()
    {
	
	    $this->kaiguan_mod=& m('kaiguan');
		$kaiguan=$this->kaiguan_mod->kg();
        /* 店铺信息 */
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
		
        $this->set_store($id);
        $store = $this->get_store_data();
        $this->assign('store', $store);
		$this->assign('kaiguan', $kaiguan);
		

        /* 搜索到的商品 */
        $this->_assign_searched_goods($id);

        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store',
            $store['store_name'], 'index.php?app=store&amp;id=' . $store['store_id'],
            LANG::get('goods_list')
        );

        $this->assign('page_title', Lang::get('goods_list') . ' - ' . $store['store_name'].' - ');
        $this->display('store.search.html');
    }

    function groupbuy()
    {
        /* 店铺信息 */
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
		
        $this->set_store($id);
        $store = $this->get_store_data();
        $this->assign('store', $store);

        /* 搜索团购 */
        empty($_GET['state']) &&  $_GET['state'] = 'on';
        $conditions = '1=1';
        if ($_GET['state'] == 'on')
        {
            $conditions .= ' AND gb.state ='. GROUP_ON .' AND gb.end_time>' . gmtime();
            $search_name = array(
                array(
                    'text'  => Lang::get('group_on')
                ),
                array(
                    'text'  => Lang::get('all_groupbuy'),
                    'url'  => url('app=store&act=groupbuy&state=all&id=' . $id)
                ),
            );
        }
        else if ($_GET['state'] == 'all')
        {
            $conditions .= ' AND gb.state '. db_create_in(array(GROUP_ON,GROUP_END,GROUP_FINISHED));
            $search_name = array(
                array(
                    'text'  => Lang::get('all_groupbuy')
                ),
                array(
                    'text'  => Lang::get('group_on'),
                    'url'  => url('app=store&act=groupbuy&state=on&id=' . $id)
                ),
            );
        }

        $page = $this->_get_page(16);
        $groupbuy_mod = &m('groupbuy');
        $groupbuy_list = $groupbuy_mod->find(array(
            'fields'    => 'goods.default_image, gb.group_name, gb.group_id, gb.spec_price, gb.end_time, gb.state',
            'join'      => 'belong_goods',
            'conditions'=> $conditions . ' and gb.status=1 AND gb.store_id=' . $id ,
            'order'     => 'group_id DESC',
            'limit'     => $page['limit'],
            'count'     => true
        ));
        $page['item_count'] = $groupbuy_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
		$canshu=$groupbuy_mod->can();
		$kaiguan=$groupbuy_mod->kg();
		$this->assign('kaiguan',$kaiguan);
        if (empty($groupbuy_list))
        {
            $groupbuy_list = array();
        }
        foreach ($groupbuy_list as $key => $_g)
        {
            empty($groupbuy_list[$key]['default_image']) && $groupbuy_list[$key]['default_image'] = Conf::get('default_goods_image');
            $tmp = current(unserialize($_g['spec_price']));
            $groupbuy_list[$key]['price'] = $tmp['price'];
			$groupbuy_list[$key]['jifen_price'] =round( $tmp['price']*$canshu['jifenxianjin']*(1+$canshu['lv31']),2);
			$groupbuy_list[$key]['vip_price'] = round($tmp['price']*$canshu['jifenxianjin']*(1+$canshu['lv21']),2);
            if ($_g['end_time'] < gmtime())
            {
                $groupbuy_list[$key]['group_state'] = group_state($_g['state']);
            }
            else
            {
                $groupbuy_list[$key]['lefttime'] = lefttime($_g['end_time']);
            }
        }
        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store',
            $store['store_name'], 'index.php?app=store&amp;id=' . $store['store_id'],
            LANG::get('groupbuy_list')
        );
		
        $this->assign('groupbuy_list', $groupbuy_list);
        $this->assign('search_name', $search_name);
		
        $this->assign('page_title', $search_name[0]['text'] . ' - ' . $store['store_name'].' - ');
        $this->display('store.groupbuy.html');
    }

    function article()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $article = $this->_get_article($id);
        if (!$article)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $this->assign('article', $article);

        /* 店铺信息 */
        $this->set_store($article['store_id']);
        $store = $this->get_store_data();
        $this->assign('store', $store);

        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store',
            $store['store_name'], 'index.php?app=store&amp;id=' . $store['store_id'],
            $article['title']
        );

        $this->assign('page_title', $article['title'] . ' - ' . $store['store_name'].' - ');
        $this->display('store.article.html');
    }

    /* 信用评价 */
    function credit()
    {
        /* 店铺信息 */
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
        $this->set_store($id);
        $store = $this->get_store_data();
        $this->assign('store', $store);
        /* 取得评价过的商品 */
        if (!empty($_GET['eval']) && in_array($_GET['eval'], array(1,2,3)))
        {
            $conditions = "AND evaluation = '{$_GET['eval']}'";
        }
        else
        {
            $conditions = "";
            $_GET['eval'] = '';
        }
        $page = $this->_get_page(10);
        $order_goods_mod =& m('ordergoods');
        $goods_list = $order_goods_mod->find(array(
            'conditions' => "seller_id = '$id' AND evaluation_status = 1 AND is_valid = 1 " . $conditions,
            'join'       => 'belongs_to_order',
            'fields'     => 'buyer_id, buyer_name, anonymous, evaluation_time, goods_id, goods_name, specification, price,jifen, quantity, goods_image, evaluation, comment,zhifufangshi',
            'order'      => 'evaluation_time desc',
            'limit'      => $page['limit'],
            'count'      => true,
        ));
        $this->assign('goods_list', $goods_list);
      $kaiguan=$order_goods_mod->kg();
	  $this->assign('kaiguan',$kaiguan);

        $page['item_count'] = $order_goods_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        /* 按时间统计 */
        $stats = array();
        for ($i = 0; $i <= 3; $i++)
        {
            $stats[$i]['in_a_week']        = 0;
            $stats[$i]['in_a_month']       = 0;
            $stats[$i]['in_six_month']     = 0;
            $stats[$i]['six_month_before'] = 0;
            $stats[$i]['total']            = 0;
        }

        $goods_list = $order_goods_mod->find(array(
            'conditions' => "seller_id = '$id' AND evaluation_status = 1 AND is_valid = 1 ",
            'join'       => 'belongs_to_order',
            'fields'     => 'evaluation_time, evaluation',
        ));
        foreach ($goods_list as $goods)
        {
            $eval = $goods['evaluation'];
            $stats[$eval]['total']++;
            $stats[0]['total']++;

            $days = (gmtime() - $goods['evaluation_time']) / (24 * 3600);
            if ($days <= 7)
            {
                $stats[$eval]['in_a_week']++;
                $stats[0]['in_a_week']++;
            }
            if ($days <= 30)
            {
                $stats[$eval]['in_a_month']++;
                $stats[0]['in_a_month']++;
            }
            if ($days <= 180)
            {
                $stats[$eval]['in_six_month']++;
                $stats[0]['in_six_month']++;
            }
            if ($days > 180)
            {
                $stats[$eval]['six_month_before']++;
                $stats[0]['six_month_before']++;
            }
        }
        $this->assign('stats', $stats);

       
	    /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store',
            $store['store_name'], 'index.php?app=store&amp;id=' . $store['store_id'],
            LANG::get('credit_evaluation')
        );
		
        $this->assign('page_title', Lang::get('credit_evaluation') . ' - ' . $store['store_name'].' - ');
        $this->display('store.credit.html');
    }

    /* 取得友情链接 */
    function _get_partners($id)
    {
        $partner_mod =& m('partner');
        return $partner_mod->find(array(
            'conditions' => "store_id = '$id'",
            'order' => 'sort_order',
        ));
    }

    /* 取得推荐商品 */
    function _get_recommended_goods($id, $num = 12)
    {
	 $this->_city_mod =& m('city');
	
		$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
        $goods_mod =& bm('goods', array('_store_id' => $id));
        $goods_list = $goods_mod->find(array(
            'conditions' => 'closed = 0 AND if_show = 1 AND recommended = 1 and cityhao='.$city_id,
            'fields'     => 'goods_name, default_image, price,jifen_price,vip_price,store_id',
			'order'      => 'add_time desc',
            'limit'      => $num,
        ));
        foreach ($goods_list as $key => $goods)
        {
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
			$goods_list[$key]['jifen_price']=round($goods['jifen_price'],2);
			$goods_list[$key]['vip_price']=round($goods['vip_price'],2);
			$store_id=$goods['store_id'];
			$region=$goods_mod->getrow("select region_name from ".DB_PREFIX."store where store_id = '$store_id' limit 1");
        	$goods_list[$key]['region']=substr($region['region_name'],5,100);
        }

        return $goods_list;
    }

    function _get_new_groupbuy($id, $num = 12)
    {
        $model_groupbuy =& m('groupbuy');
		$canshu=$model_groupbuy->can();
		$kaiguan=$model_groupbuy->kg();
		$this->assign('kaiguan',$kaiguan);
        $groupbuy_list = $model_groupbuy->find(array(
            'fields'    => 'goods.default_image, this.group_name, this.group_id, this.spec_price, this.end_time,goods.store_id',
            'join'      => 'belong_goods',
            'conditions'=> $model_groupbuy->getRealFields('this.status=1 and this.state=' . GROUP_ON . ' AND this.store_id=' . $id . ' AND end_time>'. gmtime()),
            'order'     => 'group_id DESC',
            'limit'     => $num
        ));
	
        if (empty($groupbuy_list))
        {
            $groupbuy_list = array();
        }
        foreach ($groupbuy_list as $key => $_g)
        {
		
            empty($groupbuy_list[$key]['default_image']) && $groupbuy_list[$key]['default_image'] = Conf::get('default_goods_image');
            $tmp = current(unserialize($_g['spec_price']));
            $groupbuy_list[$key]['price'] = $tmp['price'];
            $groupbuy_list[$key]['lefttime'] = lefttime($_g['end_time']);
			$groupbuy_list[$key]['jifen_price'] = round($tmp['price']*$canshu['jifenxianjin']*(1+$canshu['lv31']),2);
			$groupbuy_list[$key]['vip_price'] = round($tmp['price']*$canshu['jifenxianjin']*(1+$canshu['lv21']),2);
			$store_id=$_g['store_id'];
			$region=$model_groupbuy->getrow("select region_name from ".DB_PREFIX."store where store_id = '$store_id' limit 1");
        	$groupbuy_list[$key]['region']=substr($region['region_name'],5,100);
        }

        return $groupbuy_list;
    }

    /* 取得最新商品 */
    function _get_new_goods($id, $num = 12)
    {
        $goods_mod =& bm('goods', array('_store_id' => $id));
        $goods_list = $goods_mod->find(array(
            'conditions' => "closed = 0 AND if_show = 1",
            'fields'     => 'goods_name, default_image, price,jifen_price,vip_price,store_id',
            'order'      => 'add_time desc',
            'limit'      => $num,
        ));
        foreach ($goods_list as $key => $goods)
        {
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
			$goods_list[$key]['jifen_price']=round($goods['jifen_price'],2);
			$goods_list[$key]['vip_price']=round($goods['vip_price'],2);
			$store_id=$goods['store_id'];
			$region=$goods_mod->getrow("select region_name from ".DB_PREFIX."store where store_id = '$store_id' limit 1");
        	$goods_list[$key]['region']=substr($region['region_name'],5,100);
		}
        return $goods_list;
    }

    /* 搜索到的结果 */
    function _assign_searched_goods($id)
    {
        $goods_mod =& bm('goods', array('_store_id' => $id));
        $search_name = LANG::get('all_goods');

        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'goods_name',
                'name'  => 'keyword',
                'equal' => 'like',
            ),
        ));
        if ($conditions)
        {
            $search_name = sprintf(LANG::get('goods_include'), $_GET['keyword']);
            $sgcate_id   = 0;
        }
        else
        {
            $sgcate_id = empty($_GET['cate_id']) ? 0 : intval($_GET['cate_id']);
        }

        if ($sgcate_id > 0)
        {
            $gcategory_mod =& bm('gcategory', array('_store_id' => $id));
            $sgcate = $gcategory_mod->get_info($sgcate_id);
            $search_name = $sgcate['cate_name'];

            $sgcate_ids = $gcategory_mod->get_descendant_ids($sgcate_id);
        }
        else
        {
            $sgcate_ids = array();
        }

        /* 排序方式 */
        $orders = array(
            'add_time desc' => LANG::get('add_time_desc'),
            'price asc' => LANG::get('price_asc'),
            'price desc' => LANG::get('price_desc'),
        );
        $this->assign('orders', $orders);

        $page = $this->_get_page(16);
        $goods_list = $goods_mod->get_list(array(
            'conditions' => 'closed = 0 AND if_show = 1' . $conditions,
            'count' => true,
            'order' => empty($_GET['order']) || !isset($orders[$_GET['order']]) ? 'add_time desc' : $_GET['order'],
            'limit' => $page['limit'],
        ), $sgcate_ids);
        foreach ($goods_list as $key => $goods)
        {
            empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
			$goods_list[$key]['jifen_price']=round($goods['jifen_price'],2);
			$goods_list[$key]['vip_price']=round($goods['vip_price'],2);
        }
        $this->assign('searched_goods', $goods_list);
        $page['item_count'] = $goods_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->assign('search_name', $search_name);
    }

    /**
     * 取得文章信息
     */
    function _get_article($id)
    {
        $article_mod =& m('article');
        return $article_mod->get_info($id);
    }
	
	function store_views($id)
    {
        $this->store_mod =& m('store');
		$row=$this->store_mod->getrow("select view from ".DB_PREFIX."store where store_id='$id' limit 1");
        $this->store_mod->edit('store_id='.$id, array('view'=>$row['view']+1));
    }
	
	function cnjl()
	{
		$this->kaiguan_mod=& m('kaiguan');
		$kaiguan=$this->kaiguan_mod->kg();
        /* 店铺信息 */
        $_GET['act'] = 'index';
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
		
		
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }
		
        $this->set_store($id);
        $store = $this->get_store_data();
		 $this->assign('def', 1);
        $this->assign('store', $store);
		$this->assign('kaiguan', $kaiguan);
        /* 取得友情链接 */
        $this->assign('partners', $this->_get_partners($id));

        /* 取得推荐商品 */
        $this->assign('recommended_goods', $this->_get_recommended_goods($id));
		
        $this->assign('new_groupbuy', $this->_get_new_groupbuy($id));

        /* 取得最新商品 */
        $this->assign('new_goods', $this->_get_new_goods($id));
		$this->store_views($id);//店铺点击量

        /* 当前位置 */
        $this->_curlocal(LANG::get('all_stores'), 'index.php?app=search&amp;act=store', $store['store_name'] . ' - ' . Lang::get('jianglichengnuo'));
        $this->assign('page_title', $store['store_name'] . ' - ' . Conf::get('jianglichengnuo').' - ');
	
	
		$this->city_mod=& m('city');
		$cityrow=$this->city_mod->get_cityrow();
		$cityid=$cityrow['city_id']; 
		$store_id=$_GET['id'];
		$art=$this->city_mod->getRow("select content from ".DB_PREFIX."article_user where user_id='$store_id' limit 1");
		
		if(empty($art['content']))
		{
			$art['content']=Lang::get('gaidianjia');
		}
		$this->assign('art',$art);
		$this->display('storejl.html');
	}
	
	
}

?>
