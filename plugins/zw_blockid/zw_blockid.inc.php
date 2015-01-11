<div id="blockid_rights">
<h2>循环封禁</h2>
	<p style="color: #757575; font-size: 12px">
		本插件可以每天定时对指定贴吧的指定ID进行封禁操作。前提为您绑定的百度ID有指定贴吧的大3或小3权限。
		<br>
		当前插件版本：1.2.9 更新日期：2014年06月22日 |&nbsp;&nbsp;作者:
		<a href="http://jerrys.me" target="_blank">@JerryLocke</a>&nbsp;&nbsp;
		感谢:
		<a href="http://www.baidu.com/p/%E6%98%9F%E5%BC%A6%E9%9B%AA" target="_blank">@星弦雪</a>&nbsp;提供的Cron Wiki
	</p>
</div>

<br>
<h2>友情提醒</h2>
<p>对于被封禁用户，如果不是犯了非常严重的错误，请不要使用本插件对其进行循环封禁，给其一个改正的机会。若被封禁用户实在可恶、罪不可赦（如爆吧），那么使用本插件是对其最好的惩罚。</p>

<br>
<h2>封禁列表</h2>
<p>以下ID系统将会每天自动封禁：</p>
<table>
	<thead>
		<tr>
			<td style="width: 5%">#</td>
			<td>所在贴吧</td>
			<td>封禁ID</td>
			<td style="width: 20%">操作</td>
		</tr>
	</thead>
	<tbody id="zw_blockid-list"></tbody>
</table>
<p>
	<a class="btn" href="javascript:;" id="zw_blockid-add">添加封禁</a>
	<a class="btn" href="javascript:;" id="zw_blockid-add-batch">批量添加</a>
	<a class="btn" href="javascript:;" id="zw_blockid-del-all">全部删除</a>
</p>

<br>
<h2 id='zw_blockid-history'>封禁记录</h2>
<span id="zw_blockid-log-flip" class="float-right"><a href="javascript:zw_blockid_show_log();">« 前一天</a></span>
<p id="zw_blockid-log-stat">共要封禁 0 个ID, 成功封禁 0 个ID</p>
<table>
	<thead>
		<tr>
			<td style="width: 5%">#</td>
			<td>所在贴吧</td>
			<td>封禁ID</td>
			<td style="width: 20%">封禁情况</td>
		</tr>
	</thead>
	<tbody id="zw_blockid-log"></tbody>
</table>

<br>
<h2>设置</h2>
<p>
<form method="post" action="plugin.php?id=zw_blockid&action=setting" id="zw_blockid-setting" onsubmit="return post_win(this.action, this.id, zw_blockid_load_set)">
<input type="checkbox" id="zw_blockid-report" name="zw_blockid-report" value="1" /> <label for="zw_block-report">当天有封禁失败的记录时发邮件告知我</label>
</p>
<input type="submit" value="保存设置"></form>
</form>
<br>

<h2>封禁测试</h2>
<p>随机选取一条信息，进行一次封禁测试，检查你的设置有没有问题</p>
<p>
	<a href="javascript:msg_win_action('plugin.php?id=zw_blockid&action=test-blockid');" class="btn">测试封禁</a>
</p>