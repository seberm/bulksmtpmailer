<?php
/** The MySql connection 
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../../", true);
	
if (!defined("DATABASEEXCEPTION"))
   require_once(CURRENT_ROOT."exceptions/DatabaseException.class.php");

if (!defined("CONFIG")) {
	
	$configFile = CURRENT_ROOT."config.inc.php";
	
	if (file_exists($configFile))
		@include_once($configFile);
	else throw new DatabaseException("failed to load system configuration; exiting ...");
}

if ((!class_exists("MySQLi")) || (!class_exists("MySQLi_Result")))
	require_once(CURRENT_ROOT."tools/mysql/MySQLi.class.php");

// New MySQL instance
$_MySql = new MySQLi($_Config['mysql']['server'],
					 $_Config['mysql']['username'],
					 $_Config['mysql']['password'],
					 $_Config['mysql']['database']);

if ($_MySql->connect_error)
	throw new DatabaseException("MYSQL ERROR - ".$_MySql->connect_error.": Failed to connect to MySQL server/database - ".$_MySql->connect_error."; exiting ...");

// Set the mysql connection charset
$_MySql->set_charset("utf8");

define("MYSQL", true, true);

?> 
