<?php

/* 管理员控制器 */

class GonghuoApp extends BackendApp
{


    function __construct()
    {
        $this->GonghuoApp();
    }

    function GonghuoApp()
    {
        parent::__construct();

        $this->city_mod = &m('city');
        $this->member_mod = &m('member');
        $this->kaiguan_mod =& m('kaiguan');
        $this->gonghuo_mod =& m('gonghuo');
        $this->userpriv_mod =& m('userpriv');
        $this->message_mod =& m('message');
        $this->gonghuo_xinxi_mod =& m('gonghuo_xinxi');
    }

    function index()
    {

        $userid = $this->visitor->get('user_id');
        $priv_row = $this->userpriv_mod->getrow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0");
        $privs = $priv_row['privs'];
        $city = $priv_row['city'];

        $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style' => 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $page = $this->_get_page();
        $sort = 'gh_id';
        $order = 'desc';

        $conditions = " and 1=1 ";
        $ghname = $_GET['ghname'];
        $this->assign('ghname', $ghname);
        $type = $_GET['type'];
        $this->assign('type', $type);
        $add_time_from = $_GET['add_time_from'];
        $add_time_to = $_GET['add_time_to'];
        $this->assign('add_time_from', $add_time_from);
        $this->assign('add_time_to', $add_time_to);
        if (!empty($ghname)) {
            $conditions .= " and user_name like '%$ghname%' ";
        }
        if (!empty($add_time_from)) {
            $conditions .= " and riqi>='$add_time_from' ";
        }
        if (!empty($add_time_to)) {
            $conditions .= " and riqi<='$add_time_to 24:59:59' ";
        }

        if (!empty($type)) {
            if ($type == 8)//等待初审
            {
                $conditions .= " and chushen_status=3 and status=3 ";
            } elseif ($type == 9)//等待事实审核
            {
                $conditions .= " and chushen_status!=3 and status=3 ";
            } else {
                $conditions .= " and chushen_status!=3 and status='$type' ";
            }
        }

        if ($privs == "all") {
            $gonghuo = $this->gonghuo_mod->find(array(
                'conditions' => '1=1 ' . $conditions,
                'limit' => $page['limit'],
                'order' => "$sort $order",
                'count' => true,
            ));
        } else {
            $gonghuo = $this->gonghuo_mod->find(array(
                'conditions' => 'gh_city=' . $city . $conditions,
                'limit' => $page['limit'],
                'order' => "$sort $order",
                'count' => true,
            ));
        }
        $city_row = array();
        $result = $this->gonghuo_mod->getAll("select * from " . DB_PREFIX . "city");
        foreach ($result as $var) {
            $row = explode('-', $var['city_name']);
            $city_row[$var['city_id']] = $row[0];
        }
        $result = null;
        foreach ($gonghuo as $key => $val) {
            $gonghuo[$key]['city_name'] = $city_row[$val['gh_city']];

        }


        $page['item_count'] = $this->gonghuo_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions ? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        $this->assign('gonghuo', $gonghuo);
        $this->assign('priv_row', $priv_row);
        $this->display('gonghuo.index.html');

    }

