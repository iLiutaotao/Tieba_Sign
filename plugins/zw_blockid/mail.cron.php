<?php
if (!defined('IN_KKFRAME')) exit();

$date = date ('Ymd', TIMESTAMP + 900);
$block_error_uid = array();

$zw_blockid = new plugin_zw_blockid();
$sendmail_uid = array_filter(explode (',', $zw_blockid -> getSetting('sendmail_uid'))); //获取设定邮件报告的UID
$query = DB :: query("SELECT uid FROM `zw_blockid_log` WHERE status=0 AND DATE={$date}"); //获取有封禁失败记录的UID

while ($result = DB :: fetch($query)) {
	$block_error_uid[] = $result['uid'];
} 

$block_error_uid = array_unique($block_error_uid); //数组去重
$need_report_uid = array_intersect($block_error_uid, $sendmail_uid); //数组合并，找出需要发送邮件报告的UID

foreach($need_report_uid as $smid) { // 将报告邮件逐一加入邮件队列
	sendBlockReport(intval($smid));
} 
saveSetting('mail_queue',1);
define('CRON_FINISHED', true);

function sendBlockReport($smid) {
	global $siteurl;
	$date = date ('Ymd', TIMESTAMP + 900);
	$mdate = date('Y-m-d', TIMESTAMP);
	$query = DB :: query("SELECT tieba,blockid,status FROM `zw_blockid_log` WHERE uid={$smid} AND date={$date}");
	$memberinfo = DB :: fetch_first("SELECT username,email FROM member WHERE uid={$smid}");
	$i = 1;
	$message = <<<EOF
<html><body>
<style type="text/css">
div.wrapper * { font: 12px "Microsoft YaHei", arial, helvetica, sans-serif; word-break: break-all; }
div.wrapper a { color: #15c; text-decoration: none; }
div.wrapper a:active { color: #d14836; }
div.wrapper a:hover { text-decoration: underline; }
div.wrapper p { line-height: 20px; margin: 0 0 .5em; text-align: center; }
div.wrapper .sign_title { font-size: 20px; line-height: 24px; }
div.wrapper .result_table { width: 85%; margin: 0 auto; border-spacing: 0; border-collapse: collapse; }
div.wrapper .result_table td { padding: 10px 5px; text-align: center; border: 1px solid #dedede; }
div.wrapper .result_table tr { background: #d5d5d5; }
div.wrapper .result_table tbody tr { background: #efefef; }
div.wrapper .result_table tbody tr:nth-child(odd) { background: #fafafa; }
</style>
<div class="wrapper">
<p class="sign_title">循环封禁 - 封禁报告</p>
<p>{$mdate}<br>若有大量ID封禁失败，建议您重新设置 Cookie 相关信息或检查吧主权限</p>
<table class="result_table">
<thead><tr><td style="width: 40px">#</td><td>贴吧</td><td style="width: 75px">ID</td><td style="width: 75px">状态</td></tr></thead>
<tbody>
EOF;
	while ($result = DB :: fetch($query)) {
		$status = $result['status'] == 1?"成功":"失败";
		$message .= '<tr><td>' . ($i++) . "</td><td><a href=\"http://tieba.baidu.com/{$result['tieba']}\" target=\"_blank\">{$result['tieba']}</a></td><td>{$result['blockid']}</td><td>{$status}</td></tr>";
	} 
	$message .= '</tbody></table></div></body></html>';
	DB :: insert('mail_queue', array('to' => addslashes($memberinfo['email']),
			'subject' => addslashes("[{$mdate}] 循环封禁 - {$memberinfo['username']} - 封禁报告"),
			'content' => addslashes($message))
		);
} 
