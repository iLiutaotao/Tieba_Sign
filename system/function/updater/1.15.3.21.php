<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
saveSetting('version', '1.15.3.22');
CACHEclean('setting');
showmessage('成功更新到 1.15.3.22！', '.');
?>