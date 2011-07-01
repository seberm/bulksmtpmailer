<?php
/** The Bulk exception class
 * @class BulkException
 * @file BulkException.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);
		

### Interfaces
if (!defined("BULKERRORSINTERFACE"))
   require_once(CURRENT_ROOT."interfaces/BulkErrors.interface.php");
   
 
### Logger
if (!defined("LOGGER"))
   require_once(CURRENT_ROOT."classes/Logger.class.php");
   

class BulkException extends ErrorException implements BulkErrorsInterface {
	
	private $m_logger = null;

	function __construct($msg) {
		
		parent::__construct($msg);
		
		$this->m_logger = new Logger;
	}
	
	
	public function getStack () {
		
		$msg = "BulkMailer - " . $this->getMessage();

		// Only for tessting
		//$msg = "BulkMailer (File: ".pathinfo($this->getFile(), PATHINFO_FILENAME).":".$this->getLine().") - ".$this->getMessage();
		
		// Write error message to system logger
		$this->m_logger->addLog($msg);
		
		return $msg;
	}
	
}

define("BULKEXCEPTION", true, true);
?> 
