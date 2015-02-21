<?php
if(!defined('IN_KKFRAME')) exit('Access Denied!');
?>
<div id="authmail_rights">
<h2>著微邮箱验证插件 - 管理面板</h2>
<p style="color: #757575; font-size: 12px">当前插件版本：1.1.3 |
更新日期：2014-4-18 | Coded By <a href="http://jerrys.me"
	target="_blank">@Jerry Locke</a></p>
</div>

<h2>Tips</h2>
<p>
1.本页面只有管理员才可以看到<br>
2.每次访问本页面，请清除一次authcode已经过期的记录<br>
3.使用本插件，请确保您的邮件发送方式没有问题，<a href='/admin.php#setting'>点此设置</a>(设置完成后请测试发送)，建议使用插件包内的“著微邮件API插件”。
</p>
<br>
<h2>待验证用户管理</h2>
<table>
	<thead>
		<tr>
			<td style="width: 2%">#</td>
			<td style="width: 15%">用户名</td>
			<td style="width: 20%">验证邮箱</td>
			<td style="width: 30%">验证密钥</td>
			<td style="width: 20%">注册时间</td>
			<td style="width: 15%">操作</td>
		</tr>
	</thead>
	<tbody id="zw_mailauth_list"></tbody>
</table>
<p>
<a class="btn" href="javascript:;" id="zw_mailauth_del_all">全部删除</a>
<a class="btn" href="javascript:;" id="zw_mailauth_all_resend"	style="margin-left: 5px">全部重发</a>
<a class="btn" href="javascript:;"	id="zw_mailauth_all_pass" style="margin-left: 5px">全部通过</a>
<a class="btn" href="javascript:;"	id="zw_mailauth_clear" style="margin-left: 5px">清除失效</a>
</p>

<br>

<h2>设置</h2>
<form method="post"	action="plugin.php?id=zw_mailauth&action=savesetting" id="zw_mailauth_settings" onsubmit="return post_win(this.action, this.id,zw_mailauth_load_set)">
<p>
<input type="checkbox" id="mailaddrepeat" name="mailaddrepeat" value="1" checked />禁止单邮箱重复注册&nbsp;&nbsp;验证链接有效时长：<input type='text' id='deathtime' maxlength="4" name='deathtime'  style='width:60px' onkeyup="this.value=this.value.replace(/[^\d]/g,'') " onafterpaste="this.value=this.value.replace(/[^\d]/g,'') " />分钟</p>
<p>
邮件标题：
<input type='text' id='title' name='title' style='width:100%' maxlength='200' />
</p>
<p>
邮件模板（<a href='javascript:;' onclick='resetFomat()'>点此恢复默认</a>）：
<textarea id='format' name='format' style='width:100%'></textarea>
</p>
<p>
允许注册的邮箱域名，一行一个，只需填写@后面的域名，<br>例如：jerrys.me（留空为不限制）
<textarea id='abledomain' name='abledomain' rows='3' style='width:100%;height=100px;'></textarea>
</p>
<p>
禁止注册的邮箱域名，一行一个，只需填写@后面的域名，<br>例如：qq.com（留空为不限制）
<textarea id='unabledomain' name='unabledomain' rows='3' style='width:100%;height=100px;'></textarea>
</p>
<p>
禁止注册的邮箱地址，一行一个，<br>例如：example@example.com（留空为不限制）
<textarea id='unableaddress' name='unableaddress' rows='3' style='width:100%;height=100px;'></textarea>
</p>
<p>
您可以在邮件模板中使用以下标记以插入特定内容：<br>
{authlink} : 验证链接（必须）<br>
{username} : 用户名<br>
{deathtime} : 有效时间（分钟）<br>
{sendtime} ： 发送时间
<script>
var format='<p>尊敬的{username}，您好！</p><p>    感谢您注册贴吧签到助手，请在{deathtime}分钟内点击下面的链接以激活您的帐号：</p><p>    激活链接：{authlink}</p><p>如果您没有注册却收到本邮件，请忽略。</p><p align="right">贴吧签到助手<br>{sendtime}</p>';
function resetFomat(){
document.getElementById('format').value=format;
}
</script></p>
<input type="submit" value="保存设置"></form>
