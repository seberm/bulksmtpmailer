<?php
/**
 * @class Mail
 * @throws BulkException
 * @file Mail.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */


if (!defined('CURRENT_ROOT'))
	define('CURRENT_ROOT', '../', true);

### Classes
if (!defined('MESSAGE'))
	require_once (CURRENT_ROOT.'classes/Message.class.php');
	

### Exceptions
if (!defined('QUEUEEXCEPTION'))
   require_once(CURRENT_ROOT.'exceptions/QueueException.class.php');
   

class Queue {
	
	private $m_name;
	private $m_id;
	private $m_sending;
	private	$m_completed;
    private $m_message = NULL;
	
	
	function __construct ($queueID) {

		global $_MySql;
		
		if (!is_numeric($queueID))
			$queueID = 0;
		
		$sql = 'SELECT `id`, `name`, `messageID`, `isSending`, `isCompleted`
				FROM `Queue`
				WHERE `id` = '.$queueID.';';

		$res = $_MySql->query($sql);

		if ($res->num_rows == 0)
			throw new QueueException('Queue (ID: '.$queueID.') does not exists; exiting ...');
			
		$row = $res->fetch_assoc();
		
		$this->m_id = (int) $row['id'];
		$this->m_name = $row['name'];
		$this->m_sending = (bool) $row['isSending'];
		$this->m_completed = (bool) $row['isCompleted'];
		
		try {

			$this->m_message = new Message($row['messageID']);

		} catch (MessageException $e) {
			throw new QueueException($e->getStack());
		}
	}
	
	
	public function __toString() {
		
		return get_class($this);
	}
	
	
	public function getID() {
		
		return $this->m_id;
	}
	
	
	public function isSending() {
		
		return $this->m_sending;
	}
	
	
	public function isCompleted() {
		
		return $this->m_completed;
	}
	
	
	public function getMessage() {
		
		return $this->m_message;
	}
}

define('QUEUE', true, true);
?> 
