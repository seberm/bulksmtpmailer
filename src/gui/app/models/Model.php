<?php

use Nette\Object;
use Nette\Database\Connection;
use Nette\InvalidStateException;


class Model extends Object {

    private static $m_keywords;
    private static $m_description;

    public static $database;
    
 
    public static function initialize($options) {

        self::$m_keywords = $options->keywords;
        self::$m_description = $options->description;
    }


    public static function getDescription() { return self::$m_description; }
    public static function getKeywords() { return self::$m_keywords; }


    public static function initDB($options) {
       
        self::$database = new Connection($options->driver.':host='.$options->host.';dbname='.$options->database, $options->username, $options->password);
    }


    public static function getTodos() {

        $data = self::$database->query("SELECT id, text, done FROM Todos;")->fetchAll();

        return $data;
    }
    
    
    public static function getNews($paginator) {

        $count = self::$database->table("News")->count();
        $paginator->setItemCount($count);
        
        $data = self::$database->table("News")
                               ->order("datetime DESC")
                               ->limit($paginator->getLength(), $paginator->getOffset());

        return $data;
    }


    public static function getScreenshots() {

        $data = self::$database->query("SELECT title, filename FROM Screenshots;")->fetchAll();
        //$data = self::$database->table("Screenshots")->select("title, filename")->fetchPairs()->toArray();
        //dump($data);
        return $data;
    }




    public static function addNews($title, $text) {

        return self::$database->exec("INSERT INTO `News` (`title`, `text`, `datetime`)
                                       VALUES (?, ?, NOW());", $title, $text);
    }


    public static function addTodo($text) {

        $data = array("text" => $text);
        return self::$database->table("Todos")->insert($data);
    }


    public static function addScreenshot($title, $filename) {

        $data = array("title" => $title,
                      "filename" => $filename);

        return self::$database->table("Screenshots")->insert($data);
    }


    public static function removeNews($id) {

        return self::$database->table("News")->where("id = ?", $id)->delete();
    }


    public static function removeTodo($id) {
        
        return self::$database->table("Todos")->where("id = ?", $id)->delete();
    }


    public static function removeScreenshot($id) {

        return self::$database->table("Screenshots")->where("id = ?", $id)->delete();
    }


    public static function markTodo($id, $done = false) {

        $data = array("done" => $done);
                    
        return self::$database->table("Todos")->where("id = ?", $id)->update($data);
    }

};



?>
