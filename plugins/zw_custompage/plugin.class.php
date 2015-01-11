<?php
if (!defined('IN_KKFRAME')) exit('Access Denied!');

class plugin_zw_custompage extends Plugin {
	var $description = '本插件可以在前台添加页面、页底代码和修改背景。';
	var $modules = array();
	var $version = '1.2.2';
	private $setting;
	private $background;

	function install() {
		runquery("CREATE TABLE IF NOT EXISTS `zw_custompage_pages` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL DEFAULT '自定义页面',
`content` text,
`pswitch` tinyint(1) NOT NULL DEFAULT 1,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		$this -> saveSetting('setting', json_encode(array('page_switch' => 1,
					'footer_js_switch' => 1,
					'footer_text_switch' => 1,
					'bg_switch' => 0,
					'page_footer_js' => '',
					'page_footer_text' => '',
					'bg_images' => '',
					)));
	} 

	function uninstall() {
		runquery("DROP TABLE `zw_custompage_pages`;
DELETE FROM `setting` WHERE `k` LIKE 'zw_custompage%';
DELETE FROM `plugin_var` WHERE `pluginid`='zw_custompage';
");
	} 

	function on_install() { // 兼容模式
		$this -> install();
	} 

	function on_uninstall() { // 兼容模式
		$this -> uninstall();
	} 

	function getMethods() {
		$this -> setting = json_decode($this -> getSetting('setting'), true);
		if ($this -> setting['bg_switch'] == 1) {
			$bgimages = array_filter(explode("\n", trim ($this -> setting['bg_images'])));
			$this -> background = trim($bgimages[rand(0, count($bgimages)-1)]);
		} 
		$query = DB :: query ("SELECT * FROM zw_custompage_pages WHERE pswitch=1");
		while ($result = DB :: fetch ($query)) {
			$pages [] = $result;
		} 
		if ($this -> setting['page_switch'] == 1) {
			foreach($pages as $page) {
				$modules[] = array('id' => $page['id'],
					'type' => 'page',
					'title' => $page['title'],
					'file' => 'index.inc.php'
					);
			} 
		} 
		$modules[] = array('id' => 'admin',
			'type' => 'page',
			'title' => '自定义页面管理',
			'file' => 'admin.inc.php',
			'admin' => 1
			);
		return $modules;
	} 

	function on_upgrade($nowversion) {
		switch ($nowversion) {
			case '1.1.0':
				runquery("ALTER TABLE  `zw_custompage_setting` CHANGE  `footer_switch`  `footer_js_switch` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '1';
ALTER TABLE  `zw_custompage_setting` ADD  `footer_text_switch` TINYINT( 1 ) NOT NULL AFTER  `footer_js_switch`;
ALTER TABLE  `zw_custompage_setting` CHANGE  `page_footer`  `page_footer_js` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `zw_custompage_setting` ADD  `page_footer_text` TEXT NULL AFTER  `page_footer_js`;");
				return '1.1.1';
			case '1.1.1':
				$setting = DB :: fetch_first("SELECT * FROM `zw_custompage_setting` WHERE 1");
				$this -> saveSetting('setting', json_encode(array('page_switch' => $setting['page_switch'],
							'footer_js_switch' => $setting['footer_js_switch'],
							'footer_text_switch' => $setting['footer_text_switch'],
							'bg_switch' => $setting['bg_switch'],
							'page_footer_js' => $setting['page_footer_js'],
							'page_footer_text' => $setting['page_footer_text'],
							'bg_images' => $setting['bg_images'],
							)));
				runquery("DROP TABLE `zw_custompage_setting`;
DELETE FROM `setting` WHERE `k` LIKE 'zw_custompage%';
");
				return '1.2.0';
		} 
	} 

	function page_footer_js() {
		global $uid;
		if (is_admin($uid)) echo '<script src="plugins/zw_custompage/zw_custompage.js"></script>';
		if ($this -> setting['footer_js_switch'] == 1) echo $this -> setting['page_footer_js'];
		if ($this -> setting['bg_switch'] == 1) {
			echo "<script type='text/javascript'>$('#page_index').css({'background':'url({$this -> background})','background-size':'100% 100%','background-attachment':'fixed','color':'#e5e5e5'});</script>";
		} 
	} 

	function page_footer() {
		if ($this -> setting['footer_text_switch'] == 1) echo $this -> setting['page_footer_text'];
	} 

	function member_footer() {
		if ($this -> setting['footer_switch'] == 1) echo $this -> setting['page_footer_js'];
		if ($this -> setting['bg_switch'] == 1) {
			echo "<script src='" . jquery_path() . "'></script><script type='text/javascript'>$('#page_login').css({'background':'url({$this -> background}) no-repeat 50% 50%','background-size':'100% 100%'});</script>";
		} 
	} 

	function handleAction() {
		global $uid;
		if (!is_admin($uid)) exit('Access Denied');
		$data = array();
		$data['msgx'] = 0;
		switch ($_GET['action']) {
			case 'getsetting':
				$query = DB :: query("SELECT * FROM `zw_custompage_pages`");
				while ($result = DB :: fetch ($query)) {
					$result['title'] = strip_tags(trim($result['title']));
					$result['content'] = strip_tags(trim($result['content']));
					$result['content'] = cutstr($result['content'], 50, '...');
					$data ['pages'] [] = $result;
				} 
				$data ['count'] = count($data ['pages']);
				$setting = json_decode($this -> getSetting('setting'), true);
				$data ['setting'] = $setting ? $setting : array("page_switch" => 1, "footer_js_switch" => 1, "footer_text_switch" => 1, "bg_switch" => 0, "page_footer_js" => "", "page_footer_text" => "", "bg_images" => ""
					);
				break;
			case 'savesetting':
				$this -> saveSetting('setting', json_encode(array('page_switch' => $_POST['page_switch'] == 1?1:0,
							'footer_js_switch' => $_POST['footer_js_switch'] == 1?1:0,
							'footer_text_switch' => $_POST['footer_text_switch'] == 1?1:0,
							'bg_switch' => $_POST['bg_switch'] == 1?1:0,
							'page_footer_js' => trim($_POST['page_footer_js']),
							'bg_images' => trim($_POST['bg_images']),
							'page_footer_text' => trim($_POST['page_footer_text']),
							)));
				$data['msg'] = '保存成功！';
				break;
			case 'addpage':
				DB :: insert('zw_custompage_pages', array('title' => daddslashes(trim($_POST['page_title'])),
						'content' => daddslashes(trim($_POST['page_content'])),
						'pswitch' => $_POST['this_page_switch'] == 1 ? 1 : 0,
						));
				$data['msg'] = '添加成功！';
				break;
			case 'delall':
				DB :: query('TRUNCATE TABLE zw_custompage_pages;');
				$data['msg'] = '已经全部删除！';
				break;
			case 'allable':
				DB :: query('UPDATE `zw_custompage_pages` SET  `pswitch` = 1 WHERE `pswitch` = 0');
				$data['msg'] = '已经全部启用！';
				break;
			case 'allunable':
				DB :: query('UPDATE `zw_custompage_pages` SET  `pswitch` = 0 WHERE `pswitch` = 1');
				$data['msg'] = '已经全部关闭！';
				break;
			case 'turnedtoother':
				DB :: query("UPDATE `zw_custompage_pages`  SET pswitch=1-pswitch");
				$data['msg'] = '已经反向开启/关闭所有页面！';
				break;
			case 'setpage':
				DB :: query("UPDATE `zw_custompage_pages` SET  `title` =   '" . daddslashes(trim($_POST['page_title'])) . "',`content`  =  '" . daddslashes(trim($_POST['page_content'])) . "',`pswitch` =" . ($_POST['this_page_switch'] == 1 ? 1 : 0) . " WHERE id=" . intval($_GET['pid']));
				$data['msg'] = '保存成功！';
				break;
			case 'getpage':
				$result = DB :: fetch_first("SELECT * FROM `zw_custompage_pages` WHERE id=" . intval($_GET['pid']));
				$data ['this_page'] = $result;
				break;
			case 'delpage':
				DB :: query("DELETE FROM `zw_custompage_pages` WHERE id=" . intval($_GET['pid']));
				$data['msg'] = '删除成功！';
				break;
			default:
				$data['msg'] = '没有指定Action！！';
		} 
		echo json_encode ($data);
	} 
} 
