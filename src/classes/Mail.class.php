<?php
/**
 * @class Mail
 * @throws BulkException
 * @file Mail.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */


if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);


### Exceptions
if (!defined("BULKEXCEPTION"))
   require_once(CURRENT_ROOT."exceptions/BulkException.class.php");
   

class Mail {
	
	private $_name;
	private $_email;
	private $_id;
	
	function __construct ($mailID) {
		
		global $_MySql;
		
		if (!is_numeric($mailID))
			$mailID = 0;
		
		$sqlMail = "SELECT `id`, `Name`, `Email` FROM `Mail`
					WHERE `id` = ".$mailID.";";
					
		$resMail = $_MySql->query($sqlMail);
		$rowMail = $resMail->fetch_assoc();
		
		$this->_name = $rowMail['Name'];
		$this->_email = $rowMail['Email'];
		$this->_id = $rowMail['id'];
	}
	
	
	public function __toString () {
		
		return get_class($this);
	}
	
	
	public function getEmail () {
		
		return $this->_email;
	}
	
	
	public function getName () {
		
		return $this->_name;
	}
	
	
	public function getID () {
		
		return $this->_id;
	}
	
	
	/** Updates an email in db.Mails
	 * @param String $name
	 * @param String $email
	 * @return boolean
	 */
	public function update ($name = "", $email = "") {
		
		global $_MySql;
		
		if (!Utils::isEmail($email)) {
			throw new BulkException("Bad email format: ".$email);
			return false;
		}

		$this->_name = $_MySql->escape_string($name);
		$this->_email = $_MySql->escape_string($email);
		
		$sqlUp = "UPDATE `Mail`
				  SET `Name` = '".$this->_name."', `Email` = '".$this->_email."'
				  WHERE `id` = ".$this->_id.";";
		
		return $_MySql->query($sqlUp);
	}
	
	
	/** Removes an email from db.Mail
	 * @return boolean
	 */
	public function remove () {
		
		global $_MySql;
		
		$sqlRm = "DELETE FROM `Mail`
				  WHERE `id` = ".$this->_id.";";
				  
		return $_MySql->query($sqlRm);
	}
	
	
	public static function add ($name = "", $email = "") {
		
		global $_MySql;
		
		if (!Utils::isEmail($email)) {
			throw new BulkException("Bad email format: ".$email);
			return false;
		}
		
		$name = $_MySql->escape_string($name);
		$email = $_MySql->escape_string($email);
		
		$sqlAdd = "INSERT INTO `Mail` (`Name`, `Email`)
				   VALUES ('".$name."', '".$email."');";
				   
		return $_MySql->query($sqlAdd);
	}


	public static function markSent ($mails = Array()) {
		
		global $_MySql;
		$ids = Array();
		
		foreach ($mails as $mail)
			$ids[] = $mail->getId();
			
		$sqlUp = "UPDATE `Mail`
				  SET `Sent` = true
				  WHERE `id` IN (".implode(",", $ids).");";
		
		return $_MySql->query($sqlUp);
	}
	
	
	public static function markUnsent() {
		
		global $_MySql;
		
		$sql = "UPDATE `Mail`
				SET `Sent` = false;";
		
		return $_MySql->query($sql);
	}
	
}

define("MAIL", true, true);
?> 
