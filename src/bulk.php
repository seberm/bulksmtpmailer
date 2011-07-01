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
		throw new Exception("You're using old PHP version - " . PHP_VERSION . ". This script needs PHP 5.0.0 or upper.");
	
	// Load exceptions
	if (!defined("DATABASEEXCEPTION"))
		(@include_once(CURRENT_ROOT."exceptions/DatabaseException.class.php")) or die ("Cannot load DatabaseException class!");
	
	if (!defined("BULKEXCEPTION"))
		(@include_once(CURRENT_ROOT."exceptions/BulkException.class.php")) or die ("Cannot load BulkException class!");
    
    if (!defined("UTILS"))
        (@include_once(CURRENT_ROOT."classes/Utils.class.php")) or die ("Cannot load Utils class!");

	try {	
		
        if (!defined("MYSQL"))
            (@include_once(CURRENT_ROOT."tools/mysql/mysql.inc.php")) or die ("Cannot load database connection file!");
		
   		
        if (!defined("BULK"))
            (@include_once(CURRENT_ROOT."classes/Bulk.class.php")) or die ("Cannot load Bulk class!");
            
        /* 
         * Limit is set to 1
         *
         * When messages are sent to all e-mails,
         * next queue will start to sending.
         */
        $sql = "SELECT `id`, `isSending`
                FROM `Queue`
                WHERE `isSending` = true AND `isCompleted` = false
                LIMIT 1;";
        
        $res = $_MySql->query($sql);

        // If no queue to send
        if ($res->num_rows == 0)
            exit(0);

        $row = $res->fetch_assoc();
        $queueID = $row['id'];


		// If queue ID does not exists or is bad, we're exiting normally
		if ($queueID <= 0)
			exit(0); // Exited normally
        
        $smtp = new SMTP($_Config['bulk']['smtp']['server'],
                         $_Config['bulk']['smtp']['port'],
                         $_Config['bulk']['smtp']['secure'],
                         $_Config['bulk']['smtp']['timeout']
                        );

        // Bulk uses DI model (constructor injection)
        $bulk = new Bulk(new Queue($queueID), $smtp, $_Config);
    
        // Starts the sending
        $bulk->start();
        
    } catch (BulkException $e) {
		
        echo $e->getStack();
    }

} catch (Exception $e) {
	
	// Prints all not-caught exceptions
	echo "Non-caught exception: " . $e->getMessage();
} 


?>
