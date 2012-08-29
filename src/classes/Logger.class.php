<?php

if (!defined('CURRENT_ROOT'))
	define('CURRENT_ROOT', '../', true);


class Logger {
	
	private $m_logs = array();
	
	function __construct($logs = array()) {
		if (is_array($logs))
			$this->m_logs = $logs;
	}
	
	
	function __destruct () {
		$this->writeLogs();
	}
	
	
	public function __toString() {
		return @var_dump($this->m_logs);
	}
	
	
	final public function addLog($log) {
		if (!empty($log))
			$this->m_logs[] = $log;
	}
	
	
	final public function writeLogs() {
		foreach ($this->m_logs as $log)
			self::log($log);
	}
	
	
	final static public function log($log) {
		$line = '';
		
		if (empty($log))
			return;
		
		$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		// Example: May  8 08:44:04 localhost Bulk - something
		$line .= date('F  j, M:i:s').' ';
		$line .= $hostname.' ';
		$line .= $log.'\n';
		
        /** @todo Write a log somewhere */
	}
}


define('LOGGER', true, true);
?>
