<?php

 session_start();
 define( "WB_AKEY" , '3014382619' );
define( "WB_SKEY" , '263344b15947bcf7dd896e8aa3ffc5c5' );
define( "WB_CALLBACK_URL" , 'http://www.imm0371.com/sina.php' );
 include_once( 'saetv2.ex.class.php' ); 
 
 
 
 $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
$code_url = $o->getAuthorizeURL( WB_CALLBACK_URL );


if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

if ($token) {
	$_SESSION['token'] = $token;
	setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
$uid=$token['uid'];
	
}

  
?>