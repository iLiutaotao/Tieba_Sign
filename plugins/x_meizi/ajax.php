<?php 
require_once '../../system/common.inc.php';
require_once './core.php';
if (!$uid)
	exit ( 'Access Denied' );
$data = array ();
$data ['msgx'] = 0;
switch ($_GET ['v']) {
	case 'delid_a':
		$id=trim($_GET['id']);
		DB::query("delete from x_meizi_a where id=$id");
		$data ['msg'] = "删除成功⊙ω⊙";
		break;
	case 'delid_b':
		$id=trim($_GET['id']);
		DB::query("delete from x_meizi_b where id=$id");
		$data ['msg'] = "删除成功⊙ω⊙";
		break;
	case 'mz_refres':
		$id=trim($_GET['id']);
		$user_id=DB::result_first("select userid from x_meizi_a where id=$id");
		$mz_pannel_res=x_mz_pannel($user_id);
		$statue=$mz_pannel_res['statue'];
		DB::query("update x_meizi_a set statue='$statue' where id=$id");
		$data['msg'] = "刷新成功⊙ω⊙";
		break;
	case 'add_vote_id_a':
		$bdid = trim ( $_POST ['vote_id'] );
		$votetype = trim($_POST ['vote_type'] );
		$mz_res=x_mz_info(iconv('utf-8', 'gbk', $bdid));
		if($mz_res['fid']==0){
			$data ['msg'] = "你还没有发起妹纸认证，或者已发起妹纸认证但未点亮徽章。";
			break;
		}
		$mz_pannel_res=x_mz_pannel($mz_res['user_id']);
		DB::insert ( 'x_meizi_a', array (
			'uid'=>$uid,
			'votetype'=>$votetype,
			'userid' => $mz_res['user_id'],
			'fid' => $mz_res['fid'],
			'name' => $bdid,
			'kw' => $mz_pannel_res['kw'],
			'statue' => $mz_pannel_res['statue'],
		) );
		$data ['msg'] = "添加成功！";
		break;
	case 'add_vote_id_pre':
		$res = x_bd_login (random(8),random(8));
		$data ['vcodepic'] = $res ['vcodepic'];
		$data ['vcodemd5'] = $res ['vcodemd5'];
		$data ['pre'] = $res ['pre'];
		break;
	case 'add_vote_id_b':
		$bdid = trim ( $_POST ['bdid'] );
		$bds = trim ($_POST ['bds']);
		$res = x_bd_login ( $bdid, $bds, $_POST ['tb_vcode'], $_POST ['bdvcode_md5'] ,$_POST['bdvcode_pre']);
		if (!$res) {$data ['msg'] = "添加失败,似乎是网络问题";break;}
		switch($res ['error_code']) {
			case 0:
				preg_match('/BDUSS=(.*?);/', $res ['cookies'],$matches);
				if(!$matches[1]){$data ['msg'] = "由于某个莫名其妙的原因获取不了BDUSS、、⊙ω⊙";break;}
				$mz_res=x_mz_info(iconv('utf-8', 'gbk', $bdid));
				if($mz_res['fid']!=0){$data ['msg'] = "你发起妹纸认证了，不能再给别人投票";	break;}
				DB::insert ( 'x_meizi_b', array (
					'uid'=>$uid,
					'userid' => $mz_res['user_id'],
					'name' => $bdid,
					'cookie' =>  $res ['cookies'],
				) );
				$data ['msg'] = '添加成功！';
				$data ['msgx'] = 1;
				break;
			case 4:
				$data ['msg'] = '您输入的密码有误';
				$data ['msgx'] = 3;
			case 6:
				if($data ['msgx'] !=3) $data ['msg'] = '您输入的验证码有误';
				$data ['msgx'] = 3;
			case 257:
				$data ['bdid'] = $bdid;
				$data ['bds'] = $bds;
				$data ['vcodepic'] = $res ['vcodepic'];
				$data ['vcodemd5'] = $res ['vcodemd5'];
				$data ['pre'] = $res ['pre'];
				if($data ['msgx'] !=3) $data ['msgx'] = 2;
				break;
			case 120021:
				$data ['msg'] = 'ID被封了⊙ω⊙、请节哀';
				break;
			default:
				$data ['msg'] = '未知错误，错误代码：'.$res['error_code'];
		}
		break;
	case 'add_vote_id_b_cookie':
		$cookie = trim ( $_POST ['cookie'] );
		if(!strpos($cookie,';')) $cookie=$cookie.';';
		preg_match('/BDUSS=(.*?);/', $cookie,$matches);
		if(!$matches[1]){$data ['msg'] = "没有找到BDUSS⊙ω⊙";break;}
		$bdinfo=x_get_baidu_userinfo($cookie);
		if($bdinfo['no']!=0){$data ['msg'] = "获取用户信息失败⊙ω⊙";break;}
		$bdid=urldecode($bdinfo['data']['user_name_url']);
		$mz_res=x_mz_info($bdid);
		if($mz_res['fid']!=0){$data ['msg'] = "你发起妹纸认证了，不能再给别人投票⊙ω⊙";	break;}
		DB::insert ( 'x_meizi_b', array (
					'uid'=>$uid,
					'userid' => $mz_res['user_id'],
					'name' => iconv('GBK', 'UTF-8', $bdid),
					'cookie' =>  $cookie,
				) );
		$data ['msg'] = '添加成功！';
		$data ['msgx'] = 1;
		break;
	case 'get_vote_ids':
		$query = DB::query ( "SELECT * FROM x_meizi_a where uid=$uid" );
		while($result=DB::fetch($query)) $data ['a'][]=$result;
		foreach ($data ['a'] as &$meizi){
			switch ($meizi['votetype']){
				case '1':
					$meizi['votetype']='妹纸';
					break;
				case '2':
					$meizi['votetype']='伪娘';
					break;
				case '3':
					$meizi['votetype']='人妖';
					break;
				default:
					$meizi['votetype']='未知';
			}
			$meizi['uniname']=urlencode(iconv('utf-8', 'gbk', $meizi['name']));
		}
		unset($meizi);
		$query = DB::query ( "SELECT * FROM x_meizi_b where uid=$uid" );
		while ( $result = DB::fetch ( $query ) ) {
			$data ['b'] [] = $result;
		}
		$data ['count1'] = count ( $data ['a'] );
		$data ['count2'] = count ( $data ['b'] );
		break;
	case 'test_vode':
		$meizi = DB::fetch_first ( "SELECT * FROM x_meizi_a where uid='$uid' ORDER BY RAND() LIMIT 0,1" );
		$voter = DB::fetch_first ( "SELECT * FROM x_meizi_b where uid='$uid' and islogin=0 ORDER BY RAND() LIMIT 0,1" );
		if (!$meizi) showmessage ('没有添加被投票ID，请先添加！');
		if (!$voter) showmessage ('没有添加投票ID，请先添加！');
		switch($meizi['votetype']){
			case '1':
				$votetype='妹纸';
				break;
			case '2':
				$votetype='伪娘';
				break;
			case '3':
				$votetype='人妖';
				break;
		}
		list ( $statue, $result ) = x_meizi_vote ( $meizi, $voter);
		if($statue==1){
			DB::query("update x_meizi_a set statue='$result' WHERE id='{$meizi[id]}'");
			$statue='投票成功';
		}else{
			$statue='投票失败';
		}
		showmessage ( "<p>测试信息：{$voter[name]} 为 {$meizi[name]} 投{$votetype}票</p><p>测试结果：{$statue}</p><p>详细信息：{$result}</p>" );
		break;
	case 'get_log' :
		$date = date ( 'Ymd' );
		$data ['date'] = date ( 'Y-m-d' );
	case 'get_history' :
		if ($_GET ['v'] == 'get_history') {
			$date = intval ( $_GET ['date'] );
			$data ['date'] = substr ( $date, 0, 4 ) . '-' . substr ( $date, 4, 2 ) . '-' . substr ( $date, 6, 2 );
		}
		$data ['log'] = array ();
		$query = DB::query ( "SELECT * FROM x_meizi_log l LEFT JOIN x_meizi_a t ON t.id=l.id WHERE l.uid='$uid' AND l.date='$date'" );
		while ( $result = DB::fetch ( $query ) ) {
			$data ['log'] [] = $result;
		}
		$data ['count'] = count ( $data ['log'] );
		$data ['before_date'] = DB::result_first ( "SELECT date FROM x_meizi_log WHERE uid='{$uid}' AND date<'{$date}' ORDER BY date DESC LIMIT 0,1" );
		$data ['after_date'] = DB::result_first ( "SELECT date FROM x_meizi_log WHERE uid='{$uid}' AND date>'{$date}' ORDER BY date ASC LIMIT 0,1" );
		break;
}
echo json_encode ( $data );
	
?>