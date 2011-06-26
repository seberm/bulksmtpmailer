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
	
    const ENCODING_BASE64           = "base64";
    const ENCODING_7BIT             = "7bit";
    const ENCODING_8BIT             = "8bit";
    const ENCODING_QUOTED_PRINTABLE = "quoted-printable";

    const EOL = "\r\n";

    private $m_headers = array();

	private $m_subject;
	private $m_text;
	private $m_id;
	
	
	function __construct ($messageID) {
		
		global $_MySql;
		
		$sqlMessage = "SELECT `id`, `subject`, `text`
					   FROM `Message`
					   WHERE `id` = ".$messageID.";";

		$resMessage = $_MySql->query($sqlMessage);

		if ($resMessage->num_rows == 0)
			throw new MessageException("Message (ID: ".$messageID.") does not exist");

		$rowMessage = $resMessage->fetch_assoc();
		
		$this->m_id = $rowMessage['id'];

		$this->setSubject($rowMessage['subject']);
		$this->setText($rowMessage['text']);

        $this->setHeader("MIME-Version", "1.0");
        $this->setHeader("Date", date("r"));
	}
	
	
	public function __toString () {
		
		return get_class($this);
	}


    // Getters	
	public function getText() { return $this->m_text; }
	public function getSubject() { return $this->m_subject; }
    public function getID() { return $this->m_id; }
    public function getHeaders() { return $this->m_headers; }


    public function getHeader($name) {

        return isset($this->m_headers[$name]) ? $this->m_headers[$name] : NULL;
    }


    // Setters
    public function setSubject($subject) {

        $this->m_subject = $subject;
        return $this;
    }


    public function setText($text) {

        $this->m_text = $text;
        return $this;
    }


    public function setPriority($priority) {

        $this->setHeader("X-Priority", (int) $priority);
        return $this;
    }


    public function setContentType($type) {

        $this->setHeader("Content-Type", $type . "UTF-8");
        return $this;
    }


    public function setEncoding($encoding) {

        $this->setHeader("Content-Transfer-Encoding", $encoding);
        return $this;
    }


    public function setHeader($name, $value) {

        if (empty($name))
            throw new MessageException("you're setting empty header");

        $this->m_headers[$name] = preg_replace("#[\r\n]+#", ' ', $value);

        return $this;
    }


    public function addReplyTo($email) {

        if (!Utils::isEmail($email))
            throw new MessageException("you're setting bad e-mail format");

        $this->setHeader("Reply-To", $email);

        return $this;
    }


    private function buildMessage() {

        global $_Config;

        $server = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "localhost"; // default loopback - 127.0.0.1

        $this->setHeader("Message-ID", "<" . Utils::randomString() . "@" . $server . ">");

        switch ($_Config['bulk']['contentType']) {

            case "html":
                $this->setContentType("text/html", "UTF-8");
                break;

            case "plain":
            default:
                $this->setContentType("text/plain", "UTF-8");
                break;
        }
        
        $this->setEncoding(self::ENCODING_7BIT);
    }


    public function generateMimeMessage() {

        $output = "";
        $bound = "--------" . Utils::randomString();

        foreach ($this->m_headers as $name => $value) {

            $output .= $name . ":" . $value;
            $output .= self::EOL;
        }

        $output .= self::EOL;

        /** @todo encoding switch */
        $body = $this->getText();
        $body = preg_replace('#[\x80-\xFF]+#', '', $body);
        $body = str_replace(array("\x00", "\r"), '', $body);
        $body = str_replace("\n", self::EOL, $body);        
        $output .= $body;

        return $output;
    }
};


define("MESSAGE", true, true);

?> 
