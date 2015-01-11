<?php
if (!defined('IN_KKFRAME')) exit('Access Denied');

class zw_agent_smtp extends mailer {
	var $id = 'zw_agent_smtp';
	var $name = '著微代理SMTP发件';
	var $description = '自定义SMTP账号，由支持SMTP的服务器代登录发送。插件版本：v1.0.4';
	var $config = array(
		array('SMTP服务器', 'host', '', ''),
		array('SMTP邮箱', 'mail', '', '', 'email'),
		array('SMTP用户名(一般与邮箱一致)', 'user', '', '', 'email'),
		array('SMTP密码', 'pass', '', '', 'password'),
		array('API地址（<a href="http://www.kookxiang.com/thread-775-1-1.html" target="_blank">点此查看最新API</a>）', 'agentapi', '', 'http://api.zhuwei.cc/mailapi?t=smtp'),
		);

	function isAvailable() {
		return true;
	} 

	function post($url, $content) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	} 

	function send($mail) {
		$data = array('to' => $mail -> address,
			'title' => $mail -> subject,
			'content' => $mail -> message,
			'host' => $this -> _get_setting('host'),
			'address' => $this -> _get_setting('mail'),
			'user' => $this -> _get_setting('user'),
			'pass' => $this -> _get_setting('pass'),
			);
		$agentapi = $this -> _get_setting('agentapi');
		$sendresult = json_decode($this -> post($agentapi, $data));
		if ($sendresult['errno']==0) return 'ok';
	} 
} 

?>