<?php
/** The SMTP class
 * @class SMTP
 * @throws SMTPException
 * @file SMTP.class.php
 * @author Otto Sabart <seberm@gmail.com>
 *
 * RFC 2821 - Simple Mail Transfer Protocol
 * Some methods are inspired by Nette. Thank you David Grudl.
 */

if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);


### Exceptions
if (!defined("SMTPEXCEPTION"))
   require_once(CURRENT_ROOT."exceptions/SMTPException.class.php");


if (!defined("UTILS"))
   require_once(CURRENT_ROOT."classes/Utils.class.php");


if (!defined("MESSAGE"))
   require_once(CURRENT_ROOT."classes/Message.class.php");


class SMTP {

    // Constants
    const TIMEOUT = 30;
    const AUTHTYPE = "LOGIN";
    const SMTPTYPE = "ESMTP";
    const DEFAULT_PORT = 25;
    const SSL_PORT = 465;

	/** Final-server address
	 * @var string $m_server
	 */
	private $m_server;
	
	/** Final-server port
	 * @var int $m_port;
	 */
	private $m_port;
	
	/** Proxy server address
	 * @var string $m_proxyServer
	 */
	private $m_proxyServer;
	
	/** Proxy server port
	 * @var int $m_proxyPort
	 */
	private $m_proxyPort;
	
	/** Indicates if we're using proxy connection
	 * @var boolean $m_useProxy
	 */
	private $m_useProxy = false;
	
	/** Contains a server SMTP type
	 * @var string $m_smtpType
	 */
	private $m_smtpType;
	
	/** Contains current authorization type
	 * @var string $m_authType
	 */
	private $m_authType;
	
	/** Connection timeout
	 * @var int $m_timeout
	 */
	private $m_timeout;
	
	/** Contains a SMTP username
	 * @var string $m_login
	 */
	private $m_login;
	
	/** Contains a SMTP password
	 * @var string $m_password
	 */
	private $m_password;
	
	/** Pointer to SMTP socket
	 * @var socket $m_socket
	 */
	private $m_socket = null;

    /** Should be connection secured?
     * @var string $m_secure [ ssl | tls | (empty) ]
     */
    private $m_secure;
	
	/** Indicates if user logged
	 * @var boolean $m_logged
	 */
	private $m_logged = false;
	
	/** Indicates if we're connected to the server
	 * @var boolean $m_connected
	 */
	private $m_connected = false;
	
	/** An Array of auth types which are supported by server we're connecting to
	 * @var array $m_supportedAuthTypes
	 */
	private $m_supportedAuthTypes = array();
	
	/** SMTP Auth methods which are supported by this code
	 * @var array $AUTH_TYPES
	 */
    private $SMTP_AUTH_TYPES = array("LOGIN",
                             "PLAIN",
                          /* "DIGEST-MD5", 
                             "CRAM-MD5",
                             "GSSAPI", */
                            );
	
	/** Possible SMTP types
     * @var array $SMTP_TYPES
     */
    private $SMTP_TYPES = array("SMTP",
                                "ESMTP",
                               );
	
