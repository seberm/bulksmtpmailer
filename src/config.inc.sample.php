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
"username" => "someuser",

### Password
"password" => "somepass",

### Database name
"database" => "datbasename"

),


### MAIN SYSTEM SETTINGS
"system" => Array(


### Enable/Disable GUI
# If 'gui' is enabled, following configuration of 'bulk' array is NOT necessary. It will use a configuration stored in database (DB.SystemSettings)
"gui" => true // true/false

),


### BULK APPLICATION - It is not neccessary to set if GUI value is enabled ###
"bulk" => Array(

### Bound
"bound" => "somebound",

### Mailer
"mailer" => "phpSMTPBulk",

### Charset
"charset" => "utf-8",

### From
"from" => "somebody@some.tld",

### Message content type
"contentType" => "html", // plain X html




### SMTP MAIN CONFIGURATION
"smtp" => Array(

### Server
"server" => "smtp.server.tld",

### Port
"port" => 25, // Default: 25

### Login
"login" => "logintosmtp",

### Password
"password" => "passtosmtp",

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
// -> Running proxy with ssh: ssh -L 9876:smtp.skok.cz:25 seberm@progdan.cz -p 8765
"proxyServer" => "localhost",

### Port
"proxyPort" => 9876


),



### Count of emails which are sent in one batch
"batch" => 50,


));

define("CONFIG", true, true);
?>