    function ghwei_shenhe()
    {

        $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style' => 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $user = $this->visitor->get('user_name');
        $row_member = $this->member_mod->getrow("select * from " . DB_PREFIX . "member where user_name = '$user'");
        //$city=$row_member['city'];
        $userid = $this->visitor->get('user_id');
        $priv_row = $this->userpriv_mod->getrow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0");
        $privs = $priv_row['privs'];
        $city = $priv_row['city'];
        $page = $this->_get_page();
        $deng = Lang::get('dengdaishenhe');

        $conditions = " and 1=1 ";
        $ghname = $_GET['ghname'];
        $this->assign('ghname', $ghname);
        $type = $_GET['type'];
        $this->assign('type', $type);
        $add_time_from = $_GET['add_time_from'];
        $add_time_to = $_GET['add_time_to'];
        $this->assign('add_time_from', $add_time_from);
        $this->assign('add_time_to', $add_time_to);
        if (!empty($ghname)) {
            $conditions .= " and user_name like '%$ghname%' ";
        }
        if (!empty($add_time_from)) {
            $conditions .= " and riqi>='$add_time_from' ";
        }
        if (!empty($add_time_to)) {
            $conditions .= " and riqi<='$add_time_to 24:59:59' ";
        }

        if (!empty($type)) {
            if ($type == 8)//等待初审
            {
                $conditions .= " and chushen_status=3 and status=3 ";
            } elseif ($type == 9)//等待事实审核
            {
                $conditions .= " and chushen_status!=3 and status=3 ";
            } else {
                $conditions .= " and chushen_status!=3 and status='$type' ";
            }
        }

        if ($privs == "all") {
            $gonghuo = $this->gonghuo_mod->find(array(
                'conditions' => "(status=3 or status=4 or status=5 or status=6 or status=7) " . $conditions,
                'limit' => $page['limit'],
                'order' => 'gh_id DESC',
                'count' => true
            ));
        } else {
            $gonghuo = $this->gonghuo_mod->find(array(
                'conditions' => "(status=3  or status=4 or status=5 or status=6 or status=7) and gh_city='$city'" . $conditions,
                'limit' => $page['limit'],
                'order' => 'gh_id DESC',
                'count' => true
            ));
        }

        $city_row = array();
        $result = $this->gonghuo_mod->getAll("select * from " . DB_PREFIX . "city");
        foreach ($result as $var) {
            $row = explode('-', $var['city_name']);
            $city_row[$var['city_id']] = $row[0];
        }
        $result = null;
        foreach ($gonghuo as $key => $val) {
            $gonghuo[$key]['city_name'] = $city_row[$val['gh_city']];
        }
        $page['item_count'] = $this->gonghuo_mod->getCount();
        $this->_format_page($page);
        $this->assign('priv_row', $priv_row);
        $this->assign('page_info', $page);
        $this->assign('gonghuo', $gonghuo);//传递到风格里
        $this->display('ghwei_shenhe.html');
        return;

    }

