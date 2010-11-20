<?php
/** The SMTP class
 * @class SMTP
 * @throws SMTPException
 * @file SMTP.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);


### Exceptions
if (!defined("SMTPEXCEPTION"))
   require_once(CURRENT_ROOT."exceptions/SMTPException.class.php");


if (!defined("UTILS"))
   require_once(CURRENT_ROOT."classes/Utils.class.php");
   

### Constants
$prefix = "SMTP_";
define ($prefix."TIMEOUT", 30);
define ($prefix."AUTHTYPE", "login");
define ($prefix."SMTPTYPE", "smtp");

if (!defined("CRLF"))
	define ("CRLF", "\r\n", true);


class SMTP {
	
	private $_server;
	private $_port;
	private $_authType;
	private $_smtpType;
	private $_timeout;
	
	private $_login;
	private $_password;
	
	private $_socket = null;
	
	// Is user logged?
	private $_logged = false;
	
	// Is connected to the server?
	private $_connected = false;
	
	private $SMTP_RESPONSE = Array(
								200 => "(nonstandard success response, see rfc876)",
								211 => "System status, or system help reply",
								214 => "Help message",
								220 => "Service ready",
						  		221 => "Service closing transmission channel",
								250 => "Requested mail action okay, completed",
								251 => "User not local",
								354 => "Start mail input; end with <CRLF>.<CRLF>",
								421 => "<domain> Service not available, closing transmission channel",
								450 => "Requested mail action not taken: mailbox unavailable",
								451 => "Requested action aborted: local error in processing",
								452 => "Requested action not taken: insufficient system storage",
								500 => "Syntax error, command unrecognised",
								501 => "Syntax error in parameters or arguments",
								502 => "Command not implemented",
								503 => "Bad sequence of commands",
								504 => "Command parameter not implemented",
								521 => "<domain> does not accept mail (see rfc1846)",
								530 => "Access denied (???a Sendmailism)",
								550 => "Requested action not taken: mailbox unavailable",
								551 => "User not local",
								552 => "Requested mail action aborted: exceeded storage allocation",
								553 => "Requested action not taken: mailbox name not allowed",
								554 => "Transaction failed");
	
	
	function __construct ($server, $port, $timeout = TIMEOUT, $authType = AUTHTYPE, $smtpType = SMTPTYPE) {
		
		$this->_server = $server;
		$this->_port = is_numeric($port) ? $port : 0;
		$this->_timeout = is_numeric($timeout) ? $timeout : 0;
		$this->_authType = $authType;
		$this->_smtpType = $smtpType;
	}
	
	
	function __destruct () {
		
		if (!is_resource($this->_socket))
			return;

		if ($this->isConnected)
			$this->disconnect();
	}

	
	public function __toString () {
		
		return get_class($this);
	}
	
	
/** @todo dodelat invoke pri volani tridy jako fce.. zkusit vratit instanci na nove vytvorene pripojeni	
 */
	function __invoke($server, $port, $timeout = SMTP_TIMEOUT) { }
	
/** @todo mozna casem dodelat i dalsi navratove metody..
 */
	public function getServer () {
		
		return $this->_server;
	}
	
	
	/** Opens a connection to SMTP server
	 * return boolean
	 */
	public function connect () {
		
        $this->_socket = @fsockopen($this->_server, $this->_port, $errno, $errstr, $this->_timeout);
		
		if (!is_resource($this->_socket)) {
			throw new SmtpException("Failed to open a SMTP connection (".$errno." - ".$errstr.")");
			return false;
		}
		
		// So,.. we're connected.
		$this->_connected = true;
		
		// It's very important to call getLine function! We must come over the welcome message.
		$this->getLine();
		
		return $this->identify();
	}
	
	
	/** Removes the SMTP server socket
	 * @return boolean
	 */
	public function disconnect () {
		
		if (!$this->_connected)
			return true;
		
		$this->quit();
		
		// We're disconnected
		$this->_connected = false;

		return fclose($this->_socket);
	}
	
	
	public function isLogged () {
		
		return $this->_logged;
	}
	
	
	public function isConnected () {
		
		return $this->_connected;
	}
	
	
	/** Sets the SMTP username
	 * @param String $login
	 */
	public function setLogin ($login = "") {
		
		if (empty($login)) {
			throw new SmtpException("You're setting empty login");
			return;
		}
		
		$this->_login = $login;
	}
	
	
	/** Sets the SMTP server password
	 * @param String $password
	 */
	public function setPassword ($password = "") {
		
		if (empty($password)) {
			throw new SmtpException("You're setting empty password");
			return;
		}
		
		$this->_password = $password;
	}
	
	
	/** Executes a command on SMTP server
	 * @param String $command
	 * @see smtpLogin
	 * @return boolean
	 */
	public function execute ($command) {
		
		$cmd = $command;
		$cmd .= CRLF;

		if ($this->isConnected())
			return fwrite($this->_socket, $cmd, strlen($cmd));
		else {
			throw new SmtpException("The server is not connected");
			return false;
		}
	}
	
	
	/** Gets a line from the socket connection
	 * @return boolean or String
	 */
