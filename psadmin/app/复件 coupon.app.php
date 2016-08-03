<?php

/**
 *    商品分享管理控制器
 *
 *    @author    Hyber
 *    @usage    none
 */
class CouponApp extends BackendApp
{
    var $_m_share;

    function __construct()
    {
        $this->CouponApp();
    }

    function CouponApp()
    {
        parent::BackendApp();

        $this->coupon_mod =& m('coupon');
		$this->_user_mod =& m('member');
		$this->youhuiquan_mod =& m('youhuiquan');
    }

    /**
     *    商品分享索引
     *
     *    @author    Hyber
     *    @return    void
     */
    function index()
    {
       $row_member=$this->_user_mod->getrow("select * from ".DB_PREFIX."member where user_name = '$user'");
	$city=$row_member['city'];
	/*echo $city;*/
	 $page = $this->_get_page();
	  $sort  = 'coupon_id';
      $order = 'desc';
	/*	if($city==0)
		{*/
        $users = $this->coupon_mod->find(array(
           // 'join' => 'has_store,manage_mall',
            //'fields' => 'this.*,store.store_id,userpriv.store_id as priv_store_id,userpriv.privs',
            /*'conditions' => '1=1' . $conditions .'and city='.$city,*/
			//'conditions' => '1=1 '.$conditions,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
	/*	}*/
	/*	else
		{
		
		$users = $this->_user_mod->find(array(
            'join' => 'has_store,manage_mall',
            'fields' => 'this.*,store.store_id,userpriv.store_id as priv_store_id,userpriv.privs',
          
			'conditions' => '1=1 '.$conditions .'and member.city='.$city,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));*/
		  $this->assign('users', $users);
        $page['item_count'] = $this->coupon_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		 $this->display('coupon.index.html');

    }

    function add()
    {
	
	$user=$this->visitor->get('user_name');
	$youhui=$this->_user_mod->getAll("select city from ".DB_PREFIX."member where user_name = '$user'");
	
	  $this->assign('youhui', $youhui);
	
	
        if (!IS_POST)
        {
          
            $this->display('coupon.form.html');
        }
        else
        {
            $data = array(
                'youhui_name' => trim($_POST['youhui_name']),
				'youhui_image'  => trim($_POST['youhui_image']),
                'youhui_jine'  => trim($_POST['youhui_jine']),
                'start_time'  => trim($_POST['start_time']),
				'end_time'  => trim($_POST['end_time']),
                'beizhu'  => trim($_POST['beizhu']),
            );

              //$this->youhuiquan_mod->add($data);

             if (!$youhui_id = $this->youhuiquan_mod->add($data))  //获取brand_id
            {
                $this->show_warning($this->youhuiquan_mod->get_error());

                return;
            }


            /* 处理上传的图片 */
            $logo       =   $this->_upload_logo($youhui_id);
            if ($logo === false)
            {
                return;
            }
            
            $logo && $this->youhuiquan_mod->edit($youhui_id, array('youhui_image' => $logo)); 

            //$this->_clear_cache();
            $this->show_message('add_successed',
                'back_list',    'index.php?app=coupon&act=fufei',
                'continue_add', 'index.php?app=coupon&amp;act=add'
            );
        }
    }
 function _upload_logo($youhui_id)
    {
        $file = $_FILES['youhui_image'];
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return '';
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES['youhui_image']);//上传logo
        if (!$uploader->file_info())
        {
            $this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=coupon&amp;act=edit&amp;id=' . $youhui_id);
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/mall/coupon', $youhui_id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
            return $file_path;
        }
        else
        {
            return false;
        }
    }


 function fufei()
    {
      
	/*echo $city;*/
	 $page = $this->_get_page();
	  $sort  = 'youhui_id';
      $order = 'desc';
	/*	if($city==0)
		{*/
        $users = $this->youhuiquan_mod->find(array(
           // 'join' => 'has_store,manage_mall',
            //'fields' => 'this.*,store.store_id,userpriv.store_id as priv_store_id,userpriv.privs',
            /*'conditions' => '1=1' . $conditions .'and city='.$city,*/
			//'conditions' => '1=1 '.$conditions,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
	/*	}*/
	/*	else
		{
		
		$users = $this->_user_mod->find(array(
            'join' => 'has_store,manage_mall',
            'fields' => 'this.*,store.store_id,userpriv.store_id as priv_store_id,userpriv.privs',
          
			'conditions' => '1=1 '.$conditions .'and member.city='.$city,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));*/
		  $this->assign('users', $users);
        $page['item_count'] = $this->youhuiquan_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
		 $this->display('youhui.index.html');

    }




    function edit()
    {
        $youhui_id = empty($_GET['id']) ? 0 : $_GET['id'];
      echo $youhui_id;
        if (!$youhui_id)
        {
            $this->show_warning('no_youhuiquan');
            return;
        }
         if (!IS_POST)
        {
            $find_data     = $this->youhuiquan_mod->find($youhui_id);
            if (empty($find_data))
            {
                $this->show_warning('no_youhuiquan');

                return;
            }
            $youhui    =   current($find_data);
            if ($youhui['youhui_image'])
            {
                $youhui['youhui_image']  =   dirname(site_url()) . "/" . $youhui['youhui_image'];
            }
            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('yes_or_no', $yes_or_no);
            $this->assign('youhui', $youhui);
            $this->display('youhui.form.html');
        }
        else
        {
            $data = array();
            $data['youhui_name']     =   $_POST['youhui_name'];
            $data['youhui_jine']     =   $_POST['youhui_jine'];
            $data['start_time']    =   $_POST['start_time'];
            $data['end_time'] = $_POST['end_time'];
			$data['goumai']    =   $_POST['goumai'];
            $youhui_image               =   $this->_upload_logo($youhui_id);
            $youhui_image && $data['youhui_image'] = $youhui_image;
            if ($youhui_image === false)
            {
                return;
            }
           
            $rows=$this->youhuiquan_mod->edit($youhui_id, $data);
            if ($this->youhuiquan_mod->has_error())
            {
                $this->show_warning($this->youhuiquan_mod->get_error());

                return;
            }

            $this->show_message('edit_successed',
                'back_list',        'index.php?app=coupon',
                'edit_again',    'index.php?app=coupon&amp;act=edit&amp;id=' . $youhui_id);
        }
    }

    function drop()
    {
        $youhui_ids = isset($_GET['id']) ? trim($_GET['id']) : 0;
        if (!$youhui_ids)
        {
            $this->show_warning('no_such_navigation');

            return;
        }
        $youhui_ids=explode(',',$youhui_ids);
        if (!$this->youhuiquan_mod->drop($youhui_ids))    //删除
        {
            $this->show_warning($this->youhuiquan_mod->get_error());

            return;
        }

        $this->show_message('drop_successed');
    }

    //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = $this->_m_share->getAll();

       if (in_array($column ,array('title', 'sort_order')))
       {
           $data[$id][$column] = $value;
           if($this->_m_share->setAll($data))
           {
               $this->_clear_cache();
               echo ecm_json_encode(true);
           }
       }
       else
       {
           return ;
       }
       return ;
   }

    function _get_share_type()
    {
        return array(
            'share'   => Lang::get('share'),
            'collect' => Lang::get('collect'),
        );
    }

       /**
     *    处理上传标志
     *
     *    @author    Hyber
     *    @param     int $brand_id
     *    @return    string
     */
    
}

?>
