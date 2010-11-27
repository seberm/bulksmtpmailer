<?php
/** Main idex file
 * @author Otto Sabart <seberm@gmail.com>
 */
	
	// Current root constant
	define("CURRENT_ROOT", "../");
	
	// Reporting all errors -> enabled
	error_reporting(E_ALL);
	
	// PHP and Server default settings:
	date_default_timezone_set("Europe/Prague");

	// Check the PHP version (>5.0.0)
	if (!version_compare(phpversion(), "5.0.0", ">="))
		die ("You're using old PHP version - ".PHP_VERSION.".");
	
	
	if (!defined("ERROR"))
		(@include_once("classes/Error.class.php")) or die ("Cannot load Error class!");
	
	$_Error = new Error ();
	
	if (!defined("MYSQL")) {
		(@include_once(CURRENT_ROOT."tools/mysql/mysql.inc.php")) or die ("Cannot load database connection file!");
	}
		
	if (!defined("UTILS"))
    (@include_once(CURRENT_ROOT."classes/Utils.class.php")) or die ("Cannot load Utils class!");
	
	if (!defined("CORE"))
		(@include_once("classes/Core.class.php")) or die ("Cannot load Core class!");
	$_Core = new Core ();
	
	
	include_once ("admin/main.php");
?>