/**@todo fce pak muze byt asi & */
	private function getLine () {
		
		$line = "";
		$return = "";
		
		if ($this->isConnected()) {
/** @todo a doupravit: || substr($line, 3, 1) !== " " -> je to divny */
			while(strpos($return, CRLF) === false || substr($line, 3, 1) !== " ") {
				$line = fgets($this->_socket, 512);
				$return .= $line;
			}
			
			if (is_null($return))
				return false;
				
			return $return;
		} else throw new SmtpException("The server is not connected");
		
		return false;
	}
	
	
	/** Reads a line from 0 to $chars chars
	 * @param string $line
	 * @param integer $line
	 * @return boolean or String
	 */
	private function readLine ($line, $chars = 3) {
		
		$result = "";
	
		if (!empty($line))
			return substr(trim($line), 0, $chars);
		
		return false;
	}
	
	
	/** Returns a server response text by given response id
	 * @param int $key
	 * @return String
	 */
	private function getResponseText($key = 0) {
		
		$responseText = "";
		
		if (array_key_exists($key, $this->SMTP_RESPONSE))
			$responseText = $this->SMTP_RESPONSE[$key]."\n";
		else $responseText = "Unknown SMTP response\n";
		
		return $responseText;
	}
	
	
	/** Logins to a SMTP server
	 * @see connect
	 * @see disconnect
	 */
	public function login () {
		
		if (!$this->isConnected()) {
			throw new SmtpException("The server is not connected");
			return;
		}
	
		$login = $this->_login;
		$password = $this->_password;
		
		switch ($this->_authType) {
			case "plain":
/** @todo plain login
 */		
				break;
			
			case "login":
			default:
				$loginENC = base64_encode($login);
				$passwordENC = base64_encode($password);
				$this->execute("AUTH LOGIN");

/*if ($this->_smtpType == "esmtp") {
	$responseId = $this->readLine($this->getLine());
	if ($responseId != 250) {
		$this->disconnect();
		
		throw new SmtpException("The server does not support the given type of an SMTP authenticity");
		return;
	}
	
	$responseId = (integer) $this->readLine($this->getLine());
	if ($responseId != 250)
		throw new SmtpException($this->getResponseText($responseId));
}*/

				$this->execute($loginENC);
				$responseId = (integer) $this->readLine($this->getLine());
				if ($responseId != 334)
					throw new SmtpException($this->getResponseText($responseId));
				
				$this->execute($passwordENC);
				$responseId = (integer) $this->readLine($this->getLine());
				if ($responseId != 334)
					throw new SmtpException($this->getResponseText($responseId));
					
					
				$responseId = (integer) $this->readLine($this->getLine());
				if ($responseId != 235)
					throw new SmtpException($this->getResponseText($responseId));

/**@todo ... asi se hazi true i kdyz se vyhodi error... coz je spatne..
 */
 $this->_logged = true;		
 		
				break;
		}
	}
	
	
	/** Says a HELO to a SMTP server
	 * @return boolean
	 */
	private function helo () {
		
		if (!$this->isConnected())
			return false;
		
		$this->execute("HELO ".$this->_server);
		
		$responseId = (integer) $this->readLine($this->getLine());
		if ($responseId != 250) {
			throw new SmtpException($this->getResponseText($responseId));
			$this->disconnect();
			return false;
		}
		
		return true;
	}
	
	
	/** Says a EHLO to a eSMTP server
	 * @return boolean
	 */
	private function ehlo () {
		
		if (!$this->isConnected())
			return false;
		
		$this->execute("EHLO ".$this->_server);
		$responseId = (integer) $this->readLine($this->getLine());

		if ($responseId != 250) {
			throw new SmtpException($this->getResponseText($responseId));
			$this->disconnect();
			return false;
		}	
		
		return true;
	}
	
	
	private function identify () {
		
		$returnStat = false;
		
		if (!$this->isConnected())
			return $returnStat;
		
		switch ($this->_smtpType) {
			case "esmtp":
				$returnStat = $this->ehlo();
				break;
				
			case "smtp":
			default:
				$returnStat = $this->helo();
				break;
		}
		
		return $returnStat;
	}
	
	
	public function quit () {
		
		$this->execute("QUIT");
		
		$responseId = (integer) $this->readLine($this->getLine());
		if ($responseId != 221)
			throw new SmtpException($this->getResponseText($responseId));
	}
	
	
	public function send ($recipient, $sender, $body, $header) {
		
		if (!$this->isLogged()) {
			throw new SmtpException("You're not logged.");
			return;
		}
		
		if (!Utils::isEmail($recipient)) {
			throw new SmtpException("Bad email format: ".$recipient);
			return;
		}		
	
		$this->execute("MAIL FROM:<".$sender.">");
		$responseId = (integer) $this->readLine($this->getLine());
		if ($responseId != 250)
			throw new SmtpException($this->getResponseText($responseId));
			
		$this->execute("RCPT TO:<".$recipient.">");
		$responseId = (integer) $this->readLine($this->getLine());
		if ($responseId != 250)
			throw new SmtpException($this->getResponseText($responseId));
			
			
		$this->execute("DATA");
		$responseId = (integer) $this->readLine($this->getLine());
		if ($responseId != 354)
			throw new SmtpException($this->getResponseText($responseId));	

		$msg = $header . $body;
		$this->execute($msg);

		$this->execute(".");
		$responseId = (integer) $this->readLine($this->getLine());
		if ($responseId != 250)
			throw new SmtpException($this->getResponseText($responseId));
	
	}

}

define("SMTP", true, true);
?> 
