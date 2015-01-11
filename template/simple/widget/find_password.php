<?php
if(!defined('IN_KKFRAME')) exit();
?>
<form method="post" action="member.php?action=find_password">
<div class="login-info">
<p><input type="text" name="username" required="" tabindex="1" placeholder="用户名"></p>
<p><input type="text" name="email" required="" tabindex="2" placeholder="邮箱"></p>
</div>
<p><input type="submit" value="找回密码" tabindex="3"></p>
</form>
