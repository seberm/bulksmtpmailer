<?php
/** The Message exception class
 * @class MessageException
 * @file MessageException.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);
	
if (!defined("BULKEXCEPTION"))
     require_once(CURRENT_ROOT."exceptions/BulkException.class.php");


final class MessageException extends BulkException {
	
	public function getStack () {
		
		// Only for testing
		$msg = "MESSAGE: (File: ".pathinfo($this->getFile(), PATHINFO_FILENAME).":".$this->getLine().") - ".$this->getMessage();
		
		return $msg;
	}
	
}


define("MESSAGEEXCEPTION", true, true);
?>  
 
