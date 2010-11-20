<?php
/** The Bulk exception class
 * @class BulkException
 * @file BulkException.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);
	

### Interfaces
if (!defined("BULKERRORS"))
   require_once(CURRENT_ROOT."interfaces/BulkErrors.interface.php");
   
 
### Logger
if (!defined("LOGGER"))
   require_once(CURRENT_ROOT."classes/Logger.class.php");
   

class BulkException extends ErrorException implements BulkErrors {
	private $_Logger = null;

	function __construct($msg) {
		parent::__construct($msg);
		
		$this->_Logger = new Logger();
	}
	
	
	public function getStack () {
		$msg = "BulkMailer - ".$this->getMessage();

//only for tessting
//$msg = "BulkMailer (File: ".pathinfo($this->getFile(), PATHINFO_FILENAME).":".$this->getLine().") - ".$this->getMessage();
		
		$this->_Logger->addLog($msg);
		return $msg;
	}
	
}

define("BULKEXCEPTION", true, true);
?> 
