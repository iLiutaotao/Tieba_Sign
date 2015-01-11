<?php
if (! defined ( 'IN_KKFRAME' ))	exit ( 'Access Denied!' );
echo '<h2>妹纸认证</h2><p style="color:#757575;font-size:12px">当前插件版本：0.1.3_fixed | 更新日期：2014-04-06 | Designed By <a href="http://tieba.baidu.com/home/main?un=%D0%C7%CF%D2%D1%A9&fr=index" target="_blank">@星弦雪</a></p>';
?>
<style type="text/css">
select:active,select:focus,input,input:active,input:focus{outline:none!important}
.x_mz_tab{border:2px solid #2c3e50;border-radius:8px;margin-bottom:20px;width:99%;box-shadow:0 0 5px rgba(0,0,0,0.5);}
.x_mz_tab_title{background-color:#2c3e50;}
.x_mz_tab_title ul{width:100%;height:40px}
.x_mz_tab_title li{float:left;font-size:1.2em;height:40px;border-top-left-radius:8px;border-top-right-radius:8px}
.x_mz_tab_title li:hover{background:-webkit-linear-gradient(top,#2c3e50,#ffffff)}
.x_mz_tab_title li a{padding:10px 15px 10px 15px;display:block;color:white}
.x_mz_tab_title_selected,.x_mz_tab_title_selected:hover{background:white!important}
.x_mz_tab_title_selected a{color:#2c3e50!important;cursor:default!important}
.x_mz_tab_title a:hover{text-decoration:none!important;cursor:pointer}
.x_mz_tab_content{padding:15px;}
.x_mz_tab_content a:hover{cursor:pointer}
table.x_mz_table thead tr{background-color:#dedede;}
@media (max-width: 382px){
	.x_mz_tab_title ul{height:50px}
	.x_mz_tab_title li{width:33%;height:50px}
	.x_mz_tab_title li a{padding:5px 15px 5px 15px}
}
</style>
<p>添加小号来为指定的号投票（可选择妹纸、伪娘、人妖），每4.5小时投票一次</p>
<div class="x_mz_tab">
<div class="x_mz_tab_title">
<ul>
	<li class="x_mz_tab_title_selected"><a>ID管理</a></li><li><a>投票测试</a></li><li><a>投票记录</a></li>
</ul>
</div>
<div class="x_mz_tab_content">
<p class="x_mz_tab_content_title">添加被投票的ID：</p>
<table class="x_mz_table">
	<thead><tr><td style="width:20px">序号</td><td>ID</td><td>投票类型</td><td>认证状态</td><td>操作</td></tr></thead>
	<tbody id="x_mz_vote_for">
		<tr><td colspan="5"><img src="./style/loading.gif">载入中请稍后</td></tr>
	</tbody>
</table>
<p>
	<a class="btn" id="x_mz_add_voteid_a">添加ID</a>
</p>
<p class="x_mz_tab_content_title">添加投票的ID：</p>
<table class="x_mz_table">
	<thead><tr><td style="width: 20px">序号</td><td>ID</td><td>状态</td><td>操作</td></tr></thead>
	<tbody id="x_mz_vote_to"><tr><td colspan="4"><img src="./style/loading.gif">载入中请稍后</td></tr></tbody>
</table>
<p>
	<a class="btn" id="x_mz_add_voteid_b">添加ID</a>
</p>
</div>

<div class="x_mz_tab_content">
	<p>随机进行一次投票测试，检查你的设置有没有问题
	<a href="plugins/x_meizi/ajax.php?v=test_vode" class="btn"	onclick="return msg_win_action(this.href)">测试投票</a>
	</p>
</div>

<div class="x_mz_tab_content">
<h2 id="x_mz_log_title">投票记录</h2>
<p id="x_mz_pager_text"></p>
<table class="x_mz_table">
	<thead><tr><td style="width: 20px">序号</td><td>ID</td><td>成功</td><td>失败</td></tr></thead>
	<tbody id="x_mz_log"><tr><td colspan="5"><img src="./style/loading.gif">载入中请稍后</td></tr></tbody>
</table>

</div></div>