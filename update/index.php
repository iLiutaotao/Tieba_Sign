<?php 
require "../system/common.inc.php";
if(!defined('IN_KKFRAME')) exit('Access Denied');
ini_set('max_execution_time', 60);
define('UPDATE_DIR_TEMP', dirname(__FILE__).'/temp/');
define('UPDATE_DIR_INSTALL', dirname(__FILE__).'/../');
class AutoUpdate {
	private $_log = false;
	public $logFile = '.updatelog';
	private $_lastError = null;
	public $currentVersion = 0;
	public $latestVersionName = '';
	public $latestVersion = null;
	public $latestUpdate = null;
	public $updateUrl = 'http://api.liujiantao.me/update/';
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
				die('Could not write log file!');
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
		$this->log('Checking for a new update. . .');
		
		$updateFile = $this->updateUrl.'/update.ini';
		
		$update = @file_get_contents($updateFile);
		if ($update === false) {
			$this->log('Could not retrieve update file `'.$updateFile.'`!');
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
				$this->log('New version found `'.$latest.'`.');
				$this->latestVersion = $keyOld;
				$this->latestVersionName = $latest;
				$this->latestUpdate = $update;
				return $keyOld;
			}
			else {
				$this->log('Unable to parse update file!');
				return false;
			}
		}
	}
	public function downloadUpdate($updateUrl, $updateFile) {
		$this->log('Downloading update...');
		$update = @file_get_contents($updateUrl);
		if ($update === false) {
			$this->log('Could not download update `'.$updateUrl.'`!');
			return false;
		}
		$handle = fopen($updateFile, 'w');
		
		if (!$handle) {
			$this->log('Could not save update file `'.$updateFile.'`!');
			return false;
		}
		if (!fwrite($handle, $update)) {
			$this->log('Could not write to update file `'.$updateFile.'`!');
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
			$this->log('Updating `'.$filename.'`!');
			if (!is_dir($foldername)) {
				if (!mkdir($foldername, $this->dirPermissions, true)) {
					$this->log('Could not create folder `'.$foldername.'`!');
				}
			}
			$contents = zip_entry_read($file, zip_entry_filesize($file));
			if (substr($filename, -1, 1) == '/')
				continue;
			if (file_exists($this->installDir.$filename)) {
				if (!is_writable($this->installDir.$filename)) {
					$this->log('Could not update `'.$this->installDir.$filename.'`, not writable!');
					return false;
				}
			} else {
				$this->log('The file `'.$this->installDir.$filename.'`, does not exist!');			
				$new_file = fopen($this->installDir.$filename, "w") or $this->log('The file `'.$this->installDir.$filename.'`, could not be created!');
				fclose($new_file);
				$this->log('The file `'.$this->installDir.$filename.'`, was succesfully created.');
			}
			$updateHandle = @fopen($this->installDir.$filename, 'w');
			if (!$updateHandle) {
				$this->log('Could not update file `'.$this->installDir.$filename.'`!');
				return false;
			}
			if (!fwrite($updateHandle, $contents)) {
				$this->log('Could not write to file `'.$this->installDir.$filename.'`!');
				return false;
			}
			fclose($updateHandle);
			if ($filename == $this->updateScriptName) {
				$this->log('Try to include update script `'.$this->installDir.$filename.'`.');
				require($this->installDir.$filename);
				$this->log('Update script `'.$this->installDir.$filename.'` included!');
				unlink($this->installDir.$filename);
			}
		}
		zip_close($zip);
		if ($this->removeTempDir) {
			$this->log('Temporary directory `'.$this->tempDir.'` deleted.');
			$this->_removeDir($this->tempDir);
		}
		$this->log('Update `'.$this->latestVersion.'` installed.');
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
				$this->log('Temporary directory `'.$this->tempDir.'` does not exist and could not be created!');
				return false;
			}
			if (!is_writable($this->tempDir)) {
				$this->log('Temporary directory `'.$this->tempDir.'` is not writeable!');
				return false;
			}
			$updateFile = $this->tempDir.'/'.$this->latestVersion.'.zip';
			$updateUrl = $this->updateUrl.'/'.$this->latestVersion.'.zip';
			if (!is_file($updateFile)) {
				if (!$this->downloadUpdate($updateUrl, $updateFile)) {
					$this->log('无法下载更新!');
					return false;
				}
				$this->log('Latest update downloaded to `'.$updateFile.'`.');
			}
			else {
				$this->log('Latest update already downloaded to `'.$updateFile.'`.');
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
			showmessage('请打开GitHub下载更新','https://github.com/liujiantaoliu/Tieba_Sign',5);
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