<?php
/** The utils class
 * @class Utils
 * @file Utils.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

final class Utils { 
	
	public static function isEmail ($email) {
		
		$atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';
		$domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';
		
		return eregi("^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$", $email);
	}
}

define("UTILS", true, true);
?>
