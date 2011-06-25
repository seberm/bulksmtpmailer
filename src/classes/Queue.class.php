<?php
/**
 * @class Mail
 * @throws BulkException
 * @file Mail.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */


if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);

### Classes
if (!defined("MESSAGE"))
	require_once (CURRENT_ROOT."classes/Message.class.php");
	

### Exceptions
if (!defined("QUEUEEXCEPTION"))
   require_once(CURRENT_ROOT."exceptions/QueueException.class.php");
   

class Queue {
	
	private $_name;
	private $_id;
	private $_sending;
	private	$_completed;
	private $_Message = NULL;
	
	
	function __construct ($queueID) {

		global $_MySql;
		
		if (!is_numeric($queueID))
			$queueID = 0;
		
		$sql = "SELECT `id`, `name`, `messageID`, `isSending`, `isCompleted`
				FROM `Queue`
				WHERE `id` = ".$queueID.";";

		$res = $_MySql->query($sql);

		if ($res->num_rows == 0)
			throw new QueueException("Queue (ID: ".$queueID.") does not exists; exiting ...");
			
		$row = $res->fetch_assoc();
		
		$this->_id = $row['id'];
		$this->_name = $row['name'];
		$this->_sending = $row['isSending'];
		$this->_completed = $row['isCompleted'];
		
		try {
			$this->_Message = new Message($row['messageID']);
		} catch (MessageException $e) {
			throw new QueueException($e->getStack());
		}
	}
	
	
	public function __toString() {
		
		return get_class($this);
	}
	
	
	public function getID() {
		
		return $this->_id;
	}
	
	
	public function isSending() {
		
		return $this->_sending;
	}
	
	
	public function isCompleted() {
		
		return $this->_completed;
	}
	
	
	public function getMessage() {
		
		return $this->_Message;
	}
}

define("QUEUE", true, true);
?> 
