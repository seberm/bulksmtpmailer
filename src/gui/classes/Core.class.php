<?php
/** The system core class 
 * @class Core
 * @file Core.class.php
 * @author Otto Sabart <seberm@gmail.com>
 */


final class Core {
	
	/** The conctructor of the final Core class.
	 */
	function Core () {
	
	}
	
	
	/** If is class with module loaded and if not try to load it
	 * @param string $moduleName
	 * @return bool
	 */
	public function loadModule ($moduleName) {
		$moduleName = UcFirst($moduleName);

		if (!class_exists($moduleName)) {
			
			if (file_exists("classes/".$moduleName.".class.php")) {
				
				require_once ("classes/".$moduleName.".class.php");
				return true;
			} else return false;
		} else return true;
	}
	
};

define ("CORE", true, true);

?>
