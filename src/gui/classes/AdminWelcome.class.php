<?php

include_once("classes/AdminModule.class.php");

class AdminWelcome extends AdminModule {
	
	function __construct() {
		$this->moduleName = "Welcome";
	}
	
	public function getContent() {
		return "Welcome in the BulkSMTPMailer administration!";
	}
} 

?>
