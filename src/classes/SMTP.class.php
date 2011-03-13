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
define ($prefix."AUTHTYPE", "LOGIN");
define ($prefix."SMTPTYPE", "SMTP");

if (!defined("CRLF"))
	define ("CRLF", "\r\n", true);


class SMTP {
	
	/** Final-server address
	 * @var string $_server
	 */
	private $_server;
	
	/** Final-server port
	 * @var int $_port;
	 */
	private $_port;
	
	/** Proxy server address
	 * @var string $_proxyServer
	 */
	private $_proxyServer;
	
	/** Proxy server port
	 * @var int $_proxyPort
	 */
	private $_proxyPort;
	
	/** Indicates if we're using proxy server
	 * @var boolean $_useProxy
	 */
	private $_useProxy = false;
	
	/** Contains a server SMTP type
	 * @var string $_smtpType
	 */
	private $_smtpType;
	
	/** Contains a authorization type
	 * @var string $_authType
	 */
	private $_authType;
	
	/** Connection timeout
	 * @var int $_timeout
	 */
	private $_timeout;
	
	/** Contains a SMTP username
	 * @var string $_login
	 */
	private $_login;
	
	/** Contains a SMTP password
	 * @var string $_password
	 */
	private $_password;
	
	/** Pointer to SMTP socket
	 * @var socket $_socket
	 */
	private $_socket = null;
	
	/** Indicates if user logged
	 * @var boolean $_logged
	 */
	private $_logged = false;
	
	/** Indicates if we're connected to the server
	 * @var boolean $_connected
	 */
	private $_connected = false;
	
	/** An Array of auth types which are supported by server we're connecting to
	 * @var array $_supportedAuthTypes
	 */
	private $_supportedAuthTypes = Array();
	
	/** SMTP Auth methods which are supported by this code
	 * @var array $SMTP_AUTH_TYPES
	 */
	private $SMTP_AUTH_TYPES = Array("LOGIN", "PLAIN"/*, "DIGEST-MD5", "CRAM-MD5", "GSSAPI"*/);
	
	private $SMTP_TYPES = Array("SMTP", "ESMTP");
	
