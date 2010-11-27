<?php
/** The system bulk class
 * @class Bulk
 * @throws BulkException
 * @file Bulk.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */


if (!defined("CURRENT_ROOT"))
	define("CURRENT_ROOT", "../", true);


### Classes
if (!defined("MAIL"))
	require_once (CURRENT_ROOT."classes/Mail.class.php");
	
if (!defined("MESSAGE"))
	require_once (CURRENT_ROOT."classes/Message.class.php");

if (!defined("SMTP"))
	require_once (CURRENT_ROOT."classes/SMTP.class.php");

### Exceptions
if (!defined("BULKEXCEPTION"))
   require_once(CURRENT_ROOT."exceptions/BulkException.class.php");
   
if (!defined("CRLF"))
	define ("CRLF", "\r\n", true);


class Bulk {
	
	private $_mails = Array();
	
	/** The pointer to instance of Smtp class
	 * @var $_Smtp
	 */
	private $_Smtp = null;
	
	private $_Message = null;
	
	
	
	function __construct ($messageID) {
		
		global $_MySql;
		global $_Config;
		
		try {
			$this->_Message = new Message($messageID);
		} catch (MessageException $e) {
			throw new BulkException($e->getStack());
			return;
		}
		
		$sqlMails = "SELECT `id` FROM `Mail`
					 WHERE `Sent` = false
					 LIMIT 0, ".$_Config['bulk']['batch'].";";

		$resMails = $_MySql->query($sqlMails);
		
		if (!$resMails->num_rows) {
			
			// Signal stopped is emitted
			$this->stopped();
			
			throw new BulkException("No mails to send in database");
			return;
		}
			
		while ($rowMail = $resMails->fetch_assoc())
			$this->_mails[] = new Mail ($rowMail['id']);
		
		
		// Initialize SMTP server
		$this->_Smtp = new SMTP($_Config['bulk']['smtp']['server'],
								$_Config['bulk']['smtp']['port'],
								$_Config['bulk']['smtp']['timeout'],
								$_Config['bulk']['smtp']['authType'],
								$_Config['bulk']['smtp']['smtpType']);
		
		try {
			
			$this->_Smtp->setLogin($_Config['bulk']['smtp']['login']);
			$this->_Smtp->setPassword($_Config['bulk']['smtp']['password']);
		} catch (SmtpException $e) {
			throw new BulkException($e->getStack());
		}
	}
	
	
	public function __toString () {
		
		return get_class($this);
	}
	
	
	private function getHeader ($recipient) {
		
		global $_Config;
		$output = "";
		
		$output .= "Message-Id: <".md5(@uniqid())."@".$_Config['bulk']['bound'].CRLF.">";
		$output .= "Date: ".Date(DATE_RFC822).CRLF;
		$output .= "From: <".$_Config['bulk']['from'].">".CRLF;
		$output .= "Reply-To: ".$_Config['bulk']['from'].CRLF;
		$output .= "MIME-Version: 1.0".CRLF;
		$output .= "To: ".$recipient.CRLF;
		$output .= "Subject: ".$this->_Message->getSubject().CRLF;
		$output .= "X-Priority: 3".CRLF;
		$output .= "X-MSMail-Priority: Normal".CRLF;
		$output .= "X-Mailer: ".$_Config['bulk']['mailer'].CRLF;
		$output .= "X-Originating-Email: ".$_Config['bulk']['from'].CRLF;
		
		
		return $output;
	}
	

