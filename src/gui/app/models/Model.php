<?php

use Nette\Object,
    Nette\Database\Connection,
    Nette\InvalidStateException,
    Nette\Environment;


class Model extends Object {

    // Database should be accessible only from this object!
    private static $database;
    
    public static $translator;
    
 
    public static function initialize($options) {

    }


    public static function initDB($options) {
       
        self::$database = new Connection($options->driver.':host='.$options->host.';dbname='.$options->database, $options->username, $options->password);
    }


    public static function initLocalization($options) {
        
        $lang = $options->lang; // Default app language

        $session = Environment::getSession("App");
        if (isset($session->lang))
            $lang = $session->lang;

        switch ($lang) {
            case "en":
            case "cs":
                $l = $lang;
                break;

            default:
                $l = "en";
                break;
        }

        $file = $options->langDir . "/app." . $l . ".mo";
        self::$translator = new GettextTranslator($file, $l);
    }


    public static function getQueues() {

        $data = self::$database->query("SELECT id, name, messageID, isSending, isCompleted FROM Queue ORDER BY id DESC;")->fetchAll();

        return $data;
    }

/*
    public static function getQueue($id) {

        return self::$database->table("Queue")->where("id = ?", $id)->createJoins("Queue.messageID => Message.id")->$select("name, isSending, isCompleted")->fetch();
    }
*/
   
    public static function addQueue($name, $messageID, $isSending = false, $isCompleted = false) {

        $data = array("name" => $name,
                      "messageID" => $messageID,
                      "isSending" => $isSending,
                      "isCompleted" => $isCompleted,
                     );

        return self::$database->table("Queue")->insert($data);
    }
    
    
    public static function removeQueue($id) {

        return self::$database->table("Queue")->where("id = ?", $id)->delete();
    }

    
    public static function addMessage($subject, $text) {

        $data = array("subject" => $subject,
                      "text" => $text,
                     );

        return self::$database->table("Message")->insert($data);
    }

    
    public static function getMessages() {

        $data = self::$database->query("SELECT id, subject, text FROM Message ORDER BY id DESC;")->fetchAll();
                
        return $data;
    }


    public static function getMessage($id) {

        return self::$database->table("Message")->where("id = ?", $id)->select("subject, text")->fetch();
    }
    
    
    public static function updateMessage($subject, $text, $id) {

        $data = array("subject" => $subject,
                      "text" => $text,
                     );

        return self::$database->table("Message")->where("id = ?", $id)->update($data);
    }
    

    public static function removeMessage($id) {

        return self::$database->table("Message")->where("id = ?", $id)->delete();
    }

    
    public static function removeMail($id) {

        return self::$database->table("Mail")->where("id = ?", $id)->delete();
    }

    
    public static function getMails() {

        $data = self::$database->query("SELECT id, name, email, sent FROM Mail ORDER BY id DESC;")->fetchAll();
                
        return $data;
    }


    public static function getMail($id) {

        return self::$database->table("Mail")->where("id = ?", $id)->select("name, email, sent")->fetch();
    }


    public static function addMail($name, $email, $sent = false) {

        $data = array("name" => $name,
                      "email" => $email,
                      "sent" => $sent,
                     );

        return self::$database->table("Mail")->insert($data);
    }


    public static function updateMail($name, $email, $id, $sent = false) {

        $data = array("name" => $name,
                      "email" => $email,
                      "sent" => $sent,
                     );

        return self::$database->table("Mail")->where("id = ?", $id)->update($data);
    }


    public static function startSending($id) {

        $data = array("isSending" => true);
        
        return self::$database->table("Queue")->where("id = ?", $id)->update($data);
    }


    public static function removeUser($login) {

        return self::$database->table("Users")->where("login = ?", $login)->delete();
    }


    public static function getUser($login) {

        return self::$database->table("Users")->where("login = ?", $login)->select("id, realName, password")->fetch();

    }

};

?>
