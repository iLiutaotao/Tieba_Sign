<?php
if(!defined('IN_KKFRAME')) exit ('Access Denied!');
class plugin_x_meizi{
	var $description = '妹纸认证刷票专用';
	var $modules = array (
			array ('id' => 'index','type' => 'page','title' => '妹纸认证','file' => 'index.php')
		);
	var $version='0.1.3_13';
	function page_footer_js() {
		echo '<script src="plugins/x_meizi/main.js"></script>';
	}
	function on_install(){
		$query = DB::query ( 'SHOW TABLES' );
		$tables = array ();
		while ($table= DB::fetch($query)) $tables[]=implode ('', $table );
		if (!in_array ( 'x_meizi_a', $tables)) {
			DB::query("create table if not exists x_meizi_a(id int(10) unsigned not null auto_increment primary key,uid int(10) unsigned not null,votetype tinyint(1) unsigned not null,userid int(12) unsigned NOT NULL,fid int(12) unsigned not null,name varchar(32) not null,kw varchar(128) not null,statue text not null) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			DB::query("create table if not exists x_meizi_b(id int(10) unsigned not null auto_increment primary key,uid int(10) unsigned not null,islogin tinyint(1) not null default 0,userid int(12) unsigned not null,name varchar(32) not null,cookie text not null,voted tinyint(1) unsigned not null default 0) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			DB::query("create table if not exists x_meizi_log(id int(10) unsigned not null,uid int(10) unsigned NOT NULL, date int(11) not null DEFAULT 0, status tinyint(1) NOT NULL DEFAULT 0, success int(4) NOT NULL DEFAULT 0, failed int(4) NOT NULL DEFAULT 0,UNIQUE KEY id (id,date),KEY uid (uid)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			DB::query("replace into cron (`id`, `enabled`, `nextrun`, `order`) values ('x_meizi_daily',1,0, 96),('x_meizi_vote',1,0,97)");
			saveSetting ('x_mz_nowid','0');
			saveSetting ('x_mz_nextrun','0');
			saveSetting ('x_meizi',$this->nowversion);
			showmessage("妹纸刷票插件".substr($this->nowversion, 0,5)."版安装成功");
		}
		$version = getSetting ( 'x_meizi' );
		switch ($version){
			case '0.1.0_13-12-03':
				DB::query("alter table x_meizi_a add votetype tinyint(1) unsigned not null default 1");
				DB::query("alter table x_meizi_log_a add votenum int(4) NOT NULL DEFAULT 0");
			case '0.1.1_13-12-04':
				DB::query("drop table x_meizi_log_b");
				DB::query("alter table x_meizi_log_a rename to x_meizi_log");
				DB::query("alter table x_meizi_log change votenum success int(4) not null default 0");
				DB::query("alter table x_meizi_log add failed int(4) not null default 0");
				DB::query("alter table x_meizi_b add voted tinyint(1) unsigned not null default 0");	
			case '0.1.2_13-12-05':
				saveSetting ('x_mz_nextrun','0');
			default:
				saveSetting ('x_meizi', $this->nowversion);
				showmessage ('妹纸刷票插件已升级到'.substr($this->nowversion, 0,5).'版！');
		}
	}
	function on_uninstall(){
		DB::query("drop table x_meizi_a");
		DB::query("drop table x_meizi_b");
		DB::query("drop table x_meizi_log");
		DB::query("delete from cron where id='x_meizi_daily' or id='x_meizi_vote'" );
		DB::query("delete from setting where k='x_meizi' or k='x_mz_nowid'" );
		showmessage("卸载成功");
	}
}

