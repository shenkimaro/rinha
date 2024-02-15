<?php

/**
 * @author ibanez
 */
class CacheLbr {

	private $pathFolder;
	private $rootName = '_lbr_';

	/**
	 * Tempo em segundos para manter um arquivo, 5 minutos
	 */
	const _TIME_DEFAULT = 300;

	/**
	 * Tempo em segundos para manter um arquivo, 1 minuto
	 */
	const _TIME_SHORT = 60;

	/**
	 * Tempo em segundos para manter um arquivo, 10 minutos
	 */
	const _TIME_LONG = 600;

	/**
	 * 30 dias de cache, use com cuidado
	 */
	const _TIME_VERY_LONG = 2592000;

	/**
	 * 1 dia de cache, use com cuidado
	 */
	const _TIME_DAY = 86400;

	private $fileLifeTime;
    
    private $logLive = false;

	public function __construct() {
		$this->setPathFolder('');
		if (!defined('_LIBRARY_CACHE_FOLDER')) {
			return;
		}
        $this->logLive = true;
		$this->setPathFolder(str_replace('//', '/', _LIBRARY_CACHE_FOLDER));
	}

	public function setPathFolder(string $pathFolder) {
		$this->pathFolder = $pathFolder;
	}

	public function isCacheOn() {
		return $this->logLive;
	}

	public function add(string $key, $value, $folder = '') {
		if(!$this->logLive){
			return;
		}
		if (!is_dir($this->pathFolder . '/' . $folder . '/')) {
			if (!is_dir($this->pathFolder)) {
				mkdir($this->pathFolder);
			}
			if (!is_dir($this->pathFolder . '/' . $folder . '/')) {
				mkdir($this->pathFolder . '/' . $folder . '/');
			}		
		}
		$key = $this->generateHashKey($key);
		$fp = fopen($this->pathFolder . '/' . $folder . '/' . $key, 'a');
		if (!$fp) {
			return;
		}
		ftruncate($fp, 0);
		fwrite($fp, $value);
		fclose($fp);
	}

	public function get($key, $lifeTime, $folder = '') {
        if(!$this->logLive){
			return;
		}
		$this->fileLifeTime = $lifeTime;
		$this->removeOldFiles($folder);
		$key = $this->generateHashKey($key);
		$file = str_replace('//', '/', $this->pathFolder . '/' . $folder . '/' . $key);
		if (is_file($file)) {
			return unserialize(file_get_contents($file));
		}
		return null;
	}

	private function generateHashKey($key) {
		return $this->rootName . Crypt::hash($key, Crypt::HASH_SHA512);
	}

	public function removeFolder($folder) {
        if(!$this->logLive){
			return;
		}
		if(!is_dir($this->pathFolder . $folder)){
			return;
		}
		$files = glob($this->pathFolder . $folder . '/*'); // get all file names
		foreach ($files as $file) { // iterate files
			if (is_file($file)) {
				unlink($file); // delete file
			}else{
				Util::debug($file);
			}
		}
	}

	public function clearCache() {
        if(!$this->logLive){
			return;
		}
		if(!is_dir($this->pathFolder)){
			return;
		}
		$files = glob($this->pathFolder. '/*'); // get all file names
		foreach ($files as $file) { // iterate files
			if (is_file($file)) {
				unlink($file); // delete file
			}
		}
	}

	public function remove($key) {
        if(!$this->logLive){
			return;
		}
		$key = $this->generateHashKey($key);
		if (is_file($this->pathFolder . '/' . $key)) {
			unlink($this->pathFolder . '/' . $key);
		}
	}

	private function removeOldFiles($subFolder = '') {
        if(!$this->logLive){
			return;
		}
		$files = glob($this->pathFolder ."/$subFolder" . '/*'); // get all file names
		foreach ($files as $file) { // iterate files
			if (is_file($file) && $this->getFileTime($file) > $this->fileLifeTime) {
				unlink($file); // delete file
			}
		}
	}

	private function getFileTime(string $file): ?int {
        if(!$this->logLive){
			return null;
		}
		if (is_file($file)) {
			$lastModification = filemtime($file);
			return date("YmdHis") - date("YmdHis", $lastModification);
		}
		return 0;
	}

}
