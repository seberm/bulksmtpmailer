<?php
/** The Message exception class
 * @class MessageException
 * @file MessageException.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);
	
### Interfaces
if (!defined("BULKERRORS"))
   require_once(CURRENT_ROOT."interfaces/BulkErrors.interface.php");


class MessageException extends ErrorException implements BulkErrors {
	
	public function getStack () {
		//$msg = "SMTP: ".$this->getMessage();
		
//only for testing
$msg = "MESSAGE: (File: ".pathinfo($this->getFile(), PATHINFO_FILENAME).":".$this->getLine().") - ".$this->getMessage();
		
		return $msg;
	}
	
}


define("MESSAGEEXCEPTION", true, true);
?>  
 
