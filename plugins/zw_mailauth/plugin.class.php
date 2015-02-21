<?php
if (!defined('IN_KKFRAME')) exit('Access Denied!');

class plugin_zw_mailauth extends Plugin {
	var $description = '著微注册邮箱认证插件。';
	var $modules = array(array('id' => 'admin',
			'type' => 'page',
			'title' => '邮箱验证管理',
			'file' => 'admin.inc.php',
			'admin' => 1
			));
	var $version = '1.1.3';

	function install() {
		runquery("CREATE TABLE IF NOT EXISTS `zw_mailauth_list` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(30) NOT NULL,
  `password` char(32) NOT NULL,
  `email` varchar(80) NOT NULL,
  `authcode` char(64) NOT NULL,
  `regtime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
		$this -> saveSetting('setting', json_encode(array('deathtime' => 120,
					'title' => '[贴吧签到助手] 注册验证邮件',
					'format' => '<p>尊敬的{username}，您好！</p><p>    感谢您注册贴吧签到助手，请在{deathtime}分钟内点击下面的链接以激活您的帐号：</p><p>    激活链接：{authlink}</p><p>如果您没有注册却收到本邮件，请忽略。</p><p align="right">贴吧签到助手<br>{sendtime}</p>',
					'mailaddrepeat' => 1,
					'abledomain' => '',
					'unabledomain' => '',
					'unableaddress' => '',
					)));
	} 

	function on_uninstall() {
		runquery("DROP TABLE `zw_mailauth_list`;");
	} 

	function uninstall() { // 2014-3-22版本前遗留问题
		$this -> on_uninstall();
	} 

	function page_footer_js() {
		global $uid;
		if (is_admin($uid)) echo '<script src="plugins/zw_mailauth/zw_mailauth.js"></script>';
	} 

	function before_register() {
		global $email, $username, $siteurl;
		$authuser = DB :: fetch_first("SELECT * FROM zw_mailauth_list WHERE username='{$username}'");
		if ($authuser) showmessage('用户名已经存在', 'member.php');
		$setting = json_decode($this -> getSetting('setting'), true);
		if ($setting['mailaddrepeat'] == 1) {
			$e_mail = DB :: fetch_first("SELECT * FROM member WHERE email='{$email}'");
			if ($e_mail) showmessage('邮箱已被注册，如果该邮箱是您的，你可以选择找回密码', 'member.php');
			$authmail = DB :: fetch_first("SELECT * FROM zw_mailauth_list WHERE email='{$email}'");
			if ($authmail) showmessage('该邮箱已被注册且暂未通过验证，请到您邮箱中查找验证邮件被单击激活链接以完成激活', 'member.php', 5);
		} 
		// 检查邮箱类型是否允许被注册
		if ($setting['abledomain'] != '') {
			$emaildomain = explode('@', $email);
			$abledomains = explode("\n", $setting['abledomain']);
			if (array_search($emaildomain[1], $abledomains, false) === false) showmessage('该邮箱类型不允许注册，请更换邮箱类型！', dreferer(), 5);
		} 
		// 检查邮箱类型是否禁止被注册
		if ($setting['unabledomain'] != '') {
			$emaildomain = explode('@', $email);
			$unabledomains = explode("\n", $setting['unabledomain']);
			if (array_search($emaildomain[1], $unabledomains, false) === true) showmessage('该邮箱类型不允许注册，请更换邮箱类型！', dreferer(), 5);
		} 
		// 检查邮箱地址是否禁止被注册
		if ($setting['unableaddress'] != '') {
			$unableaddresss = explode("\n", $setting['unableaddress']);
			if (array_search($email, $unableaddresss, false) === true) showmessage('该邮箱类型不允许注册3，请更换邮箱类型！', dreferer(), 5);
		} 
		$nowtime = time();
		$authcode = md5(md5($_POST['password'] . 'zulwi' . $nowtime . 'mailauthcode')) . md5(md5('nowtimeis' . $nowtime . 'rand' . rand(0, 99999)));
		DB :: insert('zw_mailauth_list', array('username' => $username,
				'password' => md5(ENCRYPT_KEY . md5($_POST['password']) . ENCRYPT_KEY),
				'email' => $email,
				'authcode' => $authcode,
				'regtime' => $nowtime
				));
		$content = $setting['format'];
		$authlink = $siteurl . 'member.php?action=verify&username=' . urlencode($username) . '&authcode=' . $authcode; 
		// 检查是否有特定标记并进行替换
		if (strstr($content, '{username}')) $content = str_replace('{username}', $username, $content);
		if (strstr($content, '{deathtime}')) $content = str_replace('{deathtime}', $setting['deathtime'], $content);
		if (strstr($content, '{authlink}')) $content = str_replace('{authlink}', $authlink, $content);
		if (strstr($content, '{sendtime}')) $content = str_replace('{sendtime}', date('Y年m月d日 H:m:s'), $content);
		DB :: insert('mail_queue', array('to' => $email,
				'subject' => $setting['title'],
				'content' => $content,
				));
		saveSetting('mail_queue', 1);
		showmessage("注册成功，您的用户名是 <b>{$username}</b> ，注册验证邮件稍后将发送到您填写的邮箱中，请在 <b>{$setting['deathtime']}</b> 分钟内点击邮件内的链接激活帐号哦~~！", dreferer(), 5);
	} 

	function on_load() {
		switch ($_GET['action']) {
			case 'verify':
				$username = daddslashes(urldecode($_GET['username']));
				$authinfo = DB :: fetch_first("SELECT * FROM zw_mailauth_list WHERE username='{$username}'");
				if (!$authinfo) {
					showmessage("没有找到待验证记录，请重新注册！", dreferer(), 3);
				} elseif ($authinfo['authcode'] == $_GET['authcode']) {
					$setting = json_decode($this -> getSetting('setting'), true);
					$nowtime = time();
					DB :: query("DELETE FROM zw_mailauth_list WHERE id='{$authinfo['id']}'");
					if ($nowtime > $authinfo['regtime'] + $setting['deathtime'] * 60) {
						showmessage("抱歉，已超过验证期限，请重新注册！", dreferer(), 3);
					} else {
						$repeatusername = DB :: fetch_first("SELECT * FROM member WHERE username='{$authinfo['username']}'");
						if ($repeatusername) showmessage("抱歉，用户名已存在，请更换用户名。", dreferer(), 3);
						$uid = DB :: insert('member', array('username' => $authinfo['username'],
								'password' => $authinfo['password'],
								'email' => $authinfo['email'],
								));
						DB :: insert('member_setting', array('uid' => $uid));
						CACHE :: update('username');
						CACHE :: save('user_setting_' . $uid, '');
						showmessage("恭喜您，验证成功~~~您的用户名是{$username}，现在赶快登录吧~~~", dreferer(), 3);
					} 
				} else {
					showmessage("抱歉，验证失败，请检查您的验证链接或联系管理员！", dreferer(), 3);
				} 
				break;
			default:
				if ($_POST['username'] && $_POST['password']) {
					$username = daddslashes($_POST['username']);
					$authinfo = DB :: fetch_first("SELECT * FROM zw_mailauth_list WHERE username='{$username}'");
					if ($authinfo) showmessage("抱歉，您的帐号尚未通过邮箱验证，请前往邮箱查看验证邮件进行验证！", dreferer(), 3);
				} 
				$setting = json_decode($this -> getSetting('setting'), true);
				$deltime = time() - $setting['deathtime'] * 60;
				DB :: query("DELETE FROM `zw_mailauth_list` WHERE `regtime`<{$deltime}");
		} 
	} 

	function getMailContent($format, $username, $authcode, $deathtime) {
		global $siteurl;
		$content = $format;
		$authlink = substr($siteurl, 0, -20) . 'member.php?action=verify&username=' . urlencode($username) . '&authcode=' . $authcode; 
		// 检查是否有特定标记并进行替换
		if (strstr($content, '{username}')) $content = str_replace('{username}', $username, $content);
		if (strstr($content, '{deathtime}')) $content = str_replace('{deathtime}', $deathtime, $content);
		if (strstr($content, '{authlink}')) $content = str_replace('{authlink}', $authlink, $content);
		if (strstr($content, '{sendtime}')) $content = str_replace('{sendtime}', date('Y年m月d日 H:m:s'), $content);
		return $content;
	} 

	function on_upgrade($current_ver) {
		switch ($current_ver) {
			case '1.1.2':
				$setting = json_decode($this -> getSetting('setting'), true);
				if (empty($setting['deathtime'])) {
					$setting['deathtime'] = 120;
					$this -> saveSetting('setting', json_encode($setting));
				} 
				break;
		} 
	} 

	function handleAction() {
		global $uid;
		if (!is_admin($uid)) exit('Access Denied');
		$data = array();
		$data['msgx'] = 0;
		$setting = json_decode($this -> getSetting('setting'), true);
		switch ($_GET['action']) {
			case 'getsetting':
				$query = DB :: query("SELECT * FROM `zw_mailauth_list`;");
				while ($result = DB :: fetch ($query)) {
					$result['regtime'] = date("Y年m月d日 H:m:s", $result['regtime']);
					$data ['list'] [] = $result;
				} 
				$data ['count'] = count($data ['list']);
				$data ['setting'] = json_decode($this -> getSetting('setting'), true);
				break;
			case 'savesetting':
				$mailaddrepeat = $_POST['mailaddrepeat'] == 1?1:0;
				$this -> saveSetting('setting', json_encode(array('deathtime' => $_POST['deathtime'],
							'title' => $_POST['title'],
							'format' => $_POST['format'],
							'mailaddrepeat' => $mailaddrepeat,
							'abledomain' => $_POST['abledomain'],
							'unabledomain' => $_POST['unabledomain'],
							'unableaddress' => $_POST['unableaddress'],
							)));
				$data['msg'] = '保存成功！';
				break;
			case 'clear':
				$deltime = time() - $setting['deathtime'] * 60;
				DB :: query("DELETE FROM `zw_mailauth_list` WHERE `regtime`<{$deltime}");
				$data['msg'] = "清除成功！";
				break;
			case 'alldel':
				DB :: query('TRUNCATE TABLE `zw_mailauth_list`');
				$data['msg'] = '已经全部删除！';
				break;
			case 'allpass':
				$query = DB :: query("SELECT * FROM `zw_mailauth_list`;");
				while ($result = DB :: fetch ($query)) {
					$list [] = $result;
				} 
				DB :: query('TRUNCATE TABLE `zw_mailauth_list`');
				for($i = 0;$i < count($list);$i++) {
					$uid = DB :: insert('member', array('username' => $list[$i]['username'],
							'password' => $list[$i]['password'],
							'email' => $list[$i]['email'],
							));
					DB :: insert('member_setting', array('uid' => $uid));
					CACHE :: update('username');
					CACHE :: save('user_setting_' . $uid, '');
				} 
				$data['msg'] = '已经全部通过！';
				break;
			case 'allresend':
				$query = DB :: query("SELECT * FROM `zw_mailauth_list`");
				while ($result = DB :: fetch ($query)) {
					$list [] = $result;
				} 
				for($i = 0;$i < count($list);$i++) {
					$content = $this -> getMailContent($setting['format'], $list[$i]['username'], $list[$i]['authcode'], $list[$i]['deathtime']);
					DB :: insert('mail_queue', array('to' => $list[$i]['email'],
							'subject' => $setting['title'],
							'content' => $content
							));
				} 
				DB :: query("UPDATE `zw_mailauth_list` SET `regtime`=" . time());
				saveSetting('mail_queue', 1);
				$data['msg'] = '已经全部加入到邮件队列中，稍后将自动发送！';
				break;
			case 'resend':
				$result = DB :: fetch_first("SELECT * FROM `zw_mailauth_list` WHERE `id`=" . intval($_GET['vid']));
				$content = $this -> getMailContent($setting['format'], $result['username'], $result['authcode'], $setting['deathtime']);
				DB :: query("UPDATE `zw_mailauth_list` SET `regtime`=" . time() . " WHERE `id`=" . intval($_GET['vid']));
				DB :: insert('mail_queue', array('to' => $result['email'],
						'subject' => $setting['title'],
						'content' => $content,
						));
				saveSetting('mail_queue', 1);
				$data['msg'] = "新的验证邮件已经加入到队列中，稍后将自动发送！";
				break;
			case 'pass':
				$result = DB :: fetch_first("SELECT * FROM `zw_mailauth_list` WHERE `id`=" . intval($_GET['vid']));
				$uid = DB :: insert('member', array('username' => $result['username'],
						'password' => $result['password'],
						'email' => $result['email'],
						));
				DB :: insert('member_setting', array('uid' => $uid));
				CACHE :: update('username');
				CACHE :: save('user_setting_' . $uid, '');
				DB :: query("DELETE FROM `zw_mailauth_list` WHERE id=" . intval($_GET['vid']));
				$data['msg'] = '已经通过帐号的邮箱验证！';
				break;
			case 'del':
				DB :: query("DELETE FROM `zw_mailauth_list` WHERE id=" . intval($_GET['vid']));
				$data['msg'] = '成功删除该记录！';
				break;
			default:
				$data['msg'] = '没有指定 Action！！';
		} 
		echo json_encode ($data);
	} 
} 