    function gh_shenhe()
    {
        $userid = $this->visitor->get('user_id');
        $username = $this->visitor->get('user_name');
        $priv_row = $this->userpriv_mod->getRow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0 limit 1");
        $city = $priv_row['city'];
        $gh_id = empty($_GET['id']) ? null : trim($_GET['id']);
        $user_id = empty($_GET['user_id']) ? null : trim($_GET['user_id']);

        $gonghuo = $this->gonghuo_mod->getRow("select * from " . DB_PREFIX . "gonghuo where gh_id = '$gh_id' limit 1");

        $store_id = $gonghuo['cangkuid'];
        $shipping_id = $gonghuo['shipping_id'];
        $stor = $this->userpriv_mod->getRow("select store_name from " . DB_PREFIX . "store where store_id = '$store_id' limit 1");
        $this->assign('stor', $stor);
        $this->assign('gonghuo', $gonghuo);
        $xinxi = $this->member_mod->getRow("select * from " . DB_PREFIX . "gonghuo_xinxi where user_id = '$user_id' limit 1");
        $this->assign('xinxi', $xinxi);

        $ship = $this->member_mod->getRow("select shipping_name from " . DB_PREFIX . "shippings where shipping_id = '$shipping_id' limit 1");
        $this->assign('ship', $ship);

        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
        $this->assign('build_editor', $this->_build_editor(array('name' => 'beizhu')));


        if ($_POST) {


            $chushen_status = trim($_POST['chushen_status']);
            $chushen_beizhu = trim($_POST['chushen_beizhu']);
            $cangkuid = trim($_POST['cangkuid']);
            $status = trim($_POST['status']);
            $beizhu = trim($_POST['beizhu']);
            $zhongshen_beizhu = trim($_POST['zhongshen_beizhu']);
            if (empty($chushen_status)) {
                $this->show_warning(bianma('请选择初审状态'));
                return;
            }

            $new_gh = array(
                'chushen_status' => $chushen_status,
                'chushen_beizhu' => $chushen_beizhu,
                'cangkuid' => $cangkuid,
                'status' => $status,
                'beizhu' => $beizhu,
                'zhongshen_beizhu' => $zhongshen_beizhu
            );
            if ($status == 1 or $status == 2) {
                $new_gh['zong_kucun'] = $_POST['zong_kucun'];
                $new_gh['baojingshu'] = $_POST['baojingshu'];
                $new_gh['shengyukucun'] = $_POST['shengyukucun'];
            }

            $goodsname = iconv('gb2312', 'utf-8', $gonghuo['goods_name']);

            if ($priv_row['privs'] != 'all') {
                unset($new_gh['status']);
                unset($new_gh['beizhu']);
                if ($chushen_status == 1)
                    $remark = "初审通过\'[$goodsname][$gh_id]\'";
                if ($chushen_status == 2)
                    $remark = "初审不通过\'[$goodsname][$gh_id]\'";
                addlog($userid, $username, bianma($remark), $type);
            } elseif ($status == 1 or $status == 2) {
                if ($status == 1)
                    $remark = "终审通过\'[$goodsname][$gh_id]\'";

                if ($status == 2)
                    $remark = "终审不通过\'[$goodsname][$gh_id]\'";
                $suoshu_city = $_POST['suoshu_city'];
                if ($suoshu_city == 1) {
                    $str = ',' . implode(', ', $_POST[fu]) . ',';
                    $new_gh['suoshu_city'] = $str;
                } else {
                    /*$str=  implode( ', ',   $_POST[qu]).',';
                    $new_gh['suoshu_city']=$str;*/
                    $new_gh['suoshu_city'] = $suoshu_city;
                }
                addlog($userid, $username, bianma($remark), $type);
            }

            $this->gonghuo_mod->edit('gh_id=' . $gh_id, $new_gh);

            if ($status == 1) {
                $beizhu1 = Lang::get('gonghuoshang');
                $beizhu1 = str_replace('{1}', $xinxi['user_name'], $beizhu1);
            }
            if ($status == 2) {
                $beizhu1 = Lang::get('gonghuobu');
                $beizhu1 = str_replace('{1}', $xinxi['user_name'], $beizhu1);
            }
            if ($status == 1 or $status == 2) {
                $add_notice = array(
                    'from_id' => 0,
                    'to_id' => $user_id,
                    'content' => $beizhu1,
                    'add_time' => gmtime(),
                    'last_update' => gmtime(),
                    'new' => 1,
                    'parent_id' => 0,
                    'status' => 3,
                );
                $this->message_mod->add($add_notice);
            }
            $this->show_message('shenhechenggong',
                'fanhui', 'index.php?app=gonghuo');
        } else {
            $cangku = $this->member_mod->getAll("select * from " . DB_PREFIX . "store where is_cangku=1");
            $city_row = $this->member_mod->getAll("select * from " . DB_PREFIX . "city ");
            $this->assign('cangku', $cangku);
            $this->assign('city_row', $city_row);
            $this->assign('log', $logs_data);
            $this->assign('priv_row', $priv_row);
            $this->assign('user', $user_data);
            $this->display('gh_shenhe.html');
            return;
        }
    }

    function gh_edit()
    {

        $userid = $this->visitor->get('user_id');
        $priv_row = $this->gonghuo_mod->getrow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0");
        $gh_id = empty($_GET['id']) ? 0 : $_GET['id'];
        $pag = empty($_GET['page']) ? 0 : $_GET['page'];
        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
        $this->assign('build_editor', $this->_build_editor(array('name' => 'beizhu')));
        if (!IS_POST) {
            $find_data = $this->gonghuo_mod->find($gh_id);

            $gonghuo = current($find_data);
            $user_id = $gonghuo['user_id'];
            $xinxi = $this->userpriv_mod->getrow("select * from " . DB_PREFIX . "gonghuo_xinxi where user_id = '$user_id'");
            $city = $this->gonghuo_mod->getAll("select * from " . DB_PREFIX . "city");
            $cangku = $this->gonghuo_mod->getAll("select * from " . DB_PREFIX . "store where is_cangku=1");
            /* 显示新增表单 */
            $yes_or_no = array(
                1 => Lang::get('yes'),
                0 => Lang::get('no'),
            );
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));

            $str = '';
            //$shu='|';
            $huanhang = '<br>';
            /*if($gonghuo['suoshu_city']!='all')
            {*/
            $isxian = substr($gonghuo['suoshu_city'], 1);
            //$newstr = substr($isxian,0,strlen($isxian)-1);
            $yh = explode(',', $isxian);
            foreach ($city as $val) {
                $che = '';
                if (in_array($val['city_id'], $yh)) //if(strpos($youhui['yhcity'],$val['city_id']))
                {
                    $che = 'checked';
                }
                $str .= '<input type="checkbox" name="fu[]" value="' . $val['city_id'] . '" ' . $che . '/>' . $val['city_name'] . $huanhang;
            }

            /*}
            else
            {
                foreach ($city as $val)
                {
                    $che='';
                    $che='checked';
                    $str.='<input type="checkbox" name="fu[]" value="'.$shu.''.$val['city_id'].'" '.$che.'/>'.$val['city_name'].$huanhang;
                }
            }*/
            $this->assign('cangku', $cangku);
            $this->assign('city', $str);
            $this->assign('yes_or_no', $yes_or_no);
            $this->assign('priv_row', $priv_row);
            $this->assign('xinxi', $xinxi);
            $this->assign('gonghuo', $gonghuo);
            $this->display('gh_edit.html');
        } else {

            $data = array();

            $suoshu_city = $_POST['suoshu_city'];
            if ($suoshu_city == 1) {
                $str = ',' . implode(', ', $_POST[fu]) . ',';
                $data['suoshu_city'] = $str;
            } else {
                $data['suoshu_city'] = $suoshu_city;
            }
            $data['goods_name'] = $_POST['goods_name'];
            $data['goods_brand'] = $_POST['goods_brand'];
            $data['tujing'] = $_POST['tujing'];
            //$data['cankao_price']    =   $_POST['cankao_price'];
            $data['lingshou_price'] = $_POST['lingshou_price'];
            //$data['pifa_price']    =   $_POST['pifa_price'];
            //$data['yuji_price']    =   $_POST['yuji_price'];
            $data['source'] = $_POST['source'];
            $data['status'] = $_POST['status'];
            $data['beizhu'] = $_POST['beizhu'];
            $data['cangkuid'] = $_POST['cangkuid'];
            $data['zong_kucun'] = $_POST['zong_kucun'];
            $data['baojingshu'] = $_POST['baojingshu'];
            $data['yu_kucun'] = $_POST['yu_kucun'];
            $data['shengyukucun'] = $_POST['shengyukucun'];
            $data['zhongshen_beizhu'] = $_POST['zhongshen_beizhu'];
            //$ziliao               =   $this->_upload_logo($gh_id);
            $logo = $this->_upload_logo($gh_id, 'ziliao');
            $logo1 = $this->_upload_logo($gh_id, 'chanpin');

            /* $city_logo && $data['city_logo'] = $city_logo;*/
            /*if ($city_logo === false)
            {
                return;
            }*/
            $logo && $this->gonghuo_mod->edit($gh_id, array('ziliao' => $logo));
            $logo1 && $this->gonghuo_mod->edit($gh_id, array('chanpin' => $logo1));


            $rows = $this->gonghuo_mod->edit($gh_id, $data);
            if ($this->gonghuo_mod->has_error()) {
                $this->show_warning($this->gonghuo_mod->get_error());

                return;
            }

            $this->show_message('edit_successed',
                'back_list', 'index.php?app=gonghuo&page= ' . $pag,
                'edit_again', 'index.php?app=gonghuo&amp;act=gh_edit&amp;id=' . $gh_id);
        }
    }

    function _upload_logo($gh_id, $can)
    {
        $file = $_FILES[$can];
        $riqi = time() . rand(100, 999);
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return '';
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES[$can]);//上传logo

        if (!$uploader->file_info()) {
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=my_theme&amp;act=gonghuo');
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/mall/gonghuo', $riqi . $gh_id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
            return $file_path;
        } else {
            return false;
        }
    }

    function gonghuo_delete()
    {
        $gh_id = intval($_GET['id']);//供货id
        $sql = "delete from " . DB_PREFIX . "gonghuo where gh_id = '$gh_id'";
        $this->gonghuo_mod->db->query($sql);
        $this->show_message('delete', 'back_list', 'index.php?app=gonghuo');
    }

    function gh_xiangqing()
    {
        $userid = $this->visitor->get('user_id');
        $priv_row = $this->gonghuo_mod->getrow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0");

        $gh_id = empty($_GET['id']) ? 0 : $_GET['id'];
        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
        // $this->assign('build_editor', $this->_build_editor(array('name' => 'beizhu')));
        $find_data = $this->gonghuo_mod->find($gh_id);
        /*if (empty($find_data))
        {
            $this->show_warning('no_youhuiquan');

            return;
        }*/
        $gonghuo = current($find_data);
        $user_id = $gonghuo['user_id'];
        $xinxi = $this->userpriv_mod->getrow("select * from " . DB_PREFIX . "gonghuo_xinxi where user_id = '$user_id'");


        /* 显示新增表单 */
        $yes_or_no = array(
            1 => Lang::get('yes'),
            0 => Lang::get('no'),
        );
        $this->import_resource(array(
            'script' => 'jquery.plugins/jquery.validate.js'
        ));
        $this->assign('yes_or_no', $yes_or_no);
        $this->assign('gonghuo', $gonghuo);
        $this->assign('xinxi', $xinxi);
        $this->assign('priv_row', $priv_row);
        $this->display('gh_xiangqing.html');


    }


    function ghsq()
    {
        $this->member_mod =& m('member');
        $user = $this->visitor->get('user_name');
        $userid = $this->visitor->get('user_id');
        $priv_row = $this->userpriv_mod->getrow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0");
        $privs = $priv_row['privs'];
        $city = $priv_row['city'];
        $page = $this->_get_page();
        if ($privs == "all") {
            $memb = $this->gonghuo_xinxi_mod->find(array(
                'conditions' => 'status=2 or status=3',
                'limit' => $page['limit'],
                'order' => 'gh_id DESC',
                'count' => true
            ));
        } else {
            $memb = $this->gonghuo_xinxi_mod->find(array(
                'conditions' => "(status=2 or status=3) and city='$city'",
                'limit' => $page['limit'],
                'order' => 'gh_id DESC',
                'count' => true
            ));
        }
        $city_row = array();
        $result = $this->member_mod->getAll("select * from " . DB_PREFIX . "city");
        foreach ($result as $var) {
            $row = explode('-', $var['city_name']);
            $city_row[$var['city_id']] = $row[0];
        }
        $result = null;
        foreach ($memb as $key => $val) {
            $memb[$key]['city_name'] = $city_row[$val['city']];
        }
        $page['item_count'] = $this->gonghuo_xinxi_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('memb', $memb);//传递到风格里
        $this->display('ghsq.html');
        return;

    }

    function sq_weishenhe()
    {
        $this->member_mod =& m('member');
        $this->gonghuo_xinxi_mod =& m('gonghuo_xinxi');
        $user = $this->visitor->get('user_name');
        $userid = $this->visitor->get('user_id');
        $priv_row = $this->userpriv_mod->getrow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0");
        $privs = $priv_row['privs'];
        $city = $priv_row['city'];
        $page = $this->_get_page();
        if ($privs == "all") {
            $memb = $this->gonghuo_xinxi_mod->find(array(
                'conditions' => 'status=1',
                'limit' => $page['limit'],
                'order' => 'gh_id DESC',
                'count' => true
            ));
        } else {
            $memb = $this->gonghuo_xinxi_mod->find(array(
                'conditions' => "status=1 and city='$city'",
                'limit' => $page['limit'],
                'order' => 'gh_id DESC',
                'count' => true
            ));
        }
        $city_row = array();
        $result = $this->member_mod->getAll("select * from " . DB_PREFIX . "city");
        foreach ($result as $var) {
            $row = explode('-', $var['city_name']);
            $city_row[$var['city_id']] = $row[0];
        }
        $result = null;
        foreach ($memb as $key => $val) {
            $memb[$key]['city_name'] = $city_row[$val['city']];
        }
        $page['item_count'] = $this->gonghuo_xinxi_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('memb', $memb);//传递到风格里
        $this->display('ghsq_weishenhe.html');
        return;
    }

    function ghsq_shenhe()
    {
        $this->message_mod =& m('message');
        $id = empty($_REQUEST['id']) ? null : trim($_REQUEST['id']);
        $find_data = $this->gonghuo_xinxi_mod->find($id);
        $memb = current($find_data);
        $this->assign('memb', $memb);
        $status = trim($_POST['status']);

        $user_id = trim($_POST['user_id']);
        $beizhu = trim($_POST['beizhu']);
        if ($_POST) {


            if ($status == 2) {
                $beizhu = Lang::get('gonghuoshenhe');
                $beizhu = str_replace('{1}', $user_name, $beizhu);
            }
            if ($status == 3) {
                $beizhu = Lang::get('gonghuoweishenhe');
                $beizhu = str_replace('{1}', $user_name, $beizhu);
            }
            $add_notice = array(
                'from_id' => 0,
                'to_id' => $user_id,
                'content' => $beizhu,
                'add_time' => gmtime(),
                'last_update' => gmtime(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3,
            );

            $this->message_mod->add($add_notice);

            $this->gonghuo_xinxi_mod->edit('gh_id=' . $id, array('status' => $status));

            $this->show_message('shenhechenggong',
                'fanhui', 'index.php?app=gonghuo&act=ghsq');
        } else {
            $this->display('ghsq_shenhe.html');
            return;
        }
    }

    function ghsq_edit()
    {
        $gh_id = empty($_GET['id']) ? null : trim($_GET['id']);

        $find_data = $this->gonghuo_xinxi_mod->find($gh_id);
        $memb = current($find_data);
        $this->assign('memb', $memb);
        $status = trim($_POST['status']);
        $user_name = trim($_POST['user_name']);
        $user_id = trim($_POST['user_id']);
        $beizhu = trim($_POST['beizhu']);

        if (!IS_POST) {
            $find_data = $this->gonghuo_xinxi_mod->find($gh_id);
            if (empty($find_data)) {
                $this->show_warning('no_gonghuo');

                return;
            }
            $memb = current($find_data);


            $this->assign('memb', $memb);
            $this->display('ghsq_shenhe.html');
            return;
        } else {

            $data = array();
            $data['name'] = $_POST['name'];
            $data['sex'] = $_POST['sex'];
            $data['age'] = $_POST['age'];
            $data['wangdian'] = $_POST['wangdian'];
            $data['fenxiao'] = $_POST['fenxiao'];
            $data['lxfs'] = $_POST['lxfs'];
            $data['gongsi_name'] = $_POST['gongsi_name'];
            $data['address'] = $_POST['address'];
            $data['lianxiren'] = $_POST['lianxiren'];
            $data['method'] = $_POST['method'];
            $data['status'] = $_POST['status'];
            $data['beizhu'] = $_POST['beizhu'];

            $logo = $this->_upload_logo($gh_id, 'changjia');
            $logo1 = $this->_upload_logo($gh_id, 'zhizhao');
            $logo2 = $this->_upload_logo($gh_id, 'jigou');
            $logo3 = $this->_upload_logo($gh_id, 'shuiwu');
            $logo4 = $this->_upload_logo($gh_id, 'qita_1');
            $logo5 = $this->_upload_logo($gh_id, 'qita_2');
            $logo6 = $this->_upload_logo($gh_id, 'qita_3');


            $logo && $this->gonghuo_xinxi_mod->edit($gh_id, array('changjia' => $logo));
            $logo1 && $this->gonghuo_xinxi_mod->edit($gh_id, array('zhizhao' => $logo1));
            $logo2 && $this->gonghuo_xinxi_mod->edit($gh_id, array('jigou' => $logo2));
            $logo3 && $this->gonghuo_xinxi_mod->edit($gh_id, array('shuiwu' => $logo3));
            $logo4 && $this->gonghuo_xinxi_mod->edit($gh_id, array('qita_1' => $logo4));
            $logo5 && $this->gonghuo_xinxi_mod->edit($gh_id, array('qita_2' => $logo5));
            $logo6 && $this->gonghuo_xinxi_mod->edit($gh_id, array('qita_3' => $logo6));

            $rows = $this->gonghuo_xinxi_mod->edit($gh_id, $data);


            $add_notice = array(
                'from_id' => 0,
                'to_id' => $user_id,
                'content' => $beizhu,
                'add_time' => gmtime(),
                'last_update' => gmtime(),
                'new' => 1,
                'parent_id' => 0,
                'status' => 3,
            );
            $this->message_mod->add($add_notice);


            if ($this->gonghuo_xinxi_mod->has_error()) {
                $this->show_warning($this->gonghuo_xinxi_mod->get_error());

                return;
            }
            $this->show_message('edit_successed',
                'fanhui', 'index.php?app=gonghuo&act=ghsq');

        }
    }

    function baosunlog()
    {
        $user_id = $this->visitor->get('user_id');
        $this->gonghuo_mod =& m('gonghuo');
        $this->baosun_mod =& m('baosun');
        if ($_POST) {
            $ghid = $_POST['gh_id'];
            $num = $_POST['num'];
            $type = $_POST['type'];
            $gh = $this->gonghuo_mod->getRow("select shengyukucun from " . DB_PREFIX . "gonghuo where gh_id='$ghid'");
            $shengyukucun = $gh['shengyukucun'];
            $arr = array(
                'cangkuid' => $_POST['cangkuid'],
                'gh_name' => $_POST['gh_name'],
                'goods_name' => $_POST['goods_name'],
                'gh_id' => $_POST['gh_id'],
                'type' => $_POST['type'],
                'num' => $_POST['num'],
                'beizhu' => $_POST['beizhu'],
                'riqi' => date('Y-m-d H:i:s'),
            );
            if ($type == 2)
                $yukucun = $shengyukucun - $num;
            if ($type == 1)
                $yukucun = $shengyukucun + $num;
            $this->gonghuo_mod->edit('gh_id=' . $ghid, array('shengyukucun' => $yukucun));
            $this->baosun_mod->add($arr);
            $this->show_message('tianjiachenggong');
        } else {
            $gh_id = $_GET['id'];
            $row = $this->gonghuo_mod->getRow("select g.*,s.store_name from " . DB_PREFIX . "gonghuo g left join " . DB_PREFIX . "store s on g.cangkuid=s.store_id where g.gh_id='$gh_id' limit 1");

            $this->assign('row', $row);
            $this->display('baosunlog.html');
        }
    }

    function chakan_baosun()
    {
        $userid = $this->visitor->get('user_id');
        $priv_row = $this->userpriv_mod->getrow("select * from " . DB_PREFIX . "user_priv where user_id = '$userid' and store_id=0");
        $privs = $priv_row['privs'];
        $city = $priv_row['city'];
        $this->baosun_mod =& m('baosun');

        $conditions = " and 1=1";
        $type = $_GET['type'];
        if ($type != '') {
            $conditions .= " and type='$type' ";
            $this->assign('type', $type);
        }
        $cangku = $_GET['cangkuid'];
        if ($cangku != '') {
            $conditions .= " and cangkuid='$cangku' ";
            $this->assign('cangku', $cangku);
        }
        $ghname = $_GET['ghname'];
        if ($ghname != '') {
            $conditions .= " and gh_name like '%$ghname%' ";
            $this->assign('ghname', $ghname);
        }
        $start_time = $_GET['add_time_from'];
        $end_time = $_GET['add_time_to'];
        if ($start_time != '') {
            $conditions .= " and riqi >= '$start_time' ";
            $this->assign('start_time', $start_time);
        }
        if ($end_time != '') {
            $conditions .= " and riqi <= '$end_time' ";
            $this->assign('end_time', $end_time);
        }


        $page = $this->_get_page();


        $baosun = $this->baosun_mod->getAll("select * from " . DB_PREFIX . "baosun where 1=1 " . $conditions);
        foreach ($baosun as $key => $var) {
            $cangkuid = $var['cangkuid'];
            $row = $this->baosun_mod->getRow("select store_name from " . DB_PREFIX . "store where store_id='$cangkuid'");
            $baosun[$key]['store_name'] = $row['store_name'];
        }
        $cang = $this->baosun_mod->getAll("select store_name,store_id from " . DB_PREFIX . "store where is_cangku=1 ");
        $this->import_resource(array('script' => 'inline_edit.js,jquery.ui/jquery.ui.js,mlselection.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style' => 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $page['item_count'] = $this->baosun_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions ? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        $this->assign('baosun', $baosun);
        $this->assign('cang', $cang);
        $this->assign('priv_row', $priv_row);
        $this->display('chakan_baosun.html');
    }

}

?>
