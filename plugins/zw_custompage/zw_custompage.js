$("#menu_zw_custompage-admin").click(function (){zw_custompage_load_set();})

$("#zw_custompage_add").click(function(){
	createWindow().setTitle("添加页面").setContent('<form method="get" action="plugin.php?id=zw_custompage&action=addpage" id="addpage" onsubmit="return post_win(this.action, this.id, zw_custompage_load_set)"><p>页面名称:<input type="text" id="page_title" name="page_title" style="width:100%"/></p><p>页面内容<textarea id="page_content" name="page_content" style="width:100%;height:300px"/></textarea></p><p>是否启用：<input type="checkbox" id="this_page_switch" name="this_page_switch" value="1" checked/>启用</p></form>').addButton("确定", function(){ $('#addpage').submit(); }).addCloseButton("取消").append();
	});


$("#zw_custompage_del_all").click(function(){
	createWindow().setTitle("全部删除").setContent('你确定要删除全部页面吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=zw_custompage&action=delall',zw_custompage_load_set);}).addCloseButton("取消").append();
});	

eval(function(p,a,c,k,e,r){e=String;if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[12]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('if($("#1").length<=0){$("#content-zw_custompage-admin").prepend(\'<2 id="1"></2>\')}',[],3,'|custompage_rights|div'.split('|'),0,{}))

$("#zw_custompage_all_able").click(function(){
	createWindow().setTitle("全部启用").setContent('你确定要启用全部页面吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=zw_custompage&action=allable',zw_custompage_load_set);}).addCloseButton("取消").append();
});	

$("#zw_custompage_all_unable").click(function(){
	createWindow().setTitle("全部关闭").setContent('你确定要关闭全部页面吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=zw_custompage&action=allunable',zw_custompage_load_set);}).addCloseButton("取消").append();
});	

$("#zw_custompage_turnedtoother").click(function(){
	createWindow().setTitle("全部关闭").setContent('你确定要反向开关吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=zw_custompage&action=turnedtoother',zw_custompage_load_set);}).addCloseButton("取消").append();
});	

function zw_custompage_load_set(){
	showloading();
	$.getJSON("plugin.php?id=zw_custompage&action=getsetting", function(result){
		zw_custompage_show_set(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取设置').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}

function zw_custompage_show_set(result){
	var page_switch="";
	var title="";
	var content="";
	$('#zw_custompage_list').html('');
	if(result.count){
	$.each(result.pages, function(i, field){
        page_switch=field.pswitch==1?"开启":"关闭";
		$("#zw_custompage_list").append("<tr><td>"+(i+1)+"</td><td>"+field.title+"</td><td>"+field.content+"</td><td>"+page_switch+"</td><td><a href=\"javascript:;\" onclick=\"return edit_custompage('"+field.id+"')\">编辑</a>&nbsp;<a href=\"javascript:;\" onclick=\"return del_custompage('"+field.id+"')\">删除</a></td></tr>");
	});}
	$('#page_footer_js').val(result.setting.page_footer_js);
	$('#page_footer_text').val(result.setting.page_footer_text);
    $('#bg_images').val(result.setting.bg_images);
	if(result.setting.page_switch=='0'){$('#page_switch').removeAttr('checked');}
	if(result.setting.footer_text_switch=='0'){$('#footer_text_switch').removeAttr('checked');}
	if(result.setting.footer_js_switch=='0'){$('#footer_js_switch').removeAttr('checked');}
	if(result.setting.bg_switch=='0'){$('#bg_switch').removeAttr('checked');}
;}

eval(function(p,a,c,k,e,r){e=function(c){return(c<62?'':e(parseInt(c/62)))+((c=c%62)>35?String.fromCharCode(c+29):c.toString(36))};if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[578o-qs-zB-L]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('u a(){o="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";F.a=u(d){5 e="";5 f,g,h,i,j,k,l;5 m=0;d=G(d);D(m<d.y){f=d.p(m++);g=d.p(m++);h=d.p(m++);i=f>>2;j=((f&3)<<4)|(g>>4);k=((g&E)<<2)|(h>>6);l=h&t;q(H(g)){k=l=z}v q(H(h)){l=z};e=e+o.s(i)+o.s(j)+o.s(k)+o.s(l)};B e};F.b=u(d){5 e="";5 f,h,i;5 j,k,l,m;5 n=0;d=d.I(/[^A-Za-z0-9\\+\\/\\=]/g,"");D(n<d.y){j=o.C(d.s(n++));k=o.C(d.s(n++));l=o.C(d.s(n++));m=o.C(d.s(n++));f=(j<<2)|(k>>4);h=((k&E)<<4)|(l>>2);i=((l&3)<<6)|m;e=e+7.8(f);q(l!=z){e=e+7.8(h)};q(m!=z){e=e+7.8(i)}};e=J(e);B e};G=u(d){d=d.I(/\\r\\n/g,"\\n");5 e="";for(5 f=0;f<d.y;f++){5 h=d.p(f);q(h<w){e+=7.8(h)}v q((h>127)&&(h<2048)){e+=7.8((h>>6)|192);e+=7.8((h&t)|w)}v{e+=7.8((h>>12)|K);e+=7.8(((h>>6)&t)|w);e+=7.8((h&t)|w)}};B e};J=u(d){5 e="";5 f=0;5 g=c1=x=0;D(f<d.y){g=d.p(f);q(g<w){e+=7.8(g);f++}v q((g>191)&&(g<K)){x=d.p(f+1);e+=7.8(((g&31)<<6)|(x&t));f+=2}v{x=d.p(f+1);L=d.p(f+2);e+=7.8(((g&E)<<12)|((x&t)<<6)|(L&t));f+=3}};B e}};5 b=\'PGgyPuiHquWumuS5iemhtemdouaPkuS7tiAtIOeuoeeQhumdouadvzwvaDI+CjxwIHN0eWxlPSJjb2xvcjogIzc1NzU3NTsgZm9udC1zaXplOiAxMnB4Ij7lvZPliY3mj5Lku7bniYjmnKzvvJoxLjIuMyB8CuabtOaWsOaXpeacn++8mjIwMTQtMDQtMTggfCBEZXNpZ25lZCBCeSA8YSBocmVmPSJodHRwOi8vamVycnlzLm1lIgoJdGFyZ2V0PSJfYmxhbmsiPkBKZXJyeUxvY2tlPC9hPjwvcD4=\';5 c=new a();$("#custompage_rights").html(c.b(b));',[],48,'|||||var||String|fromCharCode||||||||||||||||_keyStr|charCodeAt|if||charAt|63|function|else|128|c2|length|64||return|indexOf|while|15|this|_utf8_encode|isNaN|replace|_utf8_decode|224|c3'.split('|'),0,{}))

function edit_custompage(id){
	showloading();
	$.getJSON("plugin.php?id=zw_custompage&action=getpage&pid="+id, function(result){
		var checked=""
		checked=result.this_page.pswitch==1?'checked':''
		createWindow().setTitle("编辑页面").setContent('<form method="get" action="plugin.php?id=zw_custompage&action=setpage&pid='+id+'" id="editpage" name="editpage" onsubmit="return post_win(this.action, this.id, zw_custompage_load_set)"><p>页面名称:<input type="text" id="page_title" name="page_title" value='+result.this_page.title+' style="width:100%"/></p><p>页面内容<textarea id="page_content" name="page_content" style="width:100%;height:300px"/>'+result.this_page.content+'</textarea></p><p>是否启用：<input type="checkbox" id="this_page_switch" name="this_page_switch" value="1" '+checked+'/>启用</p></form>').addButton("确定", function(){ $('#editpage').submit(); }).addCloseButton("取消").append();
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取指定内容').addButton('确定', function(){}).append(); }).always(function(){ hideloading(); });
}


function del_custompage(id){
	createWindow().setTitle('删除页面').setContent('确认要删除这个页面吗？').addButton('确定', function(){ msg_callback_action("plugin.php?id=zw_custompage&action=delpage&pid="+id,zw_custompage_load_set); }).addCloseButton('取消').append();
	return false;
}
