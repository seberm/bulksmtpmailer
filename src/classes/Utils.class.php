<?php
/** The utils class
 * @class Utils
 * @file Utils.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */
 

final class Utils { 
	
	public static function isEmail ($email) {
		
		$atom = '[-a-z0-9!\#\$%&\'*+/=?^_`{|}~]';
		$domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';

		return preg_match("#^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$#", $email);
	}
	
	
	/** Cuts the given string to given length of chars
	 * @param string $string
	 * @param int $len length of chars
	 * @parem int $max
	 * @return string
	 */
	public function cutString ($string, $len = 300) {
		
		// Removes all tags from string
		$string = strip_tags($string);

		if (strlen($string) > $len) {
			$cut = substr($string, 0, $len);
			$lastSpace = strrpos($cut, " ");

			$result = substr($cut, 0, $lastSpace)."...";

			return $result;
			
		} else return $string;
	}
	
	
	public static function escape ($string) {

		global $_MySql;

		if ($_MySql)
			return $_MySql->escape_string($string);
		else 
            return mysql_escape_string($string);
	}


    public static function randomString() {

        return md5(@uniqid());
    }
	
}

define("UTILS", true, true);
?>
