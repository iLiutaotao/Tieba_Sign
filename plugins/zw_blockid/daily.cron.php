<?php
if (! defined ('IN_KKFRAME')) exit ();
$date = date ('Ymd', TIMESTAMP + 900);
DB :: query ("ALTER TABLE zw_blockid_log CHANGE `date` `date` INT NOT NULL DEFAULT '{$date}'");
DB :: query ("INSERT IGNORE INTO zw_blockid_log (uid, fid,tieba,blockid) SELECT uid, fid, tieba, blockid FROM zw_blockid_list");
$delete_date = date ('Ymd', TIMESTAMP - 86400 * 30);
DB :: query ("DELETE FROM zw_blockid_log WHERE date<'{$delete_date}'");

define ('CRON_FINISHED', true);
