<?php
$pass = $_GET['pw'];
$word = 'password'; //把这里的password换成你想要的值，但记得计划任务得改动！
if($pass != $word ){
	echo 'no';
	exit();
}else{
	// Fix for php without web server
	@chdir(dirname(__FILE__));
	require_once './system/common.inc.php';
	define('SIGN_LOOP', true);
	define('ENABLE_CRON', true);
	// Do nothing
	echo 'ok';
}