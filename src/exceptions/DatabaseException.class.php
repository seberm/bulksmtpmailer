<?php
/** The Database exception class
 * @class DatabaseException
 * @file DatabaseException.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined('CURRENT_ROOT'))
	define('CURRENT_ROOT', '../', true);

if (!defined('BULKEXCEPTION'))
     require_once(CURRENT_ROOT.'exceptions/BulkException.class.php');

	
final class DatabaseException extends BulkException {
	
	public function getStack () {
		
		// Only for testing
		$msg = 'DATABASE: (File: '.pathinfo($this->getFile(), PATHINFO_FILENAME).':'.$this->getLine().') - '.$this->getMessage();
		
		return $msg;
	}
	
}


define('DATABASEEXCEPTION', true, true);
?>  
