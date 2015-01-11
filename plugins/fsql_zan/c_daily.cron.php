<?php
if(!defined('IN_KKFRAME')) exit();
$date = date('Ymd', TIMESTAMP+900);
DB::query("ALTER TABLE fsql_zan_log CHANGE `date` `date` INT NOT NULL DEFAULT '{$date}'");
DB::query("INSERT IGNORE INTO fsql_zan_log (sid, uid) SELECT sid, uid FROM fsql_zan_bar");
$delete_date = date('Ymd', TIMESTAMP - 86400*10);
DB::query("DELETE FROM fsql_zan_log WHERE date<'$delete_date'");
cron_set_nextrun($tomorrow + 600);
