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
if (!defined("MESSAGEEXCEPTION"))
   require_once(CURRENT_ROOT."exceptions/MessageException.class.php");
   
   

class Message {
	
	private $_subject;
	private $_text;
	private $_id;
	
	
	function __construct ($messageID) {
		
		global $_MySql;
		
		$sqlMessage = "SELECT `id`, `subject`, `text`
					   FROM `Message`
					   WHERE `id` = ".$messageID.";";

		$resMessage = $_MySql->query($sqlMessage);

		if ($resMessage->num_rows == 0)
			throw new MessageException("Message (ID: ".$messageID.") does not exist");

		$rowMessage = $resMessage->fetch_assoc();
		
		
		$this->_subject = $rowMessage['subject'];
		$this->_text = $rowMessage['text'];
		$this->_id = $rowMessage['id'];
	}
	
	
	public function __toString () {
		
		return get_class($this);
	}
	
	
	public function getText() {
		
		return $this->_text;
	}
	
	
	public function getSubject() {
		
		return $this->_subject;
	}
	
	
	public function getID() {
		
		return $this->_id;
	}
	
}


define("MESSAGE", true, true);

?> 
