<?php
/** Main page configuration file
 * @author Otto Sabart <seberm@gmail.com>
 */


$_Config = Array(

### DATABASE SERVER ###
"mysql" => Array(

### Server
"server" => "localhost", // Examples: "localhost", "192.168.3.122", "81.35.68.121:3306", "82.35.58.119:3307"

### User
"username" => "seberm",

### Password
"password" => "mandarinka",

### Database name
"database" => "bulk"

),


### MAIN SYSTEM SETTINGS
"system" => Array(


### Enable/Disable GUI
"gui" => true // true/false

),


### BULK APPLICATION - It is not neccessary to set if GUI value is enabled ###
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




### SMTP MAIN CONFIGURATION
"smtp" => Array(

### Server
"server" => "smtp.skok.cz",

### Port
"port" => 25, // Default: 25

### Login
"login" => "seberm@science-agency.cz",

### Password
"password" => "mandarinka",

### Server timeout				
"timeout" => 30,

### Auth type
"authType" => "login", // login X plain

### SMTP Type
"smtpType" => "esmtp", // smtp X esmtp



### The proxy options

### Enable proxy?
"useProxy" => true, // false X true

### Server
"proxyServer" => "localhost",

### Port
"proxyPort" => 9876


),



### Count of emails which are sent in one batch
"batch" => 50,


));

define("CONFIG", true, true);
?>
