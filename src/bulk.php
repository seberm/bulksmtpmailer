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

// Check the PHP version (>5.0.0)
if (!version_compare(phpversion(), "5.0.0", ">="))
    die ("You're using old PHP version - ".PHP_VERSION.".");
    
    
if (!defined("MYSQL"))
    (@include_once(CURRENT_ROOT."tools/mysql/mysql.inc.php")) or die ("Cannot load database connection file!");

if (!defined("UTILS"))
    (@include_once(CURRENT_ROOT."classes/Utils.class.php")) or die ("Cannot load Utils class!");

if (!defined("BULK"))
	(@include_once(CURRENT_ROOT."classes/Bulk.class.php")) or die ("Cannot load Bulk class!");



try {
	
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

	if(isset($_GET['id']) && is_numeric($_GET['id'])) {
		
// sql injection possible... I know
		$id = $_GET['id'];
		
		if ($sendingActive === true) {
			
			throw new BulkException("The BulkMailer is sending right now. Please wait.");
			exit(1);
		}
		
		$sql = "UPDATE `System`
				SET `Value` = ".$id."
				WHERE `Item` = 'SendingMessageID';";
		
		$_MySql->query($sql);
	}

	if ($id <= 0) {
		
		throw new BulkException("The message ID does not exist.");
		exit(1);
	}

	$b = new Bulk($id);
	
	// Starts the Bulk
	$b->start();
	
} catch (BulkException $e) {
	
	echo $e->getStack();
} 


?>