	/** Definition of possible errors
	 * @var array SMTP::RESPONSES
	 */
	private $SMTP_RESPONSES = array(
								200 => "(nonstandard success response, see rfc876)",
								211 => "System status, or system help reply",
								214 => "Help message",
								220 => "Service ready",
						  		221 => "Service closing transmission channel",
								250 => "Requested mail action okay, completed",
								251 => "User not local",
								354 => "Start mail input; end with <EOL>.<EOL>",
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
                                554 => "Transaction failed",
                            );
								
	
	/** Constructor
	 * @param $server Server we are connecting to
	 * @param $port Port of the server
	 * @param $timeout The connection timeout
	 */
	function __construct($server, $port, $secure, $timeout = TIMEOUT, $authType = AUTH_TYPE) {
		
		$this->m_server = isset($server) ? $server : "";
		$this->m_port = isset($port) ? (int) $port : DEFAULT_PORT;
		$this->m_timeout = (int) $timeout; 
        $this->m_secure = $secure;
		
		if (in_array($authType, $this->SMTP_AUTH_TYPES, true))
			$this->m_authType = $authType;
		else $this->m_authType = AUTH_TYPE;

        if (!$this->m_port)
            $this->m_port = ($this->m_secure === "ssl") ? SSL_PORT : $port;
	}
	
	
	/** Destructor
	 * Disconects if we are connected because we should end connection correctly.
	 */
	function __destruct() {

		if ($this->isConnected())
			$this->disconnect();
	}

	
    /** __toString
     * Returns class name.
     * @return string
     */
	public function __toString() {
		
		return get_class($this);
	}
	
	
	// Getters
	public function getServer() { return $this->m_server; }
	public function getPort() { return $this->m_port; }
	public function getLogin() { return $this->m_login; }
	public function getTimeout() { return $this->m_timeout; }
	public function getProxyServer() { return $this->m_proxyServer; }
	public function getProxyPort() { return $this->m_proxyPort; }
    public function getSmtpType() { return $this->m_smtpType; }
	
	
	/** Returns true if we are logged to SMTP server
	 * @return boolean
	 */
	public function isLogged() { return $this->m_logged; }
	
	
	/** Returns true if we are connected to SMTP server
	 * @return boolean
	 */
	public function isConnected() { return $this->m_connected; }
	
	
	/** Opens a connection to SMTP server
     * @return boolean
	 */
	public function connect() {
		
		$server = $this->m_server;
		$port = $this->m_port;
		
		if ($this->m_useProxy) {
			
			$server = $this->m_proxyServer;
			$port = $this->m_proxyPort;
		}
        
        // Should be connection secured via SSL?
        $server = (($this->m_secure === "ssl") ? "ssl://" : "") . $server;
		
        $ret = $this->m_socket = @fsockopen($server, $port, $errno, $errstr, $this->m_timeout);
		
		if (!is_resource($this->m_socket))
			throw new SMTPException($errstr, $errno);
		
        stream_set_timeout($this->m_socket, $this->m_timeout, 0);

		// We're connected
		$this->m_connected = true;
		
		// It's very important to call this function because we must come over the greeeting message
		$this->read();
		
        $this->identify();
        $this->login();

        return $ret;
	}
	
	
	/** Removes the SMTP server socket
	 * @return boolean
	 */
	public function disconnect() {
		
		if (!$this->m_connected)
			return true;
		
		$this->quit();
		
		// We're disconnected
		$this->m_connected = false;
		
		return fclose($this->m_socket);
	}
	
	
	/** Sets the SMTP username
	 * @param string $login
     * @return SMTP
	 */
	public function setLogin($login) {
		
		if (empty($login))
			throw new SMTPException("you're setting an empty login");
		
		$this->m_login = $login;

        return $this;
	}
	
	
	/** Sets the SMTP server password
	 * @param string $password
     * @return SMTP
	 */
	public function setPassword ($password) {
		
		$this->m_password = $password;

        return $this;
	}
	
	
    /** Executes a command on SMTP server
     * Inspired by Nette framework - Thanks!
	 * @param string $command
     * @param int $expectedCode response code
	 * @see smtpLogin
	 * @return void
	 */
	private function write($command, $expectedCode = NULL) {
        
        fwrite($this->m_socket, $command . Message::EOL);
        
        if ($expectedCode) {  

            $returnedCode = (int) $this->read();
            if (!in_array($returnedCode, (array) $expectedCode))
                throw new SMTPException("SMTP server did not accept " . $command . "; " . $this->getResponseText($returnedCode));
        }
	}
	
	
	/** Gets a line from the socket connection
     * Inspired by Nette framework - Thanks!
	 * @return string
	 */
	private function read() {
		
		$s = "";
		
		if (!$this->isConnected())
			throw new SMTPException("server is not connected");
			
        while (($line = fgets($this->m_socket, 1e3)) != NULL) {
				
            $s .= $line;

            if (substr($line, 3, 1) === ' ')
                break;
		}
        
        return $s;
	}
	
	
	/** Reads a line from 0 to $chars chars
	 * @param string $line
	 * @return int
	 */
    /*
	private function readLine ($line) {
		
		if (empty($line))
			return "";

		// Configuration of SMTP type	
		if (preg_match("/220\s[\w-.]+\s(?P<opt>\w+)/", $line, $matches)) {

			if (in_array($matches['opt'], $this->SMTP_TYPES, true))
				$this->m_smtpType = $matches['opt'];
			else $this->m_smtpType = SMTP_SMTPTYPE;
		}

		// Configuration of supported authorization types
		if (preg_match("/250-(?P<cmd>\w)\s(?P<opt>\w+)/", $line, $matches)) {
			
			if (is_null($matches))
				return 0;
			
			switch ($matches['cmd']) {
				
				case "AUTH":
					
					$this->m_supportedAuthTypes = explode(" ", strtoupper($matches['opt']));
					break;
					
				case "SIZE":
					
					//$this->_xxx = $matches['opt'];
					break;
				
				case "VRFY":
				case "ETRN":
				case "XVERP":
				case "PIPELINING":
				case "STARTTLS":
				default:
					break;
			}
		}
	
		return ((int) substr(trim($line), 0, 3));
	}
	
     */

	
	/** Returns a server response text by given response id
	 * @param int $key
	 * @return string
	 */
	public function getResponseText($key = 0) {
		
		$responseText = "";
	
		if (array_key_exists($key, $this->SMTP_RESPONSES))
			$responseText = $this->SMTP_RESPONSES[$key];
		else
			$responseText = "unknown SMTP response";
		
		
		return $responseText;
	}
	
	
	/** Logins to a SMTP server
	 * @see connect
	 * @see disconnect
	 */
	private function login() {
		
		$login = $this->m_login;
		$password = $this->m_password;
		
        if ($this->m_secure === "tls") {

            $this->write("STARTTLS", 220);
            if (!stream_socket_enable_crypto($this->m_socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT))
                throw new SMTPException("unable to connect via TLS");
        }

        $this->write("AUTH LOGIN", 334);
        $this->write(base64_encode($login), 334);
        $this->write(base64_encode($password), 235);

        $this->m_logged = true;

        /** 
         * @todo it's neccessary to programme other autorization methods
         */
        /*
		switch ($this->m_authType) {

			case "PLAIN":
			
				// It's neccessary to keep this syntax: 'username\0username\0password'
				$log = base64_encode($login."\0".$login."\0".$password);
				
				$this->execute("AUTH PLAIN".$log);
				$responseID = $this->readLine($this->getLine());
				if ($responseID != 235)
					throw new SmtpException($this->getResponseText($responseID));
				
				$this->m_logged = true;
				
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
				if ($responseID != 334)
					throw new SmtpException($this->getResponseText($responseID));
				
				$this->execute($passwordENC);
				$responseID = $this->readLine($this->getLine());
				if ($responseID != 334)
					throw new SmtpException($this->getResponseText($responseID));
					
				$responseID = $this->readLine($this->getLine());
				if ($responseID != 235)
					throw new SmtpException($this->getResponseText($responseID));
				
				$this->m_logged = true;
 		
				break;
		}
         */
	}
	
	
	/** Says a HELO to a SMTP server
	 * @return boolean
	 */
	private function helo($server) {
        
        $this->write("HELO " . $server);

        if ((int) $this->read() !== 250)
            return false;

        $this->m_smtpType = "SMTP";
		return true;

	}
	
	
	/** Says a EHLO to a eSMTP server
	 * @return boolean
	 */
	private function ehlo($server) {
		
		$this->write("EHLO " . $server);

        if ((int) $this->read() !== 250)
            return false;

        $this->m_smtpType = "ESMTP";
		return true;
	}
	

	private function identify() {
    
        $server = $this->m_server;

        // Are we using the proxy?
		if ($this->m_useProxy)
			$server = $this->m_server;

	    if (!$this->ehlo($server))
            $this->helo($server);    
	}
	
	
	private function quit() {
		
		$this->write("QUIT", 221);
	}
	
	
	public function send(Message $message, Mail $mail) {
		
        /** @todo predelat nacitani konfigurace */
        global $_Config;

		if (!$this->isLogged())
			throw new SMTPException("you're not logged in");
		
		if (!Utils::isEmail($mail->getEmail()))
			throw new SMTPException("bad email format: " . $mail->getEmail());

        $this->write("MAIL FROM:<" . $_Config['bulk']['from'] . ">", 250);
        $this->write("RCPT TO:<" . $mail->getEmail() . ">", array(250, 251));
        $this->write("DATA", 354);

        $data = $message->generateMimeMessage();
        $this->write($data);
        $this->write(".", 250);
	}
	
	
	public function useProxy($server, $port) {
		
		$this->m_proxyServer = $server;
		$this->m_proxyPort = isset($port) ? (int) $port : 0;
		$this->m_useProxy = true;

        return $this;
	}
}


define("SMTP", true, true);

?> 
