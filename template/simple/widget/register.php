<?php
if(!defined('IN_KKFRAME')) exit();
?>
<form method="post" action="member.php?action=register">
<div class="login-info">
<p><input type="text" name="<?php echo $form_username; ?>" required tabindex="1" placeholder="用户名"/></p>
<p><input type="password" name="<?php echo $form_password; ?>" required tabindex="2" placeholder="密码"/></p>
<p><input type="text" name="<?php echo $form_email; ?>" required tabindex="3" placeholder="邮箱"/></p>
<?php
if($invite_code) echo '<p><input type="text" name="invite_code" required placeholder="邀请码"/></p>';
?>
<?php HOOK::run('register_form'); ?>
</div>
<p><input type="submit" value="注册" tabindex="4" /></p>
</form>