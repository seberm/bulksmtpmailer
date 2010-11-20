<?php

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);


class Logger {
	
	private $_logs = Array();
	
	function __construct ($logs = Array()) {
		
		if (is_array($logs))
			$this->_logs = $logs;
	}
	
	
	function __destruct () {
		
		$this->writeLogs();
	}
	
	
	public function __toString () {
		
		return @var_dump($this->_logs);
	}
	
	
	final public function addLog ($log = "") {
		
		if (!empty($log))
			$this->_logs[] = $log;
	}
	
	
	final public function writeLogs () {
		
		foreach ($this->_logs as $log)
			self::log($log);
	}
	
	
	final static public function log ($log = "") {
		
		$line = "";
		
		if (empty($log))
			return;
		
		$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		// Example: May  8 08:44:04 localhost Bulk - something
		$line .= date("F  j, M:i:s")." ";
		$line .= $hostname." ";
		$line .= $log."\n";
		
/** @todo Write a log somewhere */
	}
}


define("LOGGER", true, true);
?>
