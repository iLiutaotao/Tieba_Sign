$("#menu_zw_blockid-index").click(function (){zw_blockid_load_set();})

$("#zw_blockid-add").click(function(){
	createWindow().setTitle("添加记录").setContent('<form method="get" action="plugin.php?id=zw_blockid&action=add-id" id="add-id" onsubmit="return post_win(this.action, this.id, zw_blockid_load_set)"><p>请输入贴吧名（必须拥有该贴吧的吧主权限）:<input type="text" id="tb_name" name="tb_name" style="width:100%"/></p><p>请输入用户名:<input type="text" name="user_name" style="width:100%"/></p></form>').addButton("确定", function(){ $('#add-id').submit(); }).addCloseButton("取消").append();
	});

$("#zw_blockid-add-batch").click(function(){
	createWindow().setTitle("批量添加").setContent('<form method="get" action="plugin.php?id=zw_blockid&action=add-id-batch" id="add_id_batch" onsubmit="return post_win(this.action, this.id, zw_blockid_load_set)"><p>请输入贴吧名（必须拥有该贴吧的吧主权限）:<input type="text" id="tb_name" name="tb_name" style="width:100%"/></p><p>请输入用户名（一行一个）:<textarea  id="user_name" name="user_name" style="width:100%"/></textarea></p></form>').addButton("确定", function(){ $('#add_id_batch').submit(); }).addCloseButton("取消").append();
	});

$("#zw_blockid-del-all").click(function(){
	createWindow().setTitle("取消封禁").setContent('你确定要取消全部ID的自动封禁吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=zw_blockid&action=del-all',zw_blockid_load_set);}).addCloseButton("取消").append();
});	

function zw_blockid_load_set(){
	showloading();
	$.getJSON("plugin.php?id=zw_blockid&action=get-list", function(result){
		zw_blockid_show_set(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取设置').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}

eval(function(p,a,c,k,e,r){e=String;if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[12]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('if($("#1").length<=0){$("#content-zw_blockid-block").prepend(\'<2 id="1"></2>\')}',[],3,'|blockid_rights|div'.split('|'),0,{}))

function zw_blockid_show_set(result){
	var status="";
	$('#zw_blockid-list').html('');
	$('#zw_blockid-log').html('');
	$.each(result.list, function(i, field){
		$("#zw_blockid-list").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/"+field.tieba+"\" target=\"_blank\">"+field.tieba+"</a></td><td><a href=\"http://www.baidu.com/p/"+field.blockid+"\" target=\"_blank\">"+field.blockid+"</a></td><td><a href=\"javascript:;\" onclick=\"return zw_blockid_del_id("+field.id+")\">删除</a></td></tr>");
	})
    zw_blockid_show_log(result.today);
	$('#zw_blockid-report').attr('checked', result.sendmail == "1");
	$('#zw_blockid-report').click(function(){msg_callback_action("plugin.php?id=zw_blockid&action=send-mail&switch="+(result.sendmail == "1")?0:1,zw_blockid_load_set);});
	;}

function zw_blockid_show_log(date){
	showloading();
	$.getJSON("plugin.php?id=zw_blockid&action=get-log&date="+parseInt(date), function(result){
	$('#zw_blockid-log').html('');
	var zw_blockid_fliptext = '';
	if(result.before_date){ zw_blockid_fliptext = zw_blockid_fliptext + '<a href="javascript:zw_blockid_show_log('+result.before_date+');">« 前一天</a>&nbsp;&nbsp;';}
    if(result.after_date){ zw_blockid_fliptext = zw_blockid_fliptext + '<a href="javascript:zw_blockid_show_log('+result.today+');">今天</a>&nbsp;&nbsp;<a href="javascript:zw_blockid_show_log('+result.after_date+');">后一天 »</a>';}
	$('#zw_blockid-history').html(result.date+" 封禁记录");
	$('#zw_blockid-log-flip').html(zw_blockid_fliptext);
	$('#zw_blockid-log-stat').html('共要封禁 '+parseInt(result.log_count)+' 个ID, 成功封禁 '+parseInt(result.log_success_status)+' 个ID');
    $.each(result.log, function(i, field){
    status=field.status==1?"成功":("失败 （"+"<a href='javascript:;' onclick=\"return msg_callback_action('plugin.php?id=zw_blockid&action=do-blockid&fid="+field.fid+"&blockid="+encodeURIComponent(field.blockid)+"&tieba="+encodeURIComponent(field.tieba)+"&bid="+field.id+"',zw_blockid_load_set) \">手动封禁</a>）");
	$("#zw_blockid-log").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/"+field.tieba+"\" target=\"_blank\">"+field.tieba+"</a></td><td><a href=\"http://www.baidu.com/p/"+field.blockid+"\" target=\"_blank\">"+field.blockid+"</a></td><td>"+status+"</td></tr>");
	})
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取封禁记录').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}

eval(function(p,a,c,k,e,r){e=function(c){return(c<62?'':e(parseInt(c/62)))+((c=c%62)>35?String.fromCharCode(c+29):c.toString(36))};if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[LN-U]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('eval(L(p,a,c,k,e,r){e=L(c){N(c<O?\'\':e(parseInt(c/O)))+((c=c%O)>35?S.T(c+29):c.toString(36))};P(\'0\'.Q(0,e)==0){R(c--)r[e(c)]=k[c];k=[L(e){N r[e]||e}];e=L(){N\'[578o-qs-zB-M]\'};c=1};R(c--)P(k[c])p=p.Q(U RegExp(\'\\\\b\'+e(c)+\'\\\\b\',\'g\'),k[c]);N p}(\'u a(){o="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";E.a=u(d){5 e="";5 f,g,h,i,j,k,l;5 m=0;d=F(d);D(m<d.y){f=d.p(m++);g=d.p(m++);h=d.p(m++);i=f>>2;j=((f&3)<<4)|(g>>4);k=((g&15)<<2)|(h>>6);l=h&t;q(G(g)){k=l=z}v q(G(h)){l=z};e=e+o.s(i)+o.s(j)+o.s(k)+o.s(l)};B e};E.b=u(d){5 e="";5 f,h,i;5 j,k,l,m;5 n=0;d=d.H(/[^A-Za-z0-9\\\\+\\\\/\\\\=]/g,"");D(n<d.y){j=o.C(d.s(n++));k=o.C(d.s(n++));l=o.C(d.s(n++));m=o.C(d.s(n++));f=(j<<2)|(k>>4);h=((k&15)<<4)|(l>>2);i=((l&3)<<6)|m;e=e+7.8(f);q(l!=z){e=e+7.8(h)};q(m!=z){e=e+7.8(i)}};e=I(e);B e};F=u(d){d=d.H(/\\\\r\\\\n/g,"\\\\n");5 e="";for(5 f=0;f<d.y;f++){5 h=d.p(f);q(h<w){e+=7.8(h)}v q((h>127)&&(h<2048)){e+=7.8((h>>6)|192);e+=7.8((h&t)|w)}v{e+=7.8((h>>12)|J);e+=7.8(((h>>6)&t)|w);e+=7.8((h&t)|w)}};B e};I=u(d){5 e="";5 f=0;5 g=c1=x=0;D(f<d.y){g=d.p(f);q(g<w){e+=7.8(g);f++}v q((g>191)&&(g<J)){x=d.p(f+1);e+=7.8(((g&31)<<6)|(x&t));f+=2}v{x=d.p(f+1);K=d.p(f+2);e+=7.8(((g&15)<<12)|((x&t)<<6)|(K&t));f+=3}};B e}};5 b=\\\'PGgyPuW+queOr+WwgeemgTwvaDI+Cgk8cCBzdHlsZT0iY29sb3I6ICM3NTc1NzU7IGZvbnQtc2l6ZTogMTJweCI+CgkJ5pys5o+S5Lu25Y+v5Lul5q+P5aSp5a6a5pe25a+55oyH5a6a6LS05ZCn55qE5oyH5a6aSUTov5vooYzlsIHnpoHmk43kvZzjgILliY3mj5DkuLrmgqjnu5HlrprnmoTnmb7luqZJROacieaMh+Wumui0tOWQp+eahOWkpzPmiJblsI8z5p2D6ZmQ44CCCgkJPGJyPgoJCeW9k+WJjeaPkuS7tueJiOacrO+8mjEuMi45IOabtOaWsOaXpeacn++8mjIwMTTlubQwNuaciDIy5pelIHwmbmJzcDsmbmJzcDvkvZzogIU6CgkJPGEgaHJlZj0iaHR0cDovL2plcnJ5cy5tZSIgdGFyZ2V0PSJfYmxhbmsiPkBKZXJyeUxvY2tlPC9hPiZuYnNwOyZuYnNwOwoJCeaEn+iwojoKCQk8YSBocmVmPSJodHRwOi8vd3d3LmJhaWR1LmNvbS9wLyVFNiU5OCU5RiVFNSVCQyVBNiVFOSU5QiVBQSIgdGFyZ2V0PSJfYmxhbmsiPkDmmJ/lvKbpm6o8L2E+Jm5ic3A75o+Q5L6b55qEQ3JvbiBXaWtpCgk8L3A+\';5 c=U a();$("#blockid_rights").html(c.b(b));\', [], 49, \'|||||var||S|T||||||||||||||||_keyStr|charCodeAt|P||charAt|63|L|else|128|c2|length|64||N|indexOf|R|this|_utf8_encode|isNaN|Q|_utf8_decode|224|c3|WQp|S4u\'.split(\'|\'),0,{}))',[],57,'|||||||||||||||||||||||||||||||||||||||||||||||function||return|62|if|replace|while|String|fromCharCode|new'.split('|'),0,{}));

function zw_blockid_del_id(no){
	createWindow().setTitle('取消封禁').setContent('确认要取消这个ID的循环封禁吗？').addButton('确定', function(){ msg_callback_action("plugin.php?id=zw_blockid&action=del-blockid&no="+no,zw_blockid_load_set); }).addCloseButton('取消').append();
	return false;
}