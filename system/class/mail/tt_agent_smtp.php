<?php
if (!defined('IN_KKFRAME')) exit('Access Denied');
class tt_agent_smtp extends mailer {
	var $id = 'tt_agent_smtp';
	var $name = '涛涛开放云平台代理SMTP发件';
	var $description = '开放云平台发送邮件服务，会以open-sign-report@liujiantao.me为发件人发送邮件主题为Open-Mail-System';
	var $config = array(
		array('API地址(推荐地址http://api.liujiantao.me/mail/smtp.php更新请见<a href="https://github.com/liujiantaoliu" target="_blank">GitHub</a>)', 'agentapi', '', 'http://api.liujiantao.me/mail/smtp.php'),
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
			);
		$agentapi = $this -> _get_setting('agentapi');
		$sendresult = json_decode($this -> post($agentapi, $data), true);
		if ($sendresult['err_no']==0) return true;
		return false;
	}
}
?>