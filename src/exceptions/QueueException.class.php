<?php
/** The Queue exception class
 * @class QueueException
 * @file QueueException.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);
	
### Interfaces
if (!defined("BULKERRORS"))
   require_once(CURRENT_ROOT."interfaces/BulkErrors.interface.php");


class QueueException extends ErrorException implements BulkErrors {
	
	public function getStack () {
		
		// Only for testing
		$msg = "QUEUE: (File: ".pathinfo($this->getFile(), PATHINFO_FILENAME).":".$this->getLine().") - ".$this->getMessage();
		
		return $msg;
	}
	
}


define("QUEUEEXCEPTION", true, true);
?>  
 
