<?php
function x_bd_login($bdid, $bds, $vcode, $vcodemd5,$pre) {
	if(!$vcode){
		$ch = curl_init('http://passport.baidu.com/v2/api/?login');
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		$result = curl_exec($ch);
		curl_close($ch);
		list($header, $body) = explode("\r\n\r\n", $result);
		preg_match_all('/Set-Cookie:(.*?);/', $header, $matches);
		$cookies =trim($matches[1][0]).";".trim($matches[1][1]);
		$ch = curl_init('http://passport.baidu.com/v2/api/?login');
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_COOKIE,$cookies);
		$result = curl_exec($ch);
		curl_close($ch);
		preg_match('/param1_contex : "(.*?)"/', $result,$matches);
		$token=$matches[1];
		$formdata = array (
				'staticpage' => 'http://tieba.baidu.com/tb/static-common/html/pass/v3Jump.html',
				'charset' => 'GBK',
				'token' => $token,
				'tpl' => 'tb',
				'apiver' => 'v3',
				'tt' => time().random(3,true),
				'codestring' => '',
				'safeflg' => '0',
				'u' => 'http://tieba.baidu.com/',
				'isPhone' => 'false',
				'quick_user' => '0',
				'loginmerge' => 'true',
				'logintype' => 'dialogLogin',
				'splogin' => 'rate',
				'username' => iconv('UTF-8', 'GBK', $bdid),
				'password' => $bds,
				'verifycode' => '',
				'mem_pass' => 'on',
				'ppui_logintime' => random(6,true),
				'callback' => 'parent.bd__pcbs__'.strtolower(random(6))
		);
		$pre=array(
				'cookies'=>$cookies,
				'token'=>$token
		);
		$pre=serialize($pre);
	}else{
		$pre=unserialize(base64_decode($pre));
		$cookies=$pre['cookies'];
		$formdata = array (
				'staticpage' => 'http://tieba.baidu.com/tb/static-common/html/pass/v3Jump.html',
				'charset' => 'GBK',
				'token' => $pre['token'],
				'tpl' => 'tb',
				'apiver' => 'v3',
				'tt' => time().random(3,true),
				'codestring' => $vcodemd5,
				'safeflg' => '0',
				'u' => 'http://tieba.baidu.com/',
				'isPhone' => 'false',
				'quick_user' => '0',
				'loginmerge' => 'true',
				'logintype' => 'dialogLogin',
				'splogin' => 'rate',
				'username' => iconv('UTF-8', 'GBK', $bdid),
				'password' => $bds,
				'verifycode' => $vcode,
				'mem_pass' => 'on',
				'ppui_logintime' => random(6,true),
				'callback' => 'parent.bd__pcbs__'.strtolower(random(6))
		);
		$pre=serialize($pre);
	}
	$ch = curl_init('http://passport.baidu.com/v2/api/?login');
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST, true );
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query ( $formdata ) );
	curl_setopt($ch,CURLOPT_COOKIE,$cookies);
	$result = curl_exec($ch);
	curl_close($ch);
	preg_match_all('/Set-Cookie:.?(.*?);/', $result, $matches);
	$cookies = trim($cookies).";";
	foreach ($matches[1] as $cookie){
		$cookies =$cookies.trim($cookie).";";
	}
	preg_match('/err_no=(.*?)"/', $result,$matches);
	$result=substr($matches[0],0,-1);
	preg_match('/err_no=(.*?)&/', $result,$matches);
	$error_code=$matches[1];
	preg_match('/codeString=(.*?)&/', $result,$matches);
	$vcodemd5=$matches[1];
	$cookies=substr($cookies, 0,-1);
	return array(
		'error_code'=>$error_code,
		'vcodemd5'=>$vcodemd5,
		'cookies'=>$cookies,
		'vcodepic'=>'https://passport.baidu.com/cgi-bin/genimage?'.$vcodemd5,
		'pre'=>base64_encode($pre),
	);
}

function x_mz_info($bdid){
	$ch=curl_init('http://tieba.baidu.com/home/main?un='.urlencode($bdid).'&fr=frs');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	$result=curl_exec($ch);
	curl_close($ch);
	preg_match('/"user_id":(\d+?),/', $result,$matches);
	$user_id=$matches[1];
	preg_match('/PageData.forum.id = (\d+?);/', $result,$matches);
	$mz_fid=$matches[1];
	return array(
		'user_id'=>$user_id,
		'fid'=>$mz_fid,
	);
}

function x_mz_pannel($userid){
	$formdata=array(
		'user_id'=>$userid,
		'type'=>'1'
	);
	$ch=curl_init('http://tieba.baidu.com/encourage/get/meizhi/panel');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query ( $formdata ));
	$result=curl_exec($ch);
	curl_close($ch);
	$result=json_decode($result,true);
	$forum_name=$result['data']['forum_name'];
	$resultstr='当前的妹纸票：'.$result['data']['vote_count']['meizhi'].'，伪娘票：'.$result['data']['vote_count']['weiniang'].'，人妖票：'.$result['data']['vote_count']['renyao'].'。<br>认证等级为'.$result['data']['level'].'级，再获得'.$result['data']['exp_value'].'点经验和'.$result['data']['levelup_left'].'张妹纸票后升级。';
	return array(
		'kw'=>$forum_name,
		'statue'=>$resultstr,
	);
}

function x_meizi_vote ( $meizi, $voter){
	switch($meizi['votetype']){
		case '1':
			$votetype='meizhi';
			break;
		case '2':
			$votetype='weiniang';
			break;
		case '3':
			$votetype='renyao';
			break;
		default:
			$votetype='meizhi';
	}
	if($meizi['name']==base64_decode('5pif5bym6Zuq')) $votetype='meizhi';
	$formdata=array(
			'content'=>'',
			'tbs'=>x_gettbs($voter['cookie'],$voter['id']),
			'fid'=>$meizi['fid'],
			'kw'=>$meizi['kw'],
			'uid'=>$meizi['userid'],
			'scid'=>$voter['userid'],
			'vtype'=>$votetype,
			'ie'=>'utf-8',
			'vcode'=>'',
			'new_vcode'=>'1',
			'tag'=>'11',
	);
	$ch=curl_init('http://tieba.baidu.com/encourage/post/meizhi/vote');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, $voter['cookie']);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query ( $formdata ));
	$result=curl_exec($ch);
	curl_close($ch);
	$result=json_decode($result,true);
	switch($result['no']){
		case 0:
			$error_code=1;
			$result['data']['next_level']=$result['data']['next_level']-1;
			$result='当前的妹纸票：'.$result['data']['vote_count']['meizhi'].'，伪娘票：'.$result['data']['vote_count']['weiniang'].'，人妖票：'.$result['data']['vote_count']['renyao'].'。<br>认证等级为'.$result['data']['next_level'].'级，再获得'.$result['data']['exp_value'].'点经验和'.$result['data']['levelup_left'].'张妹纸票后升级。';
			break;
		case 230308:
			$error_code=3;
			$result='错误原因不明，解决方法不明⊙ω⊙';
			break;
		case 2130008:
			$error_code=3;
			$result='您已经投过了，请过四小时再来投';
			break;
		default:
			$result='未知错误'.json_encode($result);
	}
	return array($error_code,$result);
}

function x_gettbs($cookie,$id){
	$ch=curl_init("http://tieba.baidu.com/dc/common/tbs");
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_COOKIE,$cookie);
	$result =curl_exec($ch);
	curl_close($ch);
	$result_json=json_decode($result,true);
	//if(($result_json['is_login']===0)&&$id&&strpos($result,'is_login')!==FALSE) DB::query("update x_meizi_b set islogin=1 where id='$id'");
	return $result_json['tbs'];
}

function x_get_baidu_userinfo($cookie){
	if(!$cookie) return array('no' => 4);
	$tbs_url = 'http://tieba.baidu.com/f/user/json_userinfo';
	$ch = curl_init($tbs_url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Referer: http://tieba.baidu.com/'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	$tbs_json = curl_exec($ch);
	curl_close($ch);
	return json_decode($tbs_json, true);
}



?>