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
	
	private $m_name;
	private $m_email;
	private $m_id;
	
	function __construct($mailID) {
		
		global $_MySql;
		
		if (!is_numeric($mailID))
			$mailID = 0;
		
		$sqlMail = "SELECT `id`, `name`, `email` FROM `Mail`
					WHERE `id` = ".$mailID.";";
					
		$resMail = $_MySql->query($sqlMail);
		
		if ($resMail->num_rows == 0)
			throw new BulkException("Mail (ID: ".$mailID.") does not exist");
		
		$rowMail = $resMail->fetch_assoc();
		
		$this->m_name = $rowMail['name'];
		$this->m_email = $rowMail['email'];
		$this->m_id = $rowMail['id'];
	}
	
	
	public function __toString() {
		
		return get_class($this);
	}
	
	
	public function getEmail() {
		
		return $this->m_email;
	}
	
	
	public function getName() {
		
		return $this->m_name;
	}
	
	
	public function getID() {
		
		return $this->m_id;
	}
	
	
	/** Updates an email in db.Mails
	 * @param String $name
	 * @param String $email
	 * @return boolean
	 */
	public function update($name = "", $email = "") {
		
		global $_MySql;
		
		if (!Utils::isEmail($email)) {
			throw new BulkException("Bad email format: ".$email);
			return false;
		}

		$this->_name = $_MySql->escape_string($name);
		$this->_email = $_MySql->escape_string($email);
		
		$sqlUp = "UPDATE `Mail`
				  SET `name` = '".$this->m_name."', `email` = '".$this->m_email."'
				  WHERE `id` = ".$this->m_id.";";
		
		return $_MySql->query($sqlUp);
	}
	
	
	/** Removes an email from db.Mail
	 * @return boolean
	 */
	public function remove() {
		
		global $_MySql;
		
		$sqlRm = "DELETE FROM `Mail`
				  WHERE `id` = ".$this->m_id.";";
				  
		return $_MySql->query($sqlRm);
	}
	
	
	public static function add($name = "", $email = "") {
		
		global $_MySql;
		
		if (!Utils::isEmail($email)) {
			throw new BulkException("Bad email format: ".$email);
			return false;
		}
		
		$name = $_MySql->escape_string($name);
		$email = $_MySql->escape_string($email);
		
		$sqlAdd = "INSERT INTO `Mail` (`name`, `email`)
				   VALUES ('".$name."', '".$email."');";
				   
		return $_MySql->query($sqlAdd);
	}


	public static function markSent ($mails = array()) {
		
		global $_MySql;
		$ids = array();
		
		foreach ($mails as $mail)
			$ids[] = $mail->getId();
			
		$sqlUp = "UPDATE `Mail`
				  SET `sent` = true
				  WHERE `id` IN (".implode(",", $ids).");";
		
		return $_MySql->query($sqlUp);
	}
	
	
	public static function markUnsent() {
		
		global $_MySql;
		
		$sql = "UPDATE `Mail`
				SET `sent` = false;";
		
		return $_MySql->query($sql);
	}
	
}

define("MAIL", true, true);
?> 
