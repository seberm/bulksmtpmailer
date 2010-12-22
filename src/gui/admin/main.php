<?php
/** HTML file with EasyAdmin design
 * @author Otto Sabart <seberm@gmail.com>
 */

$moduleName = isset($_GET['module']) ? $_GET['module'] : "AdminWelcome";
$currentDate = Date("Y");

if ($_Core->loadModule($moduleName))
	$module = new $moduleName ();
else die ("Failed to load system module.");	

echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"cs\" lang=\"cs\">
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
		<meta name=\"author\" content=\"Sabart Otto; seberm@gmail.com - www.seberm.homelinux.org\" />
		<meta name=\"generator\" content=\"Geany\" />
		<meta name=\"robots\" content=\"noindex, nofollow\" />
		<link rel=\"stylesheet\" type=\"text/css\" href=\"css/admin.css\" />
		<script type=\"text/javascript\" src=\"javascripts/jquery-1_4_4.js\"></script>
		<script type=\"text/javascript\" src=\"javascripts/admin.js\"></script>
		<title>BulkSMTPMailer - Administration | ".$module->moduleName."</title>
	</head>

<body>
<div id=\"mainContainer\">
	<div id=\"header\"></div>
	<div id=\"menu\">
		<span class=\"menuItem\"><a href=\"?module=AdminWelcome\">Home</a></span>
		<span class=\"menuItem\">---</span>
		<span class=\"menuItem\"><a href=\"?module=BulkAdmin\">Bulk administration</a></span>
		<span class=\"menuItem\"><a href=\"?module=SystemManager\">System manager</a></span>
		<span class=\"menuItem\">---</span>
		<span class=\"menuItem\"><a href=\"?module=MailsManager\">E-mails manager</a></span>
		<span class=\"menuItem\"><a href=\"?module=MessagesManager\">Messages manager</a></span>
	</div>
	<div id=\"content\">
		<h1>".$module->moduleName."</h1>
		<div id=\"moduleContent\">".$module->getContent()."</div>
	</div>
	<div id=\"footer\">Powered by <a href=\"http://www.seberm.homelinux.org\">BulkSMTPMailer</a> ".Date("Y")."</div>
</div>
</body>
</html>
";
 
?>
 
