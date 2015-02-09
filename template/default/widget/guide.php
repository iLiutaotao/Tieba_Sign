<?php
if(!defined('IN_KKFRAME')) exit();
?>
<h2>贴吧签到助手 配置向导</h2>
<div id="guide_pages">
<div id="guide_page_1">
<p>Hello，欢迎使用 贴吧签到助手~</p><br>
<p><b>这是一款免费软件，作者 <a href="http://www.ikk.me" target="_blank">kookxiang</a></b></p>
<p>如果有人向您兜售本程序，麻烦您给个差评。</p><br>
<p>配置签到助手之后，我们会在每天的 0:30 左右为您自动签到。</p>
<p>签到过程不需要人工干预。</p><br>
<p>准备好了吗？点击下面的“下一步”按钮开始配置吧</p>
<p class="btns"><button class="btn submit" onclick="$('#guide_page_1').hide();$('#guide_page_2').show();">下一步 &raquo;</button></p>
</div>
<div id="guide_page_2" class="hidden">
<p>首先，你需要绑定你的百度账号。</p><br>
<p>为了确保账号安全，我们只储存你的百度 Cookie，不会保存你的账号密码信息。</p>
<p>你可以通过修改密码的方式来让这些 Cookie 失效。</p><br>
<form method="post" action="api.php?action=baidu_login" target="_blank">
<p>百度通行证：<input type="text" name="username" placeholder="推荐使用邮箱登陆" required value="" /></p>
<p>通行证密码：<input type="password" name="password" placeholder="百度通行证密码" required value="" /></p>
<p><input type="submit" value="绑定百度账号" /> <a href="<?php echo cloud::get_api_path(); ?>manual_bind.php?sid=<?php echo cloud::id(); ?>&formhash=<?php echo $formhash; ?>" class="btn" target="_blank">手动绑定</a></p>
</form>
</div>
<div id="guide_page_manual" class="hidden"></div>
<div id="guide_page_3" class="hidden">
<p>一切准备就绪~</p><br>
<p>我们已经成功接收到你百度账号信息，自动签到已经准备就绪。</p>
<p>您可以点击 <a href="#setting">高级设置</a> 更改邮件设定，或更改其他附加设定。</p><br>
<p>感谢您的使用！</p><br>
<p>程序作者：kookxiang (<a href="http://www.ikk.me" target="_blank">http://www.ikk.me</a>)</p>
<p>提供方：LiuJiantao (<a href="http://www.liujiantao.me" target="_blank">http://www.liujiantao.me</a>)</p>
<p>建议您30天后来更新您的百度账号cookie
<script type="text/javascript">
var __qqClockShare = {
   content: "您的贴吧签到助手http://sign.liujiantao.me/百度账号cookie已经失效，请重新登陆",
   time: "<?php echo date('Y-m-d H:m',time()+30*24*3600); ?>",
   advance: 5,
   url: "http://sign.liujiantao.me/",
   icon: "2_1"
};
document.write('<a href="http://qzs.qq.com/snsapp/app/bee/widget/open.htm#content=' + encodeURIComponent(__qqClockShare.content) +'&time=' + encodeURIComponent(__qqClockShare.time) +'&advance=' + __qqClockShare.advance +'&url=' + encodeURIComponent(__qqClockShare.url) + '" target="_blank"><img src="http://i.gtimg.cn/snsapp/app/bee/widget/img/' + __qqClockShare.icon + '.png" style="border:0px;"/></a>');
</script></p>
</div>
</div>