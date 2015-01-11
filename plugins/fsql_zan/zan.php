<?php
if (! defined ( 'IN_KKFRAME' ))	exit ();
function fsql_zan_curl_client($pda,$url="http://c.tieba.baidu.com/c/f/frs/page",$bduss){
	$header = array("Content-Type: application/x-www-form-urlencoded");
	$pda=array_merge(array("BDUSS=".$bduss,
		"_client_id=wappc_1396611108603_817",
		"_client_type=2",
		"_client_version=5.7.0",
		"_phone_imei=642b43b58d21b7a5814e1fd41b08e2a6",
		"from=tieba"),$pda);
	$data=implode("&", $pda)."&sign=".md5(implode("", $pda)."tiebaclient!!!");
	//echo $data.'<br>';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$re = json_decode(curl_exec($ch)); 
	curl_close($ch);
	return $re;
}
function fsql_zan_curl_client2($pda,$url="http://c.tieba.baidu.com/c/f/frs/page",$b){
	$header = array ("Content-Type: application/x-www-form-urlencoded");
	$pda=array_merge(array("BDUSS=".$b,
		"_client_id=wappc_1396611108603_817",
		"_client_type=2",
		"_client_version=5.7.0",
		"_phone_imei=642b43b58d21b7a5814e1fd41b08e2a6",
		"action=like",
		"from=tieba"),$pda);
	$data=implode("&", $pda)."&sign=".md5(implode("", $pda)."tiebaclient!!!");
	//echo $data.'<br>';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$re = json_decode(curl_exec($ch)); 
	curl_close($ch);
	return $re;
}
function fsql_zan_get_list($uid,$kw,$sleep,$sp){
    $success_num=0;
    $zan_num=-1;
    $cookie = get_cookie ( $uid );
	preg_match ( '/BDUSS=([^ ;]+);/i', $cookie, $matches );
	$bduss = trim ( $matches [1] );
	$data=array("kw=".$kw,
            "pn=1",
            "q_type=2",
            "rn=50",
            "with_group=1");
	$re=fsql_zan_curl_client($data,"http://c.tieba.baidu.com/c/f/frs/page",$bduss);
	$re=$re->thread_list;
	foreach ($re as $key => $value) {
		if($value->zan->is_liked==0){
			$zan_num++;
			if($zan_num>0&&$zan_num%$sp==0){
				sleep($sleep);
			}
			$pid=$value->first_post_id;
			$tid=$value->id;
				$data=array("kw=".$kw,
					"post_id=".$pid,
					"thread_id=".$tid);
				$re=fsql_zan_curl_client2($data,"http://c.tieba.baidu.com/c/c/zan/like",$bduss);
                if($re->error_code==0)$success_num++;
		}
	}
    return $success_num;
}
function fsql_zan_get_list_test($uid,$kw,$sleep,$sp){
    $success_num=0;
    $zan_num=-1;
    $cookie = get_cookie ( $uid );
	preg_match ( '/BDUSS=([^ ;]+);/i', $cookie, $matches );
	$bduss = trim ( $matches [1] );
	$data=array("kw=".$kw,
            "pn=1",
            "q_type=2",
            "rn=50",
            "with_group=1");
	$re=fsql_zan_curl_client($data,"http://c.tieba.baidu.com/c/f/frs/page",$bduss);
	$re=$re->thread_list;
    $endtime = TIMESTAMP + 10;
	foreach ($re as $key => $value) {
		if($value->zan->is_liked==0 && $endtime > time()){
			$zan_num++;
			if($zan_num>0&&$zan_num%$sp==0){
				sleep($sleep);
			}
			$pid=$value->first_post_id;
			$tid=$value->id;
				$data=array("kw=".$kw,
					"post_id=".$pid,
					"thread_id=".$tid);
				$re=fsql_zan_curl_client2($data,"http://c.tieba.baidu.com/c/c/zan/like",$bduss);
                if($re->error_code==0)$success_num++;
		}
	}
    return $success_num;
}
?>