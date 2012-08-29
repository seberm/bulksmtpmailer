<?php
/** The Bulk errors interface. All Bulk exceptions must implement this interface.
 * @interface BulkErrors
 * @file BulkErrors.interface.php
 * @author Otto Sabart <seberm@gmail.com>
 */


interface BulkErrorsInterface {

	public function getStack();
}


define('BULKERRORSINTERFACE', true, true); 
?>
