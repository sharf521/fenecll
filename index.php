<?php
error_reporting(7);   

//获取当前一级域名
$domin=strtolower($_SERVER['HTTP_HOST']);
$domin=explode('.',$domin);
if($domin[0]=='www')
{
    unset($domin[0]);
}
$domin=implode('.',$domin);

//多主机共享保存 SESSION ID 的 COOKIE
ini_set('session.cookie_domain',$domin);
//session_start();

header("X-Powered-By:JAVA");
//session_cache_limiter('private, must-revalidate');//返回页面不清空缓存
define('ROOT_PATH', dirname(__FILE__));
include(ROOT_PATH . '/eccore/ecmall.php');
$_S=array();

function _safe_str($str)
	{
		if(!get_magic_quotes_gpc())	{
			if( is_array($str) ) {
				foreach($str as $key => $value) {
					$str[$key] = _safe_str($value);
				}
			}else{
				$str = addslashes($str);
			}
		}
		return $str;
	}
foreach(array('_GET','_POST','_REQUEST') as $key)
		{
			if (isset($$key)){
				foreach($$key as $_key => $_value){
					$_value=strip_tags($_value);
					$$key[$_key] = _safe_str($_value);
				}
			}
		}



date_default_timezone_set('Asia/Shanghai');//时区配置
/* 定义配置信息 */
ecm_define(ROOT_PATH . '/data/config.inc.php');

//$webserv_ip1='192.168.1.11:8081';
//$webserv_ip1='116.255.156.154:8888';
//$webserv_ip1='116.255.133.71:8888';

if(extension_loaded('zlib'))
{
	@ini_set('zlib.output_compression', 'On');
	@ini_set('zlib.output_compression_level', '9');
}

/* 启动ECMall */
ECMall::startup(array(
    'default_app'   =>  'default',
    'default_act'   =>  'index',
    'app_root'      =>  ROOT_PATH . '/app',
    'external_libs' =>  array(
        ROOT_PATH . '/includes/global.lib.php',
        ROOT_PATH . '/includes/libraries/time.lib.php',
        ROOT_PATH . '/includes/ecapp.base.php',
        ROOT_PATH . '/includes/plugin.base.php',
        ROOT_PATH . '/app/frontend.base.php',
        ROOT_PATH . '/includes/subdomain.inc.php',
    ),
));


?>