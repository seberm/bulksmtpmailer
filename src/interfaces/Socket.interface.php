<?php
/** 
 * @interface SocketInterface
 * @file Socket.interface.php
 * @author Otto Sabart <seberm@gmail.com>
 */


interface SocketInterface {

    public function connect();
    public function disconnect();

    // Socket status
    public function isConnected();

};


define("SocketInterface", true, true);
?>
