<?php
/** The MySql connection 
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../../", true);

if (!defined("CONFIG"))
	(@include_once(CURRENT_ROOT."config.inc.php")) or die ("Failed to load main system configuration!");

if ((!class_exists("MySQLi")) || (!class_exists("MySQLi_Result")))
	require_once(CURRENT_ROOT."tools/mysql/MySQLi.class.php");

// New Main web MySQL instance
$_MySql = new MySQLi ($_Config['mysql']['server'], $_Config['mysql']['username'], $_Config['mysql']['password'], $_Config['mysql']['database']);
	
if ($_MySql->connect_error)
    die("MYSQL ERROR - ".$_MySql->connect_error.": Failed to connect to MySQL server/database - ".$_MySql->connect_error);

// Set the mysql connection charset
$_MySql->set_charset("utf8");

define("MYSQL", true, true);

?> 
