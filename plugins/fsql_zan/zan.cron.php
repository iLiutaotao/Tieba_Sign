<?php
@chdir(dirname(__FILE__));
require_once './system/common.inc.php';
define('SIGN_LOOP', true);
define('ENABLE_CRON', true);
if (! defined ( 'IN_KKFRAME' ))	exit ();
require_once ROOT.'./plugins/fsql_zan/zan.php';
$date = date('Ymd', TIMESTAMP+900);
$count = DB::result_first("select COUNT(*) from fsql_zan_log where date = '{$date}'");
$endtime = TIMESTAMP + 45;
$sleep_set=HOOK::getPlugin('fsql_zan')->getSetting('sleep');
$sp_set=HOOK::getPlugin('fsql_zan')->getSetting('sp');
if($count){
    while($endtime > time()){
        $count = DB::result_first("select `sid` from fsql_zan_log where date='$date'");
        if($count==0) break;
        $offset = get_rand_bar();
        $cronzan=DB::fetch_first("select * from fsql_zan_bar where sid='{$offset}'");
        $sleep_set=HOOK::getPlugin('fsql_zan')->getSetting('sleep');
        $sp_set=HOOK::getPlugin('fsql_zan')->getSetting('sp');
        $result=0;
        $result=fsql_zan_get_list($cronzan['uid'],$cronzan['name'],$sleep_set,$sp_set);
        DB::query("UPDATE fsql_zan_log SET count=count+{$result} WHERE sid='{$cronzan['sid']}' AND date='$date'");
        }
        echo 'ok'."</br>";
        }
function get_rand_bar(){
    $date = date('Ymd', TIMESTAMP+900);
    $query = DB::query("select `sid` from fsql_zan_log where date='$date'");
    $r = array();
    while($row = DB::fetch($query)) $r[]=$row;
    $count=DB::result_first("select count(`sid`) from fsql_zan_log  where date='$date'");
    $sid=rand(1,$count)-1;
    return $r[$sid]['sid'];  
}

?>