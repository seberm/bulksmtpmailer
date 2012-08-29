<?php
/** 
 * @interface MailerInterface
 * @file Mailer.interface.php
 * @author Otto Sabart <seberm@gmail.com>
 */


interface MailerInterface {

    public function send(Message $message, Mail $mail);

};


define('MailerInterface', true, true);
?>
