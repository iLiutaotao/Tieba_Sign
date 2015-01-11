<?php
if(!defined('IN_KKFRAME')) exit();
$extra_title = getSetting('extra_title');
$title = $extra_title ? "用户中心 - 贴吧签到助手 - {$extra_title}" : '用户中心 - 贴吧签到助手';
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<?php include template('widget/meta'); ?>
</head>
<body>
<div class="wrapper" id="page_login">
<h1>贴吧签到助手+</h1>
<p class="title_desc">麻麻再也不用担心我断签了~</p>
<div id="content-login" class="center-box">
<img src="./template/simple/style/no_avatar.png" class="avatar">
<?php include template('widget/login'); ?>
</div>
<div id="content-register" class="hidden center-box">
<img src="./template/simple/style/no_avatar.png" class="avatar">
<?php include template('widget/register'); ?>
</div>
<div id="content-lostpass" class="hidden center-box">
<img src="./template/simple/style/no_avatar.png" class="avatar">
<form method="post" action="member.php?action=find_password">
<?php include template('widget/find_password'); ?>
</div>
<p class="other">
<a href="javascript:;" id="menu_login" class="current">已有账号？点击登陆</a>
<?php if(!getSetting('block_register')) { ?>
<a href="javascript:;" id="menu_register">注册新账号</a>
<?php } ?>
<a href="javascript:;" id="menu_lostpass">找回密码</a>
</p>
<div class="footer">
<ul>
<li>Modified by <a href="http://www.iwch.me" target="_blank">Wang Chenhao</a></li>
<li>Designed by <a href="http://ikk.me" target="_blank">kookxiang</a></li>
<li><a href="http://www.iwch.me/about/donate" target="_blank">赞助开发</a></li>
<li>贴吧签到助手 4.6.2 &copy; 2014. All Rights Reserved</li>
<?php if(getSetting('beian_no')) echo ' - <a href="http://www.miibeian.gov.cn/" target="_blank" rel="nofollow">'.getSetting('beian_no').'</a>'; ?>
</ul>
</div>
<script src="<?php echo jquery_path(); ?>"></script>
<script src="./template/simple/js/member.js?version=<?php echo VERSION; ?>"></script>
<?php HOOK::run('member_footer'); ?>
</div>
</body>
</html>