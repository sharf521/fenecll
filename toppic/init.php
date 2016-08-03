<?
date_default_timezone_set('Asia/Shanghai');//时区配置
define('ROOT', realpath(dirname(__FILE__).'/../'));
require_once(ROOT.'/data/config.inc.php');

$db_config['port']     = '3306';      //端口		
$db_config['prefix']   = 'ecm_'; //CMS表名前缀	
$db_config['language'] = 'gbk'; //数据库字符集 gbk,latin1,utf8,utf8..

session_cache_limiter('private, must-revalidate');//返回页面不清空缓存 
session_start();

require_once(ROOT.'/chinapnr/mysql.class.php');
$db = new Mysql($db_config);