<?php
if(!defined('IN_KKFRAME')) exit('Access Denied!');
?>
<div id="custompage_rights">
<h2>著微自定义页面插件 - 管理面板</h2>
<p style="color: #757575; font-size: 12px">当前插件版本：1.2.2 |
更新日期：2014-03-24 | Designed By <a href="http://jerrys.me"
	target="_blank">@JerryLocke</a></p>
</div>

<h2>Tips</h2>
<p>1.本页面只有管理员才可以看到，在此页面 <b><u> 修改设置后需要刷新浏览器才能看到效果</u></b>
（建议Ctrl+F5强制刷新） <br>
2.部分常用HTML代码： <br>
换行：&lt;br&gt; <br>
空格：&amp;nbsp; <br>
链接：&lt;a href="链接地址" target="_blank"&gt;链接文字&lt;/a&gt;<br>
段落：&lt;p&gt;段落内容&lt;/p&gt; <br>
图片：&lt;img src="图片地址" width="图片宽度" height="图片高度"/&gt; <br>
下划线：&lt;u&gt;加下划线的内容&lt;/u&gt;<br>
加粗：&lt;b&gt;要加粗的内容&lt;/b&gt;</p>

<br>

<h2>页面管理</h2>
<p>可以添加任意数量的页面，支持HTML代码</p>
<table>
	<thead>
		<tr>
			<td style="width: 5%">#</td>
			<td>页面名称</td>
			<td>页面内容</td>
			<td>页面开关</td>
			<td style="width: 20%">操作</td>
		</tr>
	</thead>
	<tbody id="zw_custompage_list"></tbody>
</table>
<p><a class="btn" href="javascript:;" id="zw_custompage_add">添加页面</a> <a
	class="btn" href="javascript:;" id="zw_custompage_del_all"
	style="margin-left: 5px">全部删除</a> <a class="btn" href="javascript:;"
	id="zw_custompage_all_able" style="margin-left: 5px">全部启用</a> <a
	class="btn" href="javascript:;" id="zw_custompage_all_unable"
	style="margin-left: 5px">全部关闭</a> <a
	class="btn" href="javascript:;" id="zw_custompage_turnedtoother"
	style="margin-left: 5px">反向开关</a></p></p>

<br>

<h2>全局设置</h2>
<form method="post"
	action="plugin.php?id=zw_custompage&action=savesetting"
	id="settings" onsubmit="return post_win(this.action, this.id,zw_custompage_load_set)">
<p><input type="checkbox" id="page_switch" name="page_switch" value="1" checked />启用自定义页面&nbsp;&nbsp;<input type="checkbox" id="footer_text_switch" name="footer_text_switch" value="1" checked />启用页底文字&nbsp;&nbsp;<input
	type="checkbox" id="footer_js_switch" name="footer_js_switch" value="1" checked />启用页底代码&nbsp;&nbsp;<input
	type="checkbox" id="bg_switch" name="bg_switch" value="1" checked />启用自定义背景图</p>
<p>页底文字（可以添加文字、超链接等）： <br>
<textarea id="page_footer_text" name="page_footer_text" style="width: 90%"></textarea>
</p>
<p>页底代码（可以添加统计代码、分享代码等）： <br>
<textarea id="page_footer_js" name="page_footer_js" style="width: 90%"></textarea>
</p>
<p>自定义背景图图片地址（只有“启用自定义背景图”为选中时才会生效，每行填写一个地址，打开页面时将随机选取一个地址作为背景图）： <br>
<textarea id="bg_images" name="bg_images" style="width: 90%"></textarea>
</p>
<input type="submit" value="保存设置"></form>
