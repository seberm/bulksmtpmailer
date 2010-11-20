<?php
/** The SMTP exception class
 * @class SMTPException
 * @file SMTPException.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);
	
### Interfaces
if (!defined("BULKERRORS"))
   require_once(CURRENT_ROOT."interfaces/BulkErrors.interface.php");


class SMTPException extends ErrorException implements BulkErrors {
	
	public function getStack () {
		//$msg = "SMTP: ".$this->getMessage();
		
//only for testing
$msg = "SMTP: (File: ".pathinfo($this->getFile(), PATHINFO_FILENAME).":".$this->getLine().") - ".$this->getMessage();
		
		return $msg;
	}
	
}


define("SMTPEXCEPTION", true, true);
?>  
