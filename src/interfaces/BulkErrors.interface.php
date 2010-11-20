<?php
/** The Bulk errors interface. All Bulk exceptions must implement this interface.
 * @interface BulkErrors
 * @file BulkErrors.interface.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);


interface BulkErrors {
	public function getStack ();
}


define("BULKERRORS", true, true); 
?>
