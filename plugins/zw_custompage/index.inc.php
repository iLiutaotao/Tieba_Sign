<?php
if(!$uid) exit('Access Denied');
$id = explode('-',$page[id]);
$result = DB :: fetch_first("SELECT * FROM `zw_custompage_pages` WHERE id={$id[1]};");
$page_name = $result['title'];
$page_content = $result['content'];

echo '<h2>' . $page_name . '</h2>';
echo $page_content;
