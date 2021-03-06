<?php

return array(
    'dashboard' => array(
        'text'      => Lang::get('dashboard'),
        'subtext'   => Lang::get('offen_used'),
        'default'   => 'welcome',
        'children'  => array(
            'welcome'   => array(
                'text'  => Lang::get('welcome_page'),
                'url'   => 'index.php?act=welcome',
            ),
            /*'aboutus'   => array(
               'text'  => Lang::get('aboutus_page'),
               'url'   => 'index.php?act=aboutus',
           ),
           'e_setting'  => array(
               'parent'=> 'setting',
               'text'  => Lang::get('base_setting'),
               'url'   => 'index.php?app=setting&act=base_setting',
           ),*/
            'user_manage' => array(
                'text'  => Lang::get('user_manage'),
                'parent'=> 'user',
                'url'   => 'index.php?app=user',
            ),
            'store_manage'  => array(
                'text'  => Lang::get('store_manage'),
                'parent'=> 'store',
                'url'   => 'index.php?app=store',
            ),
            'goods_manage'  => array(
                'text'  => Lang::get('goods_manage'),
                'parent'=> 'goods',
                'url'   => 'index.php?app=goods',
            ),
            'order_manage' => array(
                'text'  => Lang::get('order_manage'),
                'parent'=> 'trade',
                'url'   => 'index.php?app=order'
            ),
			'tongji'   => array(
                'text'  => Lang::get('tongji'),
                'url'   => 'index.php?act=tongji',
            ),
			'caiwubaobiao'   => array(
                'text'  => Lang::get('caiwubaobiao'),
                'url'   => 'index.php?act=caiwubaobiao',
            ),
        ),
    ),
    // 设置
    'setting'   => array(
        'text'      => Lang::get('setting'),
        'default'   => 'base_setting',
        'children'  => array(
            'base_setting'  => array(
                'text'  => Lang::get('base_setting'),
                'url'   => 'index.php?app=setting&act=base_setting',
            ),
            'region' => array(
                'text'  => Lang::get('region'),
                'url'   => 'index.php?app=region',
            ),
            'payment' => array(
                'text'  => Lang::get('payment'),
                'url'   => 'index.php?app=payment',
            ),
            //'theme' => array(
              //  'text'  => Lang::get('theme'),
              //  'url'   => 'index.php?app=theme',
            //),
           // 'template' => array(
           //     'text'  => Lang::get('template'),
           //     'url'   => 'index.php?app=template',
          //  ),
            'mailtemplate' => array(
                'text'  => Lang::get('noticetemplate'),
                'url'   => 'index.php?app=mailtemplate',
            ),
        ),
    ),
    // 商品
    'goods' => array(
        'text'      => Lang::get('goods'),
        'default'   => 'goods_manage',
        'children'  => array(
            'gcategory' => array(
                'text'  => Lang::get('gcategory'),
                'url'   => 'index.php?app=gcategory',
            ),
            'brand' => array(
                'text'  => Lang::get('brand'),
                'url'   => 'index.php?app=brand',
            ),
            'goods_manage' => array(
                'text'  => Lang::get('goods_manage'),
                'url'   => 'index.php?app=goods',
            ),
            'recommend_type' => array(
                'text'  => LANG::get('recommend_type'),
                'url'   => 'index.php?app=recommend'
            ),

        ),
    ),
    // 店铺
    'store'     => array(
        'text'      => Lang::get('store'),
        'default'   => 'store_manage',
        'children'  => array(
            'sgrade' => array(
                'text'  => Lang::get('sgrade'),
                'url'   => 'index.php?app=sgrade',
            ),
            'scategory' => array(
                'text'  => Lang::get('scategory'),
                'url'   => 'index.php?app=scategory',
            ),
            'store_manage'  => array(
                'text'  => Lang::get('store_manage'),
                'url'   => 'index.php?app=store',
            ),
			'store_log'  => array(
                'text'  => Lang::get('store_log'),
                'url'   => 'index.php?app=sgrade&act=storelog',
            ),
			'article_sto'  => array(
                'text'  => Lang::get('article_sto'),
                'url'   => 'index.php?app=article_sto',
            ),
			/*'store_jifen'  => array(
                'text'  => Lang::get('store_jifen'),
                'url'   => 'index.php?app=store&act=jifenbili',
            ),*/
        ),
    ),
    // 会员
    'user' => array(
        'text'      => Lang::get('user'),
        'default'   => 'user_manage',
        'children'  => array(
            'user_manage' => array(
                'text'  => Lang::get('user_manage'),
                'url'   => 'index.php?app=user',
            ),
            'admin_manage' => array(
                'text' => Lang::get('admin_manage'),
                 'url'   => 'index.php?app=admin',
             ),
             'user_notice' => array(
                'text' => Lang::get('user_notice'),
                'url'  => 'index.php?app=notice',
             ),
			 'invite' => array(
                'text' => Lang::get('invite'),
                'url'  => 'index.php?app=invite',
             ),
			 'rongyu' => array(
                'text' => Lang::get('rongyu'),
                'url'  => 'index.php?app=invite&act=rongyu',
             ),
			  'jiekuan' => array(
                'text' => Lang::get('jiekuan'),
                'url'  => 'index.php?app=jiekuan&act=jiekuan',
             ),
			
			
        ),
    ),
    // 交易
    'trade' => array(
        'text'      => Lang::get('trade'),
        'default'   => 'order_manage',
        'children'  => array(
            'order_manage' => array(
                'text'  => Lang::get('order_manage'),
                'url'   => 'index.php?app=order'
            ),
			 
        ),
    ),
    // 网站
    'website' => array(
        'text'      => Lang::get('website'),
        'default'   => 'acategory',
        'children'  => array(
            'acategory' => array(
                'text'  => Lang::get('acategory'),
                'url'   => 'index.php?app=acategory',
            ),
            'article' => array(
                'text'  => Lang::get('article'),
                'url'   => 'index.php?app=article',
            ),
            'partner' => array(
                'text'  => Lang::get('partner'),
                'url'   => 'index.php?app=partner',
            ),
            'navigation' => array(
                'text'  => Lang::get('navigation'),
                'url'   => 'index.php?app=navigation',
            ),
            'db' => array(
                'text'  => Lang::get('db'),
                'url'   => 'index.php?app=db&amp;act=backup',
            ),
            'groupbuy' => array(
                'text' => Lang::get('groupbuy'),
                'url'  => 'index.php?app=groupbuy',
            ),
            'consulting' => array(
                'text'  =>  LANG::get('consulting'),
                'url'   => 'index.php?app=consulting',
            ),
            'share_link' => array(
                'text'  =>  LANG::get('share_link'),
                'url'   => 'index.php?app=share',
            ),
			'adv' => array(
                'text'  =>  LANG::get('adv'),
                'url'   => 'index.php?app=adv',
            ),
			'coupon' => array(
                'text'  =>  LANG::get('coupon'),
                'url'   => 'index.php?app=coupon',
            ),
			'fenzhan' => array(
                'text'  =>  LANG::get('fenzhan'),
                'url'   => 'index.php?app=fenzhan',
            ),
			'webs' => array(
                'text'  =>  LANG::get('webs'),
                'url'   => 'index.php?app=fenzhan&act=web',
            ),
			'gonghuo' => array(
                'text'  =>  LANG::get('gonghuo'),
                'url'   => 'index.php?app=gonghuo',
            ),
			'tousu' => array(
                'text'  =>  LANG::get('tousu'),
                'url'   => 'index.php?app=tousu',
            ),
			'site_system' => array(
                'text'  =>  LANG::get(iconv('utf-8','gbk','企业管理')),
                'url'   => 'index.php?app=site_system',
            ),
        ),
    ),
    // 扩展
    'extend' => array(
        'text'      => Lang::get('extend'),
        'default'   => 'plugin',
        'children'  => array(
            'plugin' => array(
                'text'  => Lang::get('plugin'),
                'url'   => 'index.php?app=plugin',
            ),
            'module' => array(
                'text'  => Lang::get('module'),
                'url'   => 'index.php?app=module&act=manage',
            ),
            'widget' => array(
                'text'  => Lang::get('widget'),
                'url'   => 'index.php?app=widget',
            ),
        ),
    ),
);

?>
