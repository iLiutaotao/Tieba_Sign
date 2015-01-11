<?php
if(!defined('IN_KKFRAME')) exit();
?>
<form method="post" action="member.php?action=login">
<div class="login-info">
<p><input type="text" name="username" required="" tabindex="1" placeholder="用户名"></p>
<p><input type="password" name="password" required="" tabindex="2" placeholder="密码"></p>
<?php HOOK::run('login_form'); ?>
</div>
<p><input type="submit" value="登录" tabindex="3" /></p>
</form>