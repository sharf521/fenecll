<?php

class BrandApp extends MallbaseApp
{
    function index()
    {
	//$url=$_SERVER['HTTP_HOST'];//获得当前网址
	 /*echo $url;*/
	 $this->_city_mod =& m('city');
	 //$row_city=$this->_city_mod->getrow("select city_id from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];

        //导航
        $this->assign('navs', $this->_get_navs());
        $_curlocal = array(
                array(
                'text' => Lang::get('index'),
                'url'  => 'index.php',),
                array(
                'text' => Lang::get('all_brands'),
                'url'  => '',),
            );
	
        $this->assign('_curlocal', $_curlocal);
        $recommended_stores = $this->_recommended_stores(5);
        $this->assign('recommended_stores', $recommended_stores);
        $recommended_brands = $this->_recommended_brands(10);
        $this->assign('recommended_brands', $recommended_brands);
        //对品牌重新组合排序
        $brand_mod =& m('brand');
        $brands_tmp = $brand_mod->find(array(
            'order' => "tag DESC,sort_order asc",
			'conditions' => 'city='.$city_id,
			
			));
        $brands_tmp = array_values($brands_tmp);
        $brands = array();
        $i = 0;
        foreach ($brands_tmp as $key => $val)
        {
            if (empty($key))
            {
               $brands[$i]['tag'] = $val['tag'];
               $brands[$i]['count'] = 1;
               $brands[$i]['brands'][] = $val;
               $i++;
            }
            else
            {
                if ($val['tag'] == $brands[$i-1]['tag'])
                {
                    $brands[$i-1]['count'] = $brands[$i-1]['count'] + 1;
                    $brands[$i-1]['brands'][] = $val;
                }
                else
                {
                    $brands[$i]['tag'] = $val['tag'];
                    $brands[$i]['count'] = 1;
                    $brands[$i]['brands'][] = $val;
                    $i++;
                }
            }
        }
        $brands_sort = array();
        foreach ($brands as $key => $val)
        {
            $brands_sort[$key] = $val['count'];
        }
        arsort($brands_sort);
        foreach ($brands_sort as $key => $val)
        {
            $brands_sort[$key] = $brands[$key];
        }
        $this->assign('brands', $brands_sort);
        $this->assign('page_title', Lang::get('all_brands').' - ');
        $this->display('brand.index.html');
    }

    function _recommended_brands($num)
    {
	//$url=$_SERVER['HTTP_HOST'];
	/*	echo $url;*/
	   $this->_city_mod =& m('city');
	 //$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
        $brand_mod =& m('brand');
        $brands = $brand_mod->find(array(
            'conditions' => 'recommended = 1 AND if_show = 1 and city='.$city_id,
            'order' => 'sort_order',
            'limit' => '0,' . $num));
        return $brands;
    }

    function _recommended_stores($num)
    {
	//$url=$_SERVER['HTTP_HOST'];
	
	   $this->_city_mod =& m('city');
	 //$row_city=$this->_city_mod->getrow("select * from ".DB_PREFIX."city where city_yuming = '$url'");
	//$city_id=$row_city['city_id'];
	$cityrow=$this->_city_mod->get_cityrow();
		$city_id=$cityrow['city_id'];
        $store_mod =& m('store');
        $goods_mod =& m('goods');
        $stores = $store_mod->find(array(
            'conditions'    => 'recommended=1 AND state = 1 and cityid='.$city_id,
            'order'         => 'sort_order',
            'join'          => 'belongs_to_user',
            'limit'         => '0,' . $num,
        ));
        foreach ($stores as $key => $store){
            empty($store['store_logo']) && $stores[$key]['store_logo'] = Conf::get('default_store_logo');
            $stores[$key]['goods_count'] = $goods_mod->get_count_of_store($store['store_id']);
        }
        return $stores;
    }
}

?>
