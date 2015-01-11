$("#menu_x_meizi-index").click(function (){x_meizi_load_contents(this);});
$(".x_mz_tab .x_mz_tab_content").each(function(i){
	$(this).addClass("x_mz_tab_content_"+i);
	if(i!=0) $(this).hide();
});
$("#x_mz_add_voteid_a").click(function(){x_mz_add_voteid_a();});
$("#x_mz_add_voteid_b").click(function(){x_mz_add_voteid_b();});
$(".x_mz_tab .x_mz_tab_title li a").click(function(){
	if($(this).parent().hasClass("x_mz_tab_title_selected")) return 0;
	else{
		$(".x_mz_tab .x_mz_tab_content_"+$(this).parent().siblings().filter(".x_mz_tab_title_selected").index()).slideUp();
		$(this).parent().siblings().filter(".x_mz_tab_title_selected").removeClass("x_mz_tab_title_selected");
		$(".x_mz_tab .x_mz_tab_content_"+$(this).parent().index()).slideDown();
		$(this).parent().addClass("x_mz_tab_title_selected");
		x_meizi_load_contents(this);
	}
});
function x_meizi_load_contents(content){
	if($(content).parent().index()==0) x_mz_set();
	else if($(content).parent().index()==2) load_xmz_log();
/*	else if($(content).parent().index()==1) x_mz_adv_set();*/
}
function x_mz_set(){
	showloading();
	$.getJSON("plugins/x_meizi/ajax.php?v=get_vote_ids", function(result){
		show_x_mz_set(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取设置').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function show_x_mz_set(result){
	$('#x_mz_vote_for').html('');
	$('#x_mz_vote_to').html('');
	if(result.count1){
		$.each(result.a, function(i, field){
		$("#x_mz_vote_for").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/home/main?un="+field.uniname+"\" target=\"_blank\">"+field.name+"</a></td><td>"+field.votetype+"</td><td>"+field.statue+"</td><td><a  onclick=\"return x_mz_refresh('"+field.id+"')\">更新状态</a>&nbsp;&nbsp;<a onclick=\"return delid_a('"+field.id+"')\">删除</a></td></tr>");
	});}else{
		$('#x_mz_vote_for').html('<tr><td colspan="5">暂无记录</td></tr>');
	}
	if(result.count2){
		$.each(result.b, function(i, field){
			if(field.islogin==1) statue='掉线';
			else statue='正常';
		$("#x_mz_vote_to").append("<tr><td>"+(i+1)+"</td><td>"+field.name+"</td><td>"+statue+"</td><td><a  onclick=\"return delid_b('"+field.id+"')\">删除</a></td></tr>");
	});}else{
		$('#x_mz_vote_to').html('<tr><td colspan="5">暂无记录</td></tr>');
	}
}

function x_mz_add_voteid_a(){
	createWindow().setTitle("添加被投票ID").setContent('<p>请输入被投票ID:</p><form method="get" action="plugins/x_meizi/ajax.php?v=add_vote_id_a" id="x_mz_add_vote_id_a" onsubmit="return post_win(this.action, this.id,function(){x_mz_set();})"><input type="text" name="vote_id" style="width:90%"/><p style="margin-top:10px">投票类型：<select name="vote_type" style="width:120px;height:29px"><option value="1">妹纸</option><option value="2">伪娘</option><option value="3">人妖</option></select></p></form>').addButton("确定", function(){ $('#x_mz_add_vote_id_a').submit(); }).addCloseButton("取消").append();
}

function x_mz_add_voteid_b(){
	showloading();
	$.getJSON("plugins/x_meizi/ajax.php?v=add_vote_id_pre", function(result){
	createWindow().setTitle("添加帖吧").setContent('<form method="get" action="plugins/x_meizi/ajax.php?v=add_vote_id_b" id="x_mz_add_vote_for_id" onsubmit="return x_mz_login_win(this.action, this.id)"><input type="hidden" name="bdvcode_md5" value="'+result.vcodemd5+'"><input type="hidden" name="bdvcode_pre" value="'+result.pre+'"><p>请输入百度ID:</p><input type="text" name="bdid" style="width:100%" value=""><p>请输入密码:</p><input type="password" name="bds" style="width:100%" value=""><p>请输入验证码:</p><p><input type="text" name="tb_vcode" style="width:100px"><img src="'+result.vcodepic+'" style="height: 30px; position: absolute; margin-left: 10px;"></p></form>').addButton("确定", function(){ $('#x_mz_add_vote_for_id').submit(); }).addCloseButton("取消").append();
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法解析返回结果').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
	}
function x_mz_login_win(link, formid){
	link += link.indexOf('?') < 0 ? '?' : '&';
	link += "format=json";
	showloading();
	$.post(link, $('#'+formid).serialize(), function(result){
	if(result.msgx==2||result.msgx==3){
		content='<form method="get" action="plugins/x_meizi/ajax.php?v=add_vote_id_b" id="x_mz_add_id_form_re" onsubmit="return x_mz_login_win(this.action, this.id)"><input type="hidden" name="bdvcode_md5" value="'+result.vcodemd5+'"><input type="hidden" name="bdvcode_pre" value="'+result.pre+'"><p>请输入百度ID:</p><input type="text" name="bdid" style="width:100%" value="'+result.bdid+'"><p>请输入密码:</p><input type="password" name="bds" style="width:100%" value="'+result.bds+'"><p>请输入验证码:</p><p><input type="text" name="tb_vcode" style="width:100px"><img src="'+result.vcodepic+'" style="height: 30px; position: absolute; margin-left: 10px;"></p></form>';
		if(result.msgx==3) content='<p style="color:red">'+result.msg+'</p>'+content;
		createWindow().setTitle('系统消息').setContent(content).addButton('确定', function(){$('#x_mz_add_id_form_re').submit();}).append();
	}else{
		createWindow().setTitle('系统消息').setContent(result.msg).addButton('确定', function(){if(result.msgx==1) x_mz_set();}).append();
		}
	}, 'json').fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法解析返回结果').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
	return false;
}
function delid_a(id){
	createWindow().setTitle('删除ID').setContent('确认要删除这个ID吗？').addButton('确定', function(){ msg_callback_action("plugins/x_meizi/ajax.php?v=delid_a&id="+id,x_mz_set); }).addCloseButton('取消').append();
}
function delid_b(id){
	createWindow().setTitle('删除ID').setContent('确认要删除这个ID吗？').addButton('确定', function(){ msg_callback_action("plugins/x_meizi/ajax.php?v=delid_b&id="+id,x_mz_set); }).addCloseButton('取消').append();
}
function x_mz_refresh(id){
	msg_callback_action("plugins/x_meizi/ajax.php?v=mz_refres&id="+id,x_mz_set);
}
function load_xmz_log(){
	showloading();
	$.getJSON("plugins/x_meizi/ajax.php?v=get_log", function(result){
		show_xmz_log(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取投票记录').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function load_xmz_history(date){
	showloading();
	$.getJSON("plugins/x_meizi/ajax.php?v=get_history&date="+date, function(result){
		show_xmz_log(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取投票记录').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}
function show_xmz_log(result){
	if(!result || result.count == 0){
		$('#x_mz_log').html('<tr><td colspan="5">暂无记录</td></tr>');
		return;
	}
	$('#x_mz_log').html('');
	$('#x_mz_log_title').html(result.date+" 投票记录");
	$.each(result.log, function(i, field){
		$("#x_mz_log").append("<tr><td>"+(i+1)+"</td><td>"+field.name+"</td><td>"+field.success+"</td><td>"+field.failed+"</td></tr>");
	});
	var pager_text = '';
	if(result.before_date) pager_text += '<a class="btn" onclick="return load_xmz_history('+result.before_date+')">&laquo; 前一天</a>';
	pager_text += '<a class="btn" onclick="load_xmz_log()">今天</a>';
	if(result.after_date) pager_text += '<a class="btn" onclick="return load_xmz_history('+result.after_date+')">后一天 &raquo;</a>';
	$('#x_mz_pager_text').html(pager_text);
}