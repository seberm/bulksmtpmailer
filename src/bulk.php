<?php
/** Main idex file
 * @author Otto Sabart <seberm@gmail.com>
 */

// Current root constant
if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "./", true);

// Reporting all errors -> enabled
error_reporting(E_ALL);

// PHP and Server default settings:
date_default_timezone_set("Europe/Prague");

// Global exceptions
try {
	// Check the PHP version (>5.0.0)
	if (!version_compare(phpversion(), "5.0.0", ">="))
		die("You're using old PHP version - ".PHP_VERSION.".");
	
	// Load exceptions
	if (!defined("DATABASEEXCEPTION"))
		(@include_once(CURRENT_ROOT."exceptions/DatabaseException.class.php")) or die ("Cannot load DatabaseException class!");
	
	if (!defined("BULKEXCEPTION"))
		(@include_once(CURRENT_ROOT."exceptions/BulkException.class.php")) or die ("Cannot load BulkException class!");
	////////////////////////////////////////////////
		
	try {	
		
		// Load classes
		try {
			if (!defined("MYSQL"))
				(@include_once(CURRENT_ROOT."tools/mysql/mysql.inc.php")) or die ("Cannot load database connection file!");
		
			if (!defined("UTILS"))
				(@include_once(CURRENT_ROOT."classes/Utils.class.php")) or die ("Cannot load Utils class!");
		} catch (DatabaseException $e) {
			
			throw new BulkException($e->getStack());
		}
		
		if (!defined("BULK"))
			(@include_once(CURRENT_ROOT."classes/Bulk.class.php")) or die ("Cannot load Bulk class!");
		////////////////////////////////////////////////
			
		$sqlActive = "	SELECT `Value`
						FROM `System`
						WHERE `Item` = 'ActiveSending';";
		$resActive = $_MySql->query($sqlActive);
		$rowActive = $resActive->fetch_assoc();
		$sendingActive = $rowActive['Value'];
	
	
		$sqlActiveMessage = "	SELECT `Value`
								FROM `System`
								WHERE `Item` = 'SendingMessageID';";
		$resActiveMessage = $_MySql->query($sqlActiveMessage);
		$rowActiveMessage = $resActiveMessage->fetch_assoc();
	
		$messageId = $rowActiveMessage['Value'];
	
		$id = isset($messageId) ? $messageId : 0;
	
		if (isset($_GET['id']) && is_numeric($_GET['id'])) {
			
			// SQL Injection prevention
			$id = Utils::escape($_GET['id']);
			
			if ($sendingActive === true)
				throw new BulkException("the BulkMailer is sending right now, please wait...");
			
			$sql = "UPDATE `System`
					SET `Value` = ".$id."
					WHERE `Item` = 'SendingMessageID';";
			
			$_MySql->query($sql);
		}
	
		if ($id <= 0)
			throw new BulkException("the message ID does not exist; exiting ...");
	
		$bulk = new Bulk($id);
		
		// Starts the Bulk
		$bulk->start();
			
	} catch (BulkException $e) {
		
		echo $e->getStack();
	}

} catch (Exception $e) {
	
	// Prints all not-caught exceptions
	echo $e->getStack();
} 


?>
