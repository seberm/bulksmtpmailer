<?php
/** Main page configuration file
 * @author Otto Sabart <seberm@gmail.com>
 */


$_Config = Array(

### DATABASE SERVER ###
"mysql" => Array(

### Server
"server" => "localhost", // Examples: "localhost", "192.168.3.122", "81.35.68.121", "82.35.58.119:3306"

### User
"username" => "seberm",

### Password
"password" => "mandarinka",

### Database name
"database" => "bulk"

),


### BULK APPLICATION ###
"bulk" => Array(

### Bound
"bound" => "science-agency.cz",

### Mailer
"mailer" => "phpSMTPBulk",

### Charset
"charset" => "utf-8",

### From
"from" => "karkulka@panasek.com",

### Message content type
"contentType" => "html", // plain X html

### SMTP conf
"smtp" => Array("server" => "smtp_server_address", // the SMTP server
				"login" => "login_name",
				"password" => "smtp_passord",
				"port" => 25, // server port
				"timeout" => 30, // server timeout
				"authType" => "login", // login X plain
				"smtpType" => "esmtp"), // smtp X esmtp

### Count of emails which are sent in one batch
"batch" => 50,


));

define("CONFIG", true, true);
?>
