<?php
if(!defined('IN_KKFRAME')) exit('Access Denied!');
$obj = $_PLUGIN['obj']['fsql_zan'];
$limit=$obj->getSetting('limit');
if($limit!=0){$limit="（".$limit."个吧以内）";}else{$limit="（无数量限制）";}
?>
<style type="text/css">
.small_gray{color:#757575;font-size:12px;}
.small_gray_i{color:#B1B1B1;font-size:12px;font-style:italic;margin:0 0 2em 0;}
.fsql-nav-tabs {
  border-bottom: 1px solid #ddd;
  list-style: none;
  padding: 0;
  margin: 0 0 20px 0;
  height:31px
}
.fsql-nav-tabs > li {
  margin-bottom: -1px;float: left;  line-height: 20px;
}
.fsql-nav-tabs > li > a:hover,
.fsql-nav-tabs > li > a:focus {
  border-color: #eeeeee #eeeeee #dddddd;
  cursor:pointer;
}
.fsql-nav-tabs > .active > a,
.fsql-nav-tabs > .active > a:hover,
.fsql-nav-tabs > .active > a:focus {
  color: #555555;
  cursor: default;
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-bottom-color: transparent;
}
.fsql-nav-tabs > li > a {
  padding: 8px 12px 8px 12px;
  display: block;
  margin-right: 2px;
  line-height: 14px;
  border: 1px solid transparent;
  border-radius: 4px 4px 0 0;
}
.fsql-nav-tabs > li > a:hover,
.fsql-nav-tabs > li > a:focus {
  border-color: #eeeeee #eeeeee #dddddd;
  text-decoration: none;
  background-color: #eeeeee;
}
.fsql-nav-tabs:before,
.fsql-nav-tabs:after{
  display: table;
  line-height: 0;
  content: "";
}
.fsql-nav-tabs:after{
  clear: both;
}
table.fsql_table thead tr{background-color:#dedede;}
</style>
<h2>云点赞</h2>
<p class="small_gray">当前插件版本：1.0 | 更新日期：14-06-16 | Reorganized By <a href="http://tieba.baidu.com/home/main?id=a969467265657a696e6754696e793021&fr=index" target="_blank">@FreezingTiny</a></p>
<div>
	<ul class="fsql-nav-tabs">
		<li class="fsql_active"><a>设置</a></li><li><a>记录</a></li><li><a>帮助</a></li>
	</ul>
</div>

<div class="fsql_tab_content">

<div>
<h3>测试</h3>
<p>随机选取一个吧，进行一次【10】秒左右的点赞测试，检查你的设置有没有问题</p>
<p><a href="plugin.php?id=fsql_zan&action=test_zan" class="btn"	onclick="return msg_win_action(this.href)">点赞测试</a></p>
<h3>添加需要点赞的贴吧<?php echo $limit;?></h3>
<table class="f_table">
	<thead><tr><td style="width:20px">序号</td><td>贴吧</td><td style="width: 20%">操作</td></tr></thead>
	<tbody id="fsql_zan_show">
		<tr><td colspan="4"><img src="./template/default/style/loading.gif?version=1.14.6.2">载入中...请稍候</td></tr>
	</tbody>
</table>
<p>
	<a class="btn" id="f_z_add_tb"	style="margin-left: 5px">添加贴吧</a>
	<a class="btn" id="f_z_del_tid"	style="margin-left: 5px">全部删除</a>
</p>
</div>


<div>
<h3>记录</h3>
	<h2 id="f_z_zan_log_tite">当天的点赞记录</h2>
    <p>注：点赞次数不等于点赞的帖子数，同一帖子重复点赞也被计入。</p>
    <p id="f_z_pager_text"></p>
<table class="f_table">
	<thead><tr><td style="width: 20px">序号</td><td>贴吧</td><td style="width: 90px">单日点赞次数</td></tr></thead>
	<tbody id="f_z_log_tab"><tr><td colspan="5"><img src="./template/default/style/loading.gif?version=1.14.6.2">载入中...请稍后</td></tr></tbody>
</table>
</div>


<div>
<h3>提示：</h3>
	<p>慎用该插件！可能会被吧务封禁。</p>
	<h2>关于使用:</h2>
	<p>1.添加贴吧后，第二天开始自动点赞</p>
    <p>2.点赞范围是该吧客户端首页的帖子，即坟贴不会被赞。</p>
    <p>3.因占用服务器带宽较高，请不要添加过多的贴吧。</p>
	<h2>声明：</h2>
	<p>使用本插件导致封号，作者概不负责。（也负不了责，我又没能力给你解封）</p>
</div>
</div>