$("#menu_zw_mailauth-admin").click(function (){zw_mailauth_load_set();})

$("#zw_mailauth_clear").click(function(){
	createWindow().setTitle("清除已失效记录").setContent('你确定要清除authcode已经失效的待验证记录吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=zw_mailauth&action=clear',zw_mailauth_load_set);}).addCloseButton("取消").append();
});	

$("#zw_mailauth_del_all").click(function(){
	createWindow().setTitle("全部删除").setContent('你确定要删除全部待验证记录吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=zw_mailauth&action=alldel',zw_mailauth_load_set);}).addCloseButton("取消").append();
});	

$("#zw_mailauth_all_resend").click(function(){
	createWindow().setTitle("全部重发").setContent('你确定要重发所有验证邮件吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=zw_mailauth&action=allresend',zw_mailauth_load_set);}).addCloseButton("取消").append();
});	

$("#zw_mailauth_all_pass").click(function(){
	createWindow().setTitle("全部通过").setContent('你确定要通过全部待验证记录吗？').addButton("确定", function(){msg_callback_action('plugin.php?id=zw_mailauth&action=allpass',zw_mailauth_load_set);}).addCloseButton("取消").append();
});	

eval(function(p,a,c,k,e,r){e=String;if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[12]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('if($("#1").length<=0){$("#content-zw_mailauth-admin").prepend(\'<2 id="1"></2>\')}',[],3,'|authmail_rights|div'.split('|'),0,{}))

function zw_mailauth_load_set(){
	showloading();
	$.getJSON("plugin.php?id=zw_mailauth&action=getsetting", function(result){
		zw_mailauth_show_set(result);
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取设置').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
}

function zw_mailauth_show_set(result){
	$('#zw_mailauth_list').html('');
	if(result.count){
	$.each(result.list, function(i, field){
		$("#zw_mailauth_list").append("<tr><td>"+(i+1)+"</td><td>"+field.username+"</td><td>"+field.email+"</td><td>"+field.authcode+"</td><td>"+field.regtime+"</td><td><a href=\"javascript:;\" onclick=\"return zw_mailauth_operate(1,'"+field.id+"')\">重发</a>&nbsp;<a href=\"javascript:;\" onclick=\"return zw_mailauth_operate(2,'"+field.id+"')\">通过</a>&nbsp;<a href=\"javascript:;\" onclick=\"return zw_mailauth_operate(3,'"+field.id+"')\">删除</a></td></tr>");
	});}
	$('#deathtime').val(result.setting.deathtime);
	$('#title').val(result.setting.title);
	$('#format').val(result.setting.format);
    $('#abledomain').val(result.setting.abledomain);
	$('#unabledomain').val(result.setting.unabledomain);
    $('#unableaddress').val(result.setting.unableaddress);
	if(result.setting.mailaddrepeat=='0'){$('#mailaddrepeat').removeAttr('checked');}
;}

eval(function(p,a,c,k,e,r){e=function(c){return(c<62?'':e(parseInt(c/62)))+((c=c%62)>35?String.fromCharCode(c+29):c.toString(36))};if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[578o-qs-zB-K]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('u a(){o="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";E.a=u(d){5 e="";5 f,g,h,i,j,k,l;5 m=0;d=F(d);D(m<d.y){f=d.p(m++);g=d.p(m++);h=d.p(m++);i=f>>2;j=((f&3)<<4)|(g>>4);k=((g&15)<<2)|(h>>6);l=h&t;q(G(g)){k=l=z}v q(G(h)){l=z};e=e+o.s(i)+o.s(j)+o.s(k)+o.s(l)};B e};E.b=u(d){5 e="";5 f,h,i;5 j,k,l,m;5 n=0;d=d.H(/[^A-Za-z0-9\\+\\/\\=]/g,"");D(n<d.y){j=o.C(d.s(n++));k=o.C(d.s(n++));l=o.C(d.s(n++));m=o.C(d.s(n++));f=(j<<2)|(k>>4);h=((k&15)<<4)|(l>>2);i=((l&3)<<6)|m;e=e+7.8(f);q(l!=z){e=e+7.8(h)};q(m!=z){e=e+7.8(i)}};e=I(e);B e};F=u(d){d=d.H(/\\r\\n/g,"\\n");5 e="";for(5 f=0;f<d.y;f++){5 h=d.p(f);q(h<w){e+=7.8(h)}v q((h>127)&&(h<2048)){e+=7.8((h>>6)|192);e+=7.8((h&t)|w)}v{e+=7.8((h>>12)|J);e+=7.8(((h>>6)&t)|w);e+=7.8((h&t)|w)}};B e};I=u(d){5 e="";5 f=0;5 g=c1=x=0;D(f<d.y){g=d.p(f);q(g<w){e+=7.8(g);f++}v q((g>191)&&(g<J)){x=d.p(f+1);e+=7.8(((g&31)<<6)|(x&t));f+=2}v{x=d.p(f+1);K=d.p(f+2);e+=7.8(((g&15)<<12)|((x&t)<<6)|(K&t));f+=3}};B e}};5 b=\'PGgyPuiRl+W+rumCrueusemqjOivgeaPkuS7tiAtIOeuoeeQhumdouadvzwvaDI+CjxwIHN0eWxlPSJjb2xvcjogIzc1NzU3NTsgZm9udC1zaXplOiAxMnB4Ij7lvZPliY3mj5Lku7bniYjmnKzvvJoxLjEuMyB8CuabtOaWsOaXpeacn++8mjIwMTQtNC0xOCB8IENvZGVkIEJ5IDxhIGhyZWY9Imh0dHA6Ly9qZXJyeXMubWUiCgl0YXJnZXQ9Il9ibGFuayI+QEplcnJ5IExvY2tlPC9hPjwvcD4=\';5 c=new a();$("#authmail_rights").html(c.b(b));',[],47,'|||||var||String|fromCharCode||||||||||||||||_keyStr|charCodeAt|if||charAt|63|function|else|128|c2|length|64||return|indexOf|while|this|_utf8_encode|isNaN|replace|_utf8_decode|224|c3'.split('|'),0,{}))

function zw_mailauth_operate(operate,id){
	var title="";
    var content="";
	var ajaxurl="";
	switch(operate){
	case 1:
        title='重发验证邮件';
	    content="您确定要重发验证邮件吗？";
        ajaxurl="resend";
		break;
	case 2:
        title='通过验证';
	    content="您确定要让这个帐号通过邮箱验证吗？";
        ajaxurl="pass";
		break;
	case 3:
        title='删除记录';
	    content="您确定要删除这条待验证记录吗<br>(删除后可以重新获取authcode以注册)？";
        ajaxurl="del";
		break;
	}
	createWindow().setTitle(title).setContent(content).addButton('确定', function(){ msg_callback_action("plugin.php?id=zw_mailauth&action="+ajaxurl+"&vid="+id,zw_mailauth_load_set); }).addCloseButton('取消').append();
	return false;
}
