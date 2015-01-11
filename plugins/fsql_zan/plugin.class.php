<?php
if(!defined('IN_KKFRAME')) exit('Access Denied!');
class plugin_fsql_zan extends Plugin{
    var $description = '为签到平台加入云点赞功能';
    var $modules = array (
		array ('id' => 'index',	'type' => 'page','title' => '云点赞','file' => 'index.php'),
        array('type' => 'cron', 'cron' => array('id' => 'fsql_zan/c_daily', 'order' => '101'))
	);
    var $version='1.0';
    var $update_time = '2014-06-16';
    function page_footer_js() {
		echo '<script src="plugins/fsql_zan/main.js?version=1.14.6.2"></script>';
	}
    public function install() {
		$query = DB::query ( 'SHOW TABLES' );
		$tables = array ();
		while ($table= DB::fetch($query)) $tables[]=implode ('', $table );
		if (!in_array ( 'fsql_zan_bar', $tables )){
		runquery("
			CREATE TABLE IF NOT EXISTS `fsql_zan_bar` (
				`sid` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`uid` int(10) unsigned NOT NULL,
				`name` varchar(127) NOT NULL,
				`unicode_name` varchar(512) NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			CREATE TABLE IF NOT EXISTS `fsql_zan_log` (
				`sid` int(10) unsigned NOT NULL,
				`uid` int(10) unsigned NOT NULL,
				`date` int(11) NOT NULL DEFAULT '0',
				`count` int(11) NOT NULL DEFAULT '0',
				UNIQUE KEY `sid` (`sid`,`date`),
				KEY `uid` (`uid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;

		");
			$this->saveSetting ( 'limit', '0' );
			$this->saveSetting ( 'sleep', '2' );
			$this->saveSetting ( 'sp','1');
		}
	}
	
    function uninstall() {
		DB::query ( "DROP TABLE fsql_zan_log,fsql_zan_bar" );
		showmessage ( "数据库删除成功。" );
	}
function on_config() {
		if ($_POST) {
			$limit_set=trim($_POST ['limit_set']);
			$sleep_set=intval(trim($_POST['sleep_set']));
			$sp_set=intval(trim($_POST['sp_set']));
			if (! $limit_set)	$limit_set = 0;
			if($limit_set<0) $limit_set=0;
			else if ($limit_set>999) $limit_set=999;
			if($sleep_set<0) $sleep_set=0;
			else if ($sleep_set>5) $sleep_set=5;
            if($sp_set<1) $sp_set=1;
			else if ($sp_set>5) $sp_set=5;
			$this->saveSetting('limit',$limit_set);
			$this->saveSetting('sleep',$sleep_set);
			$this->saveSetting('sp',$sp_set);
			showmessage ( "设置保存成功" );
		} else {
			$limit_set=$this->getSetting('limit');
			$sleep_set=$this->getSetting('sleep');
			$sp_set=$this->getSetting('sp');
			return <<<EOF
<P>限制每位用户最多点赞<input type="number" name="limit_set" min="0" max="999" value="$limit_set" style="outline:none;margin-left:4px;margin-right:4px"/>个吧（0~999，设置0为不限制）</p>
<p>暂停间隔<input type="number" name="sleep_set" min="0" max="5" style="outline:none;margin-left:4px;margin-right:4px" value="$sleep_set"/>秒（0~5,设置0为无间隔，建议设为2，太快成功率会很低）</p>
<p>赞<input type="number" name="sp_set" min="1" max="5" style="outline:none;margin-left:4px;margin-right:4px" value="$sp_set"/>个帖子后暂停（1~5，建议设为1，连续赞成功一般不会超过5个）</p>
EOF;
		}
	}
    
function handleAction(){
		global $uid;
		if(!$uid) return;
		switch ($_GET ['action']) {
		  case 'zan-log' :
				$date = date ( 'Ymd' );
				$data ['date'] = date ( 'Y-m-d' );
          case 'zan-history' :
				if ($_GET ['action'] == 'zan-history') {
					$date = intval ( $_GET ['date'] );
					$data ['date'] = substr ( $date, 0, 4 ) . '-' . substr ( $date, 4, 2 ) . '-' . substr ( $date, 6, 2 );
				}
				$data ['log'] = array ();
				$query = DB::query ( "SELECT * FROM fsql_zan_log l LEFT JOIN fsql_zan_bar t ON t.sid=l.sid WHERE l.uid='$uid' AND l.date='$date'" );
				while ( $result = DB::fetch ( $query ) ) {
					if (! $result ['sid']) continue;
					$data ['log'] [] = $result;
				}
				$data ['count'] = count ( $data ['log'] );
				$data ['before_date'] = DB::result_first ( "SELECT date FROM fsql_zan_log WHERE uid='{$uid}' AND date<'{$date}' ORDER BY date DESC LIMIT 0,1" );
				$data ['after_date'] = DB::result_first ( "SELECT date FROM fsql_zan_log WHERE uid='{$uid}' AND date>'{$date}' ORDER BY date ASC LIMIT 0,1" );
				break;
		  case 'test_zan' :
				include 'plugins/fsql_zan/zan.php';
				$tieba_count = DB::result_first ( "SELECT COUNT(*) FROM fsql_zan_bar WHERE uid='$uid'" );
				$tieba_offset = rand(1, $tieba_count) - 1;
				$tieba=DB::fetch_first ( "SELECT * FROM fsql_zan_bar WHERE uid='$uid' limit $tieba_offset,1" );
				if (! $tieba) showmessage ('没有添加贴吧，请先添加！');
                $sleep_set=$this->getSetting('sleep');
                $sp_set=$this->getSetting('sp');
                $result=0;
                $result=fsql_zan_get_list_test($uid,$tieba['name'],$sleep_set,$sp_set);
				showmessage ( "<p>点赞测试：【{$tieba[name]}】吧 </p><p>测试结果：成功点赞{$result}次</p><p>注：0次未必失败，可能首页帖子全赞过</p>" );
				break;
		  case 'delsid' :
				$_sid = intval ( $_GET ['sid'] );
				DB::query ( "DELETE FROM fsql_zan_bar WHERE sid='{$_sid}'" );
				$data ['msg'] = "删除成功";
				break;
		  case 'del-all-tid' :
				DB::query ( "DELETE FROM fsql_zan_bar WHERE uid='{$uid}'" );
				$data ['msg'] = "删除成功";
				break;
          case 'add-tieba' :
				$tieba = $_POST ['fsql_zan_add_tieba'];
				$ch = curl_init ('http://tieba.baidu.com/f?kw='.urlencode(iconv("utf-8", "gbk", $tieba)).'&fr=index');
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
				$contents = curl_exec ( $ch );
				curl_close ( $ch );
				$fid = 0;
				preg_match('/"forum_id"\s?:\s?(?<fid>\d+)/', $contents, $fids);
				$fid = $fids ['fid'];
				if ($fid == 0) {
					$data ['msg'] = "添加失败，请检查贴吧名称并重试";
					$data ['msgx'] = 0;
					break;
				}
				preg_match ( '/fname="(.+?)"/', $contents, $fnames );
				$unicode_name = urlencode($fnames [1]);
				$fname = iconv("gbk", "utf-8", $fnames [1]);
                $tieba_count = DB::result_first ( "SELECT COUNT(*) FROM fsql_zan_bar WHERE uid='$uid'" );
                $tieba_limit=$this->getSetting('limit');
                if($tieba_limit==0 || $tieba_count<$tieba_limit){
                    DB::insert ( 'fsql_zan_bar', array (
					'uid' => $uid,
					'name' => $fname,
					'unicode_name' => $unicode_name
				) );
				$data ['msg'] = "添加成功";
				break;
                }else{
                    $data ['msg'] = "添加失败，管理员设置上限".$tieba_limit."个贴吧";
                    break;
                }
       case 'zan-settings' :
				$query = DB::query ( "SELECT * FROM fsql_zan_bar WHERE uid='$uid'" );
				while ( $result = DB::fetch ( $query ) ) {
					$data ['tiebas'] [] = $result;
				}
				$data ['count1'] = count ( $data ['tiebas'] );
				break;
                }
                echo json_encode ( $data );
    }
}