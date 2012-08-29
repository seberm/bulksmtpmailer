<?php
/** The SMTP exception class
 * @class SMTPException
 * @file SMTPException.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined('CURRENT_ROOT'))
	define('CURRENT_ROOT', '../', true);

if (!defined('BULKEXCEPTION'))
     require_once(CURRENT_ROOT.'exceptions/BulkException.class.php');


final class SMTPException extends BulkException {
	
	
    /** @Override
     */
	public function getStack () {
		
		// Only for testing
		$msg = 'SMTP: (File: '.pathinfo($this->getFile(), PATHINFO_FILENAME).':'.$this->getLine().') - '.$this->getMessage();
		
		return $msg;
	}
	
}


define('SMTPEXCEPTION', true, true);
?>  
