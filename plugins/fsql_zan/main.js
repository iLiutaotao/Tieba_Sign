$("#menu_fsql_zan-index").click(function (){
if($(".fsql-nav-tabs >.fsql_active").index()==0) load_zan_set();
else if($(".fsql-nav-tabs >.fsql_active").index()==1) load_zan_log();
});
$(".fsql_tab_content>div").each(function(i){
$(this).addClass("fsql_tab_content_"+i);
if(i!=0) $(this).hide();
});
$(".fsql-nav-tabs >li>a").click(function(){
if($(this).parent().hasClass("fsql_active")) return 0;
else{
$(".fsql_tab_content>.fsql_tab_content_"+$(this).parent().siblings().filter(".fsql_active").index()).hide();
$(this).parent().siblings().filter(".fsql_active").removeClass("fsql_active");
$(".fsql_tab_content>.fsql_tab_content_"+$(this).parent().index()).show();
$(this).parent().addClass("fsql_active");
if($(this).parent().index()==0)  load_zan_set();
else if($(this).parent().index()==1) load_zan_log();
}
});
$("#f_z_add_tb").click(function(){
createWindow().setTitle("添加帖吧").setContent('<p>你可以添加要自动点赞的贴吧</p><p>请输入帖吧的名字（不要带“吧”字）:</p><p>例如:要添加chrome吧，请输入chrome</p><form method="get" action="plugin.php?id=fsql_zan&action=add-tieba" id="fsql_zan_add_tb_form" onsubmit="return post_win(this.action, this.id,f_reload)"><input type="text" id="fsql_zan_add_tieba" name="fsql_zan_add_tieba" style="width:90%"/></form>').addButton("确定", function(){ $('#fsql_zan_add_tb_form').submit(); }).addCloseButton("取消").append();
});
$("#f_z_del_tid").click(function(){
createWindow().setTitle("批量删除").setContent('你确定要删除全部贴吧吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=fsql_zan&action=del-all-tid',f_reload);}).addCloseButton("取消").append();
});
function f_reload(){
load_zan_set();
}
function load_zan_set(){
showloading();
$.getJSON("plugin.php?id=fsql_zan&action=zan-settings", function(result){
show_zan_set(result);
}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取设置').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function show_zan_set(result){
$('#fsql_zan_show').html('');
if(result.count1){
$.each(result.tiebas, function(i, field){
$("#fsql_zan_show").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/f?kw="+field.unicode_name+"\" target=\"_blank\">"+field.name+"</a></td><td><a href=\"javascript:;\" onclick=\"return fsql_delsid('"+field.sid+"')\">删除</a></td></tr>");
});}else{
$('#xxx_post_show').html('<tr><td colspan="4">暂无记录</td></tr>');
}
}
function fsql_delsid(sid){
createWindow().setTitle('删除帖子').setContent('确认要删除这个帖子的云点赞吗？').addButton('确定', function(){ msg_callback_action("plugin.php?id=fsql_zan&action=delsid&sid="+sid,f_reload); }).addCloseButton('取消').append();
return false;
}
function load_zan_log(){
showloading();
$.getJSON("plugin.php?id=fsql_zan&action=zan-log", function(result){
show_zan_log(result);
}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取回帖报告').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function load_zan_history(date){
showloading();
$.getJSON("plugin.php?id=fsql_zan&action=zan-history&date="+date, function(result){
show_zan_log(result);
}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取签到报告').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function show_zan_log(result){
if(!result || result.count == 0){
$('#f_z_log_tab').html('<tr><td colspan="5">暂无记录</td></tr>');
return;
}
$('#f_z_log_tab').html('');
$('#f_z_zan_log_tite').html(result.date+" 点赞记录");
$.each(result.log, function(i, field){
$("#f_z_log_tab").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/f?kw="+field.unicode_name+"\" target=\"_blank\">"+field.name+"</a></td><td>"+field.count+"</td></tr>");
});
var pager_text = '';
if(result.before_date) pager_text += '<a class="btn" onclick="return load_zan_history('+result.before_date+')">&laquo; 前一天</a>';
pager_text += '<a class="btn" onclick="load_zan_log()">今天</a>';
if(result.after_date) pager_text += '<a class="btn" onclick="return load_zan_history('+result.after_date+')">后一天 &raquo;</a>';
$('#f_z_pager_text').html(pager_text);
}