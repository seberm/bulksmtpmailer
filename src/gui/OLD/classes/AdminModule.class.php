<?php

abstract class AdminModule 
{
	public $moduleName;
		
	abstract function __construct();
	
	abstract public function getContent();

}