	private function getBody () {
		
		global $_Config;
		$output = "";
	
		
$bound = $_Config['bulk']['bound'].time();
		///// toto je plain text kus zpravy
		$output .= "Content-Type: multipart/alternative; boundary=\"".$bound."\"".CRLF.CRLF;

		$output .= "This is a multi-part message in MIME format.".CRLF;
		$output .= "--".$bound.CRLF;
		$output .= "Content-Type: text/plain; charset=".$_Config['bulk']['charset'].CRLF;
		$output .= "Content-Transfer-Encoding: 7bit".CRLF.CRLF;
		
		$output .= "Zapnete zobrazovani obrazku.".CRLF.CRLF;
		$output .= "--".$bound.CRLF;
				
$bound2 = $_Config['bulk']['bound'].(time() + 5);
		$output .= "Content-Type: multipart/related; boundary=\"".$bound2."\"".CRLF.CRLF;

///// this is the HTML part of the message
		
		$output .= "--".$bound2.CRLF;
		$output .= "Content-Type: text/html; charset=".$_Config['bulk']['charset'].CRLF;
		$output .= "Content-Transfer-Encoding: 7bit".CRLF.CRLF;
		$output .= $this->getMessage().CRLF.CRLF;

/*
////picture attachments (just test) - it's working!
		$output .= "--".$bound2.CRLF;
		$output .= "Content-Type: image/jpeg; name=\"nevim.jpg\"".CRLF;
	
		$output .= "Content-Transfer-Encoding: base64".CRLF;
		$output .= "Content-ID: <part1.123456@xxx.com>".CRLF;
		$output .= "Content-Disposition: attachment; filename=\"nevim.jpg\"".CRLF.CRLF;
		
$output .= "
R0lGODlhQwBEAHcAMSH+GlNvZnR3YXJlOiBNaWNyb3NvZnQgT2ZmaWNlACH5BAEAAAAALAAAAABD
AEQAhwAAAAAAAAAAMwAAZgAAmQAAzAAA/wAzAAAzMwAzZgAzmQAzzAAz/wBmAABmMwBmZgBmmQBm
zABm/wCZAACZMwCZZgCZmQCZzACZ/wDMAADMMwDMZgDMmQDMzADM/wD/AAD/MwD/ZgD/mQD/zAD/
/zMAADMAMzMAZjMAmTMAzDMA/zMzADMzMzMzZjMzmTMzzDMz/zNmADNmMzNmZjNmmTNmzDNm/zOZ
ADOZMzOZZjOZmTOZzDOZ/zPMADPMMzPMZjPMmTPMzDPM/zP/ADP/MzP/ZjP/mTP/zDP//2YAAGYA
M2YAZmYAmWYAzGYA/2YzAGYzM2YzZmYzmWYzzGYz/2ZmAGZmM2ZmZmZmmWZmzGZm/2aZAGaZM2aZ
ZmaZmWaZzGaZ/2bMAGbMM2bMZmbMmWbMzGbM/2b/AGb/M2b/Zmb/mWb/zGb//5kAAJkAM5kAZpkA
mZkAzJkA/5kzAJkzM5kzZpkzmZkzzJkz/5lmAJlmM5lmZplmmZlmzJlm/5mZAJmZM5mZZpmZmZmZ
zJmZ/5nMAJnMM5nMZpnMmZnMzJnM/5n/AJn/M5n/Zpn/mZn/zJn//8wAAMwAM8wAZswAmcwAzMwA
/8wzAMwzM8wzZswzmcwzzMwz/8xmAMxmM8xmZsxmmcxmzMxm/8yZAMyZM8yZZsyZmcyZzMyZ/8zM
AMzMM8zMZszMmczMzMzM/8z/AMz/M8z/Zsz/mcz/zMz///8AAP8AM/8AZv8Amf8AzP8A//8zAP8z
M/8zZv8zmf8zzP8z//9mAP9mM/9mZv9mmf9mzP9m//+ZAP+ZM/+ZZv+Zmf+ZzP+Z///MAP/MM//M
Zv/Mmf/MzP/M////AP//M///Zv//mf//zP///wECAwECAwECAwECAwECAwECAwECAwECAwECAwEC
AwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwEC
AwECAwECAwECAwECAwECAwECAwECAwECAwECAwECAwj/AAEIHEiwoMGDCBMqXMiw4UBBgVgJYhUI
IkSBgqw43MhRIIsrGFmJNCWyZMlVIit6ZNGx5cAAAiWWnGiyJsWbESOCdOmSZk2SI2eyIlmKValV
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
+etoT3tBAgIAOw==
".CRLF;

		$output .= "--".$bound2."--".CRLF.CRLF;
*/
		$output .= "--".$bound."--".CRLF;
		
		
		return $output;
	}
	
	
	public function getMessage() {
		
		global $_Config;
		$output = "";
		
		$output .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
		$output .= "<html>";
		$output .= "<head>";
		$output .= "<meta http-equiv=\"content-type\" content=text/html; charset=".$_Config['bulk']['charset']."\">";
		$output .= "</head>";
		$output .= "<body>";

		$output .= $this->_Message->getText();

		$output .= "</body>";
		$output .= "</html>";

//<img alt="nezobrazuje se obrazek" title="tooooooltip" src="cid:part1.123456@xxx.com" height="75" width="100">

		return $output;
	}
	
	
	public function sendBatch () {
		
		global $_Config;
		
		try {
			if ($this->_Smtp->connect()) { // Connect to the SMTP server
				$this->_Smtp->login(); // Login to SMTP server
				foreach ($this->_mails as $mail) {
					$mailHeader = $this->getHeader($mail->getEmail());
					$mailBody = $this->getBody();
					
					$this->_Smtp->send($mail->getEmail(), $_Config['bulk']['from'], $mailBody, $mailHeader);
				}	
				
				Mail::markSent($this->_mails);
				
				$this->_Smtp->disconnect();
			} else {
				// We can send email alternatively, bad it's the bad way. (We can give an error from server)
				foreach ($this->_mails as $mail) {
					$mailHeaders = "";
					$mailHeaders .= "From: ".$_Config['bulk']['from'].CRLF;
					$mailHeaders .= "Reply-To: ".$_Config['bulk']['from'].CRLF;
					$mailHeaders .= "X-Mailer: ".$_Config['bulk']['mailer'].CRLF;
					
					mail($mail->getEmail(), $this->_Message->getSubject(), $this->_Message->getText(), $mailHeaders);
				}
			}
		} catch (SmtpException $e) {
			throw new BulkException($e->getStack());
			return;
		}
	}
	
	
	private static function stopped () {
		
		global $_MySql;
		
		$sql1 = "UPDATE `System`
				 SET `Value` = false
				 WHERE `Item` = 'ActiveSending';";
				
		$sql2 = "UPDATE `System`
				 SET `Value` = 0
				 WHERE `Item` = 'SendingMessageId';";
				
		Mail::markUnsent();
				 
		return ($_MySql->query($sql1) && $_MySql->query($sql2));
	}
	
	
	private static function started () {
		
		global $_MySql;
		
		$sql = "UPDATE `System`
				SET `Value` = true
				WHERE `Item` = 'ActiveSending';";
				
		return $_MySql->query($sql);
	}
	
	
	/** Starts the sending of emails
	 */
	public function start () {
		
		$this->started();
		$this->sendBatch();
	}
}

define("BULK", true, true);
?> 
