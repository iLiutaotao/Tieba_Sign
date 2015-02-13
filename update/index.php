<?php 
require "../system/common.inc.php";
if(!defined('IN_KKFRAME')) exit('Access Denied');
ini_set('max_execution_time', 600);
define('UPDATE_DIR_TEMP', dirname(__FILE__).'/temp/');
define('UPDATE_DIR_INSTALL', dirname(__FILE__).'/../');
class AutoUpdate {
	private $_log = ture;
	public $logFile = '.updatelog';
	private $_lastError = null;
	public $currentVersion = 0;
	public $latestVersionName = '';
	public $latestVersion = null;
	public $latestUpdate = null;
	public $updateUrl = 'http://api.liujiantao.me/update';
	public $updateIni = 'update.ini';
	public $tempDir = UPDATE_DIR_TEMP;
	public $removeTempDir = true;
	public $installDir = UPDATE_DIR_INSTALL;
	public $dirPermissions = 0755;
	public $updateScriptName = '_upgrade.php';
	public function __construct($log = false) {
		$this->_log = $log;
	}
	public function log($message) {
		if ($this->_log) {
			$this->_lastError = $message;
			$log = fopen($this->logFile, 'a');
			if ($log) {
				$message = date('<Y-m-d H:i:s>').$message."\n";
				fputs($log, $message);
				fclose($log);
			}
			else {
				die('无法写入日志文件!');
			}
		}
	}
	public function getLastError() {
		if (!is_null($this->_lastError))
			return $this->_lastError;
		else
			return false;
	}
	private function _removeDir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") 
						$this->_removeDir($dir."/".$object); 
					else 
						unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	public function checkUpdate() {
		$this->log('检查更新. . .');	
		$updateFile = $this->updateUrl.'/update.ini';
		$update = @file_get_contents($updateFile);
		if ($update === false) {
			$this->log('无法获取更新文件 `'.$updateFile.'`!');
			return false;
		}
		else {
			$versions = parse_ini_string($update, true);
			if (is_array($versions)) {
				$keyOld = 0;
				$latest = 0;
				$update = '';
				foreach ($versions as $key => $version) {
					if ($key > $keyOld) {
						$keyOld = $key;
						$latest = $version['version'];
						$update = $version['url']; 
					}
				}
				$this->log('发现新版本 `'.$latest.'`.');
				$this->latestVersion = $keyOld;
				$this->latestVersionName = $latest;
				$this->latestUpdate = $update;
				return $keyOld;
			}
			else {
				$this->log('无法解压更新文件!');
				return false;
			}
		}
	}
	public function downloadUpdate($updateUrl, $updateFile) {
		$this->log('正在下载更新...');
		$update = @file_get_contents($updateUrl);
		if ($update === false) {
			$this->log('无法下载更新 `'.$updateUrl.'`!');
			return false;
		}
		$handle = fopen($updateFile, 'w');
		if (!$handle) {
			$this->log('无法保存更新文件 `'.$updateFile.'`!');
			return false;
		}
		if (!fwrite($handle, $update)) {
			$this->log('无法执行更新文件 `'.$updateFile.'`!');
			return false;
		}
		fclose($handle);
		return true;
	}
	public function install($updateFile) {
		$zip = zip_open($updateFile);
		while ($file = zip_read($zip)) {				
			$filename = zip_entry_name($file);
			$foldername = $this->installDir.dirname($filename);
			$this->log('更新中 `'.$filename.'`!');
			if (!is_dir($foldername)) {
				if (!mkdir($foldername, $this->dirPermissions, true)) {
					$this->log('无法创建目录 `'.$foldername.'`!');
				}
			}
			$contents = zip_entry_read($file, zip_entry_filesize($file));
			if (substr($filename, -1, 1) == '/')
				continue;
			if (file_exists($this->installDir.$filename)) {
				if (!is_writable($this->installDir.$filename)) {
					$this->log('无法更新 `'.$this->installDir.$filename.'`, 不可写入!');
					return false;
				}
			} else {
				$this->log('文件 `'.$this->installDir.$filename.'`, 不存在!');			
				$new_file = fopen($this->installDir.$filename, "w") or $this->log('文件 `'.$this->installDir.$filename.'`, 不能创建!');
				fclose($new_file);
				$this->log('文件 `'.$this->installDir.$filename.'`, 创建成功.');
			}
			$updateHandle = @fopen($this->installDir.$filename, 'w');
			if (!$updateHandle) {
				$this->log('无法更新文件 `'.$this->installDir.$filename.'`!');
				return false;
			}
			if (!fwrite($updateHandle, $contents)) {
				$this->log('无法写入文件 `'.$this->installDir.$filename.'`!');
				return false;
			}	
			fclose($updateHandle);
			if ($filename == $this->updateScriptName) {
				$this->log('尝试更新 `'.$this->installDir.$filename.'`.');
				require($this->installDir.$filename);
				$this->log('更新脚本 `'.$this->installDir.$filename.'` 包含!');
				unlink($this->installDir.$filename);
			}
		}
		zip_close($zip);
		if ($this->removeTempDir) {
			$this->log('临时目录 `'.$this->tempDir.'` 被删除.');
			$this->_removeDir($this->tempDir);
		}
		$this->log('更新 `'.$this->latestVersion.'` 安装完成.');
		return true;
	}
	public function update() {
		if ((is_null($this->latestVersion)) or (is_null($this->latestUpdate))) {
			$this->checkUpdate();
		}
		
		if ((is_null($this->latestVersion)) or (is_null($this->latestUpdate))) {
			return false;
		}
		if ($this->latestVersion > $this->currentVersion) {
			$this->log('Updating...');
			if ($this->tempDir[strlen($this->tempDir)-1] != '/');
				$this->tempDir = $this->tempDir.'/';
			
			if ((!is_dir($this->tempDir)) and (!mkdir($this->tempDir, 0777, true))) {
				$this->log('临时目录 `'.$this->tempDir.'` 不存在并且无法创建!');
				return false;
			}
			if (!is_writable($this->tempDir)) {
				$this->log('临时目录 `'.$this->tempDir.'` 不可写入!');
				return false;
			}
			$updateFile = $this->tempDir.'/'.$this->latestVersion.'.zip';
			$updateUrl = $this->updateUrl.'/'.$this->latestVersion.'.zip';
			if (!is_file($updateFile)) {
				if (!$this->downloadUpdate($updateUrl, $updateFile)) {
					$this->log('无法下载更新!');
					return false;
				}
				
				$this->log('最新更新下载 `'.$updateFile.'`.');
			}
			else {
				$this->log('最新更新下载到 `'.$updateFile.'`.');
			}
			return $this->install($updateFile);
		}
		else {
			$this->log('没有可用更新');
			return false;
		}
	}
}
$update = new AutoUpdate(true);
$update->currentVersion = VERSION_NAME; //版本号，整数
$update->updateUrl = 'http://api.liujiantao.me/update'; //更新服务器URL
$latest = $update->checkUpdate();
if ($latest !== false) {
	if ($latest > $update->currentVersion) {
		echo "新版本: ".$update->latestVersionName."<br>";
		echo "升级中...<br>";
		if ($update->update()) {
			showmessage('更新成功','../');
		}
		else {
			showmessage('更新失败请打开GitHub下载更新','https://github.com/liujiantaoliu/Tieba_Sign',5);
		}
	}
	else {
		showmessage('当前是最新版本','../');
	}
}
else {
	echo $update->getLastError();
}
?>