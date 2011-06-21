<?php
/**
 * @author Otto Sabart <seberm@gmail.com>
 */

/** Main system error class
 */
class Error {
	/*** Artibutes ***/
	
	/** Array of the errors
	 * @var array $errors Array with system errors
	 */
	public $errors = Array();
	
	/** Add the error
	 * @param string Error text
	 */
	public function addError ($errorText) {
		
		$this->errors[] = $errorText;
	}
	
	/** Print all system errors
	 * @return string All errors with HTML code
	 */
	public function printErrors () {
		
		foreach ($this->errors as $value)
			$output .= "<div class=\"error\">".$value."</div>";
		
		return $output;
	}
	
	/** Function resets errors
	 * @return bool
	 */
	public function reset () {
		
		if (!empty($this->errors)) {
			
			//unset ($this->errors);
			$this->errors = Array ();
		}
		
		return true;
	}
};

define ("ERROR", true, true);

?>