	/** Definition of possible errors
	 * @var array $SMTP_TYPES
	 */
	private $SMTP_RESPONSES = Array(
								200 => "(nonstandard success response, see rfc876)",
								211 => "System status, or system help reply",
								214 => "Help message",
								220 => "Service ready",
						  		221 => "Service closing transmission channel",
								250 => "Requested mail action okay, completed",
								251 => "User not local",
								354 => "Start mail input; end with <CRLF>.<CRLF>",
								421 => "Service not available, closing transmission channel",
								450 => "Requested mail action not taken: mailbox unavailable",
								451 => "Requested action aborted: local error in processing",
								452 => "Requested action not taken: insufficient system storage",
								500 => "Syntax error, command unrecognised",
								501 => "Syntax error in parameters or arguments",
								502 => "Command not implemented",
								503 => "Bad sequence of commands",
								504 => "Command parameter not implemented",
								521 => "System does not accept mail (see rfc1846)",
								530 => "Access denied (???a Sendmailism)",
								550 => "Requested action not taken: mailbox unavailable",
								551 => "User not local",
								552 => "Requested mail action aborted: exceeded storage allocation",
								553 => "Requested action not taken: mailbox name not allowed",
								554 => "Transaction failed");
								
	
	/** Constructor
	 * @param $server Server we are connecting to
	 * @param $port Port of the server
	 * @param $timeout The connection timeout
	 */
	function __construct ($server, $port, $timeout = SMTP_TIMEOUT, $authType = SMTP_AUTH_TYPE) {
		
		$this->_server = $server;
		$this->_port = is_numeric($port) ? $port : 0;
		$this->_timeout = is_numeric($timeout) ? $timeout : 0;
		
		if (in_array($authType, $this->SMTP_AUTH_TYPES, true))
			$this->_authType = $authType;
		else $this->_authType = SMTP_AUTH_TYPE;

	}
	
	
	/** Destructor
	 * Disconects if we are connected.
	 */
	function __destruct () {

		if ($this->isConnected())
			$this->disconnect();
	}

	
	public function __toString () {
		
		return get_class($this);
	}
	
	
	// GET methods
	public function getServer () { return $this->_server; }
	public function getPort () { return $this->_port; }
	public function getLogin () { return $this->_login; }
	public function getTimeout () { return $this->_timeout; }
	public function getProxyServer () { return $this->_proxyServer; }
	public function getProxyPort () { return $this->_proxyPort; }
	
	
	/** Returns true if we are logged to SMTP server
	 * @return boolean
	 */
	public function isLogged () { return $this->_logged; }
	
	
	/** Returns true if we are connected to SMTP server
	 * @return boolean
	 */
	public function isConnected () { return $this->_connected; }
	
	
	/** Opens a connection to SMTP server
	 * return boolean
	 */
	public function connect () {
		
		$server = $this->_server;
		$port = $this->_port;
		
		if ($this->_useProxy) {
			
			$server = $this->_proxyServer;
			$port = $this->_proxyPort;
		}
		
        $this->_socket = @fsockopen($server, $port, $errno, $errstr, $this->_timeout);
		
		if (!is_resource($this->_socket)) {
			
			throw new SmtpException("failed to open a SMTP connection (".$errno." - ".$errstr.")");
			return false;
		}
		
		// So,.. we're connected
		$this->_connected = true;
		
		// It's very important to call this function because we must come over the welcome message
		$this->readLine($this->getLine());
		
		$ret = $this->identify();
		if (!$ret && $this->isConnected())
			$this->disconnect();
		
		return $ret;
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
		
		if (!is_resource($this->_socket))
			return true;

		return fclose($this->_socket);
	}
	
	
	/** Sets the SMTP username
	 * @param String $login
	 */
	public function setLogin ($login = "") {
		
		if (empty($login)) {
			
			throw new SmtpException("you're setting an empty login");
			return;
		}
		
		$this->_login = $login;
	}
	
	
	/** Sets the SMTP server password
	 * @param String $password
	 */
	public function setPassword ($password = "") {
		
		if (empty($password)) {
			
			throw new SmtpException("you're setting an empty password");
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
echo $cmd;
		if (!$this->isConnected()) {
			
			throw new SmtpException("server is not connected");
			return false;
		}
		
		return fwrite($this->_socket, $cmd, strlen($cmd));
	}
	
	
	/** Gets a line from the socket connection
	 * @return boolean or string
	 */
	private function getLine () {
		
		$line = "";
		$ret = "";
		
		if (!$this->isConnected()) {
			
			throw new SmtpException("server is not connected");
			return false;
		}
			
/** @todo edit: || substr($line, 3, 1) !== " " -> it's weird */

		while((strpos($ret, CRLF) === false) || substr($line, 3, 1) !== " ") {
//		while (preg_match("/[a-zA-Z0-9]".CRLF."/", $line)) {
				
			$line = fgets($this->_socket, 512);
			$ret .= $line;
		}
					
		if (empty($ret))
			return false;
				
		return $ret;
	}
	
	
	/** Reads a line from 0 to $chars chars
	 * @param string $line
	 * @return integer
	 */
	private function readLine ($line) {
		
		if (empty($line))
			return "";

		// Configuration of SMTP type	
		if (preg_match("/220\s[\w-.]+\s(?P<opt>\w+)/", $line, $matches)) {

			if (in_array($matches['opt'], $this->SMTP_TYPES, true))
				$this->_smtpType = $matches['opt'];
			else $this->_smtpType = SMTP_SMTPTYPE;
		}

		// Configuration of supported authorization types
		if (preg_match("/250-(?P<cmd>\w)\s(?P<opt>\w+)/", $line, $matches)) {
			
			if (is_null($matches))
				return 0;
			
			switch ($matches['cmd']) {
				
				case "AUTH":
					
					$this->_supportedAuthTypes = explode(" ", strtoupper($matches['opt']));
					break;
					
				case "SIZE":
					
					//$this->_xxx = $matches['opt'];
					break;
					
				case "PIPELINING":
				case "STARTTLS":
				default:
					break;
			}
		}
	
		return ((int) substr(trim($line), 0, 3));
	}
	
	
	/** Returns a server response text by given response id
	 * @param int $key
	 * @return string
	 */
	private function getResponseText($key = 0) {
		
		$responseText = "";
	
		if (array_key_exists($key, $this->SMTP_RESPONSES)) {
//! \todo proc bylo tady			
			$responseText = $this->SMTP_RESPONSES[$key];
//! \todo a tady na konci retezce pridano '\n'
		} else $responseText = "unknown SMTP response";
		
		
		return $responseText;
	}
	
	
	/** Logins to a SMTP server
	 * @see connect
	 * @see disconnect
	 */
	public function login () {
		
		if (!$this->isConnected()) {
			
			throw new SmtpException("server is not connected");
			return;
		}
	
		$login = $this->_login;
		$password = $this->_password;
		
/** @todo it's neccessary to programme other autorization methods */
		switch ($this->_authType) {
			case "PLAIN":
			
				// It's neccessary to keep this syntax: 'username\0username\0password'
				$log = base64_encode($login."\0".$login."\0".$password);
				
				$this->execute("AUTH PLAIN".$log);
				$responseID = $this->readLine($this->getLine());
				if ($responseID != 235) {
					throw new SmtpException($this->getResponseText($responseID));
					return false;
				}
				
				$this->_logged = true;
				
				break;
			
			//case "DIGEST-MD5":
			//case "CRAM-MD5":
			//case "GSSAPI":
			
			case "LOGIN":
			default:
				$loginENC = base64_encode($login);
				$passwordENC = base64_encode($password);
				$this->execute("AUTH LOGIN");
				

				$this->execute($loginENC);
				$responseID = $this->readLine($this->getLine());
				if ($responseID != 334) {
					
					throw new SmtpException($this->getResponseText($responseID));
					return false;
				}
				
				$this->execute($passwordENC);
				$responseID = $this->readLine($this->getLine());
				if ($responseID != 334) {
					
					throw new SmtpException($this->getResponseText($responseID));
					return false;
				}
					
				$responseID = $this->readLine($this->getLine());
				if ($responseID != 235) {
					
					throw new SmtpException($this->getResponseText($responseID));
					return false;
				}
				
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
		
		$responseID = $this->readLine($this->getLine());
		if ($responseID != 250) {
			
			throw new SmtpException($this->getResponseText($responseID));
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
		
		
		$server = $this->_server;
		
		// Are we using the proxy?
		if ($this->_useProxy)
			$server = $this->_proxyServer;
		
		$this->execute("EHLO ".$server);
		$responseID = $this->readLine($this->getLine());

		if ($responseID != 250) {
			
			throw new SmtpException($this->getResponseText($responseID));
			return false;
		}	
		
		return true;
	}
	

	private function identify () {
		
		if (!$this->isConnected())
			return false;

		$returnStat = false;
		switch ($this->_smtpType) {
			
			case "ESMTP":
				$returnStat = $this->ehlo();
				break;
				
			case "SMTP":
			default:
				$returnStat = $this->helo();
				break;
		}
		
		return $returnStat;
	}
	
	
	public function quit () {
		
		$this->execute("QUIT");
		
		$responseID = $this->readLine($this->getLine());
		if ($responseID != 221) {
			
			throw new SmtpException($this->getResponseText($responseID));
			return;
		}
	}
	
	
	public function send ($recipient, $sender, $body, $header) {
		
		if (!$this->isLogged()) {
			
			throw new SmtpException("you're not logged in");
			return;
		}
		
		if (!Utils::isEmail($recipient)) {
			
			throw new SmtpException("bad email format: ".$recipient);
			return;
		}		
	
		$this->execute("MAIL FROM:<".$sender.">");
		$responseID = $this->readLine($this->getLine());
		if ($responseID != 250){
			
			throw new SmtpException($this->getResponseText($responseID));
			return;
		}
		
		$this->execute("RCPT TO:<".$recipient.">");
		$responseID = $this->readLine($this->getLine());
		if ($responseID != 250) {
			
			throw new SmtpException($this->getResponseText($responseID));
			return;
		}
			
		$this->execute("DATA");
		$responseID = $this->readLine($this->getLine());
		if ($responseID != 354) {
			
			throw new SmtpException($this->getResponseText($responseID));
			return;
		}
		
		$msg = $header . $body;
		$this->execute($msg);

		$this->execute(CRLF.".");
		$responseID = $this->readLine($this->getLine());
		if ($responseID != 250) {
			
			throw new SmtpException($this->getResponseText($responseID));
			return;
		}
	
	}
	
	
	public function useProxy($server, $port) {
		
		$this->_proxyServer = $server;
		$this->_proxyPort = is_numeric($port) ? $port : 0;
		$this->_useProxy = true;
	}
}


define("SMTP", true, true);

?> 
