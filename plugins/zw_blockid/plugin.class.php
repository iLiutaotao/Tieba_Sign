<?php
if (! defined ('IN_KKFRAME')) exit ('Access Denied!');

class plugin_zw_blockid extends Plugin {
	var $name = 'zw_blockid';
	var $description = '本插件可以给网站用户提供循环封禁用户功能';
	var $modules = array (
		array ('id' => 'index',
			'type' => 'page',
			'title' => '循环封禁',
			'file' => 'zw_blockid.inc.php'
			),
		array('type' => 'cron',
			'cron' => array('id' => 'zw_blockid/daily', 'order' => 101),
			),
		array('type' => 'cron',
			'cron' => array('id' => 'zw_blockid/blockid', 'order' => 102),
			),
		array('type' => 'cron',
			'cron' => array('id' => 'zw_blockid/mail', 'order' => 103),
			),
		);
	var $version = '1.2.9';

	function install() {
		runquery("CREATE TABLE `zw_blockid_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `blockid` varchar(20) NOT NULL,
  `tieba` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`fid`,`blockid`,`tieba`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `zw_blockid_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `fid` int(8) NOT NULL,
  `tieba` varchar(200) NOT NULL,
  `blockid` varchar(100) NOT NULL,
  `date` int(11) NOT NULL DEFAULT '20131201',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `retry` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
	}

	function uninstall() {
		runquery("
DROP TABLE `zw_blockid_list`;
DROP TABLE `zw_blockid_log`;
DELETE FROM `plugin_var` WHERE `pluginid`='zw_blockid';
");
	}

	function checkCompatibility() {
		if (version_compare(VERSION, '1.14.4.24', '<')) showmessage('签到助手版本过低，请升级');
	}

	function page_footer_js() {
		echo '<script src="plugins/zw_blockid/zw_blockid.js"></script>';
	}

	function on_upgrade($nowversion) {
		if ($nowversion == '0') {
			DB :: query("DELETE FROM  `setting` WHERE  `k` LIKE  'zw_blockid%';");
			return '1.2.0';
		}
		if ($nowversion == '1.2.0') {
			return '1.2.4';
		}
		if ($nowversion == '1.2.4') {
			runquery("UPDATE cron SET id='zw_blockid/cron/zw_blockid' WHERE id='zw_blockid';
			UPDATE cron SET id='zw_blockid/cron/zw_blockid_daily' WHERE id='zw_blockid_daily';
			UPDATE cron SET id='zw_blockid/cron/zw_blockid_mail' WHERE id='zw_blockid_mail';");
			return '1.2.5';
		}
		if ($nowversion == '1.2.5') {
			runquery("UPDATE cron SET id='zw_blockid/cron_blockid' WHERE id='zw_blockid' OR id='zw_blockid/cron/zw_blockid';
			UPDATE cron SET id='zw_blockid/cron_daily' WHERE id='zw_blockid_daily' OR id='zw_blockid/cron/zw_blockid_daily';
			UPDATE cron SET id='zw_blockid/cron_mail' WHERE id='zw_blockid_mail' OR id='zw_blockid/cron/zw_blockid_mail';");
			return '1.2.6';
		}
		if ($nowversion == '1.2.6') {
			runquery("UPDATE cron SET id='zw_blockid/blockid' WHERE id='zw_blockid/cron_blockid';
			UPDATE cron SET id='zw_blockid/daily' WHERE id='zw_blockid/cron_daily';
			UPDATE cron SET id='zw_blockid/mail' WHERE id='zw_blockid/cron_mail';");
			return '1.2.8';
		}
		if ($nowversion == '1.2.8') {
			runquery("CREATE TABLE IF NOT exists `zw_blockid_list_tmp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `blockid` varchar(20) NOT NULL,
  `tieba` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`fid`,`blockid`,`tieba`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `zw_blockid_list_tmp`(uid, fid, blockid, tieba) SELECT DISTINCT uid, fid, blockid, tieba FROM `zw_blockid_list`;
DELETE FROM `zw_blockid_list`;
INSERT INTO `zw_blockid_list`(uid, fid, blockid, tieba) SELECT DISTINCT uid, fid, blockid, tieba FROM `zw_blockid_list_tmp`;
DROP TABLE `zw_blockid_list_tmp`;
ALTER TABLE `zw_blockid_list` ADD UNIQUE (`uid` ,`fid` ,`blockid` ,`tieba`);
");
		}
	}

	function handleAction() {
		global $uid;
		if (!$uid) exit ('Access Denied!');
		$data = array ();
		$data ['msgx'] = 1;
		switch ($_GET ['action']) {
			case 'add-id' :
				$tieba = daddslashes($_POST ['tb_name']);
				$blockid = daddslashes($_POST ['user_name']);
				$tb_name = mb_convert_encoding($_POST ['tb_name'], 'gb2312', 'utf-8');
				$url = 'http://tieba.baidu.com/f?kw=' . urlencode ($tb_name);
				$contents = kk_fetch_url($url, 0, '', get_cookie ($uid));
				$fid = 0;
				preg_match ('/"forum_id"?:?(?<fid>\d+)/', $contents, $fids);
				$fid = $fids ['fid'];
				if ($fid == 0) {
					$data ['msg'] = "添加失败，无法获取该贴吧的FID";
					$data ['msgx'] = 0;
					break;
				}
				if ($result = DB :: result_first("SELECT * FROM zw_blockid_list WHERE uid={$uid} AND fid={$fid} AND blockid='{$blockid}' AND tieba='{$tieba}'")) {
					$data ['msg'] = "添加失败，已有重复记录！";
				} else {
					DB :: insert ('zw_blockid_list', array ('uid' => $uid,
							'fid' => $fid,
							'blockid' => $blockid,
							'tieba' => $tieba,
							));
					$re = $this -> blockid($fid, $blockid, 1, $uid);
					$data ['msg'] = ($re['errno'] == 0 ? "封禁成功" : "封禁失败，已添加进封禁列表") . "！贴吧FID为：{$fid}，被封禁用户为{$user_name}";
				}
				break;
			case 'add-id-batch' :
				$tieba = $_POST ['tb_name'];
				$user_name = explode ("\n", $_POST ['user_name']);
				for($i = 0;$i < count($user_name);$i++) {
					$user_name[$i] = trim($user_name[$i]);
				}
				$user_name = array_filter($user_name);
				if (!is_array($user_name)) {
					$data ['msg'] = "添加失败：格式错误，多个ID请用换行分隔！";
					break;
				}
				$tb_name = mb_convert_encoding ($tieba, 'gb2312', 'utf-8');
				$url = 'http://tieba.baidu.com/f?kw=' . urlencode ($tb_name);
				$contents = kk_fetch_url($url, 0, '', get_cookie ($uid));
				$fid = 0;
				preg_match ('/"forum_id"?:?(?<fid>\d+)/', $contents, $fids);
				$fid = $fids ['fid'];
				if ($fid == 0) {
					$data ['msg'] = "添加失败，无法获取该贴吧的FID";
					$data ['msgx'] = 0;
					break;
				}
				$count = 0;
				foreach($user_name as $id) {
					if (DB :: insert ('zw_blockid_list', array ('uid' => $uid,
								'fid' => $fid,
								'blockid' => daddslashes($id),
									'tieba' => daddslashes($tieba),
									), true)) $count++;
				}
				$data ['msg'] = "成功添加了{$count}个ID！所在贴吧为{$tieba}，该贴吧FID为：{$fid}";
				break;
			case 'get-list' :
				$data ['list'] = array ();
				$data ['log'] = array ();
				$query = DB :: query ("SELECT * FROM zw_blockid_list WHERE uid={$uid}");
				while ($result = DB :: fetch ($query)) {
					$data ['list'] [] = $result;
				}
				$data ['today'] = date('Ymd');
				$sendmail_uid = array_filter(explode (',', $this -> getSetting('sendmail_uid')));
				$data['sendmail'] = in_array($uid, $sendmail_uid) ? 1 : 0;
				break;
			case 'get-log':
				$date = intval($_GET['date']);
				$data ['log'] = array ();
				$data ['today'] = date('Ymd');
				$data ['date'] = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
				$data ['log_success_status'] = 0;
				$query = DB :: query ("SELECT * FROM zw_blockid_log WHERE uid={$uid} AND date={$date}");
				while ($result = DB :: fetch ($query)) {
					if ($result['status'] == 1) $data ['log_success_status']++;
					$data ['log'] [] = $result;
				}
				$data['log_count'] = count($data ['log']);
				$data['before_date'] = DB :: result_first("SELECT date FROM zw_blockid_log WHERE uid={$uid} AND date<{$date} ORDER BY date DESC LIMIT 0,1");
				$data['after_date'] = DB :: result_first("SELECT date FROM zw_blockid_log WHERE uid={$uid} AND date>{$date} ORDER BY date LIMIT 0,1");
				break;
			case 'del-blockid' :
				$no = intval($_GET ['no']);
				DB :: query ("DELETE FROM zw_blockid_list WHERE id={$no} AND uid={$uid}");
				$data ['msg'] = "删除成功！";
				break;
			case 'do-blockid':
				$username = urldecode($_GET['blockid']);
				$tieba = urldecode($_GET['tieba']);
				$re = $this -> blockid ($_GET['fid'], $username, 1, $uid);
				$id = intval($_GET['bid']);
				if ($re['errno'] == -1) {
					$data ['msg'] = "JSON解析失败！";
				} elseif ($re['errno'] == 1) {
					$data ['msg'] = "封禁成功！封禁账号：{$username}，FID为{$_GET['fid']}";
					DB :: query ("UPDATE zw_blockid_log SET status=1 WHERE id={$id} AND uid={$uid}");
				} else {
					$data ['msg'] = "封禁失败！返回信息：{$re['errmsg']}，封禁账号：{$username}，所在贴吧：{$tieba}，FID为{$_GET['fid']}";
				}
				break;
			case 'del-all' :
				DB :: query ("DELETE FROM zw_blockid_list WHERE uid='{$uid}'");
				$data ['msg'] = "删除成功！";
				break;
			case 'test-blockid' :
				$query = DB :: query ("SELECT * FROM zw_blockid_list WHERE uid='{$uid}'");
				while ($result = DB :: fetch ($query)) {
					$blockid_list [] = $result;
				}
				if (! $blockid_list) {
					$data ['msgx'] = 0;
					$data ['msg'] = "没有封禁信息，请先添加！";
					break;
				}
				$rand = rand (0, count ($blockid_list) - 1);
				$test_blockid = $blockid_list [$rand];
				$re = $this -> blockid ($test_blockid ['fid'], $test_blockid ['blockid'], 1, $uid);
				if ($re['errno'] == -1) {
					$data ['msg'] = "JSON解析失败！";
				} elseif ($re['errno'] == 0) {
					$data ['msg'] = "封禁成功！封禁账号：{$test_blockid['blockid']}，所在贴吧：{$test_blockid['tieba']}，FID为{$test_blockid['fid']}";
				} else {
					$data ['msg'] = "封禁失败！返回信息：{$re['errmsg']}，封禁账号：{$test_blockid['blockid']}，所在贴吧：{$test_blockid['tieba']}，FID为{$test_blockid['fid']}";
				}
				break;
			case 'setting':
				if (intval($_POST['zw_blockid-report']) == 1) {
					$sendmail_uid = array_filter(explode (',', $this -> getSetting('sendmail_uid')));
					if (!in_array($uid, $sendmail_uid)) $sendmail_uid[] = $uid;
					$this -> saveSetting('sendmail_uid', implode(',', $sendmail_uid));
					$data ['msg'] = "成功开启邮件报告！";
				} else {
					$sendmail_uid = array_filter(explode (',', $this -> getSetting('sendmail_uid')));
					if (in_array($uid, $sendmail_uid)) {
						for($i = 0;$i < count($sendmail_uid);$i++) {
							if ($sendmail_uid[$i] == $uid) unset($sendmail_uid[$i]);
						}
						$this -> saveSetting('sendmail_uid', implode(',', $sendmail_uid));
					}
					$data ['msg'] = "成功关闭邮件报告！";
				}
				break;
			default :
				$data ['msg'] = "没有指定action！";
				break;
		}
		echo json_encode ($data);
	}

	function blockid($fid, $id, $day, $douid) {
		$blockid_api = "http://tieba.baidu.com/pmc/blockid";
		$formdata = array('user_name[]' => $id,
			'day' => $day,
			'fid' => $fid,
			'tbs' => get_tbs($douid),
			'ie' => 'gbk',
			'reason' => "抱歉，你的发贴操作或发表贴子的内容违反了本吧的吧规，已经被封禁，封禁期间不能在本吧继续发言。"
			);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $blockid_api);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIE, get_cookie($douid));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formdata));
		$re = @json_decode(curl_exec($ch), true);
		curl_close($ch);
		if (empty($re)) {
			return array('errno' => -1, 'errmsg' => '未知错误！');
		} else {
			return $re;
		}
	}
}
