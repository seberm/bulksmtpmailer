<?php

include_once("classes/AdminModule.class.php");

class SystemManager extends AdminModule {
	
	function __construct () {

		$this->moduleName = "System manager";
	}

	public function getContent () {
		
		$action = isset($_GET['action']) ? $_GET['action'] : "show";

		switch ($action) {

			case "save":
				$output = $this->save();
				break;

			case "showForm":
			default:
				$output = $this->showForm();
				break;
		}

		return $output;
	}
	

	private function showForm () {
		
		global $_MySql;

		$sql = "SELECT * FROM `SystemSettings`";
		$res = $_MySql->query($sql);

		$output = "
		<form method=\"post\" action=\"?module=".get_class($this)."&amp;action=save\" enctype=\"multipart/form-data\">
		<table border=\"0\" cellspacing=\"5\">
			<tr>
				<th>Name</th>
				<th>Value</th>
			</tr>";
		
		while ($row = $res->fetch_assoc()) {
			
			$output .= "<tr>";
				$output .= "<td>".$row['Name'].":</td>";
				$output .= "<td><input type=\"text\" name=\"Items[".$row['Item']."]\" value=\"".(isset($row['Value']) ? $row['Value'] : "")."\" /></td>";
			$output .= "</tr>";
		}
		
		$output .= "</table>
		<input type=\"submit\" value=\"Save\" /><a href=\"?module=".get_class($this)."\"><input type=\"button\" value=\"Back\" /></a>
		</form>
		";

		return $output;
	}


	private function save () {
		
		global $_MySql;

		$items = isset($_POST['Items']) ? $_POST['Items'] : Array();
		
		$sql = "INSERT INTO `SystemSettings` (`Item`, `Value`) VALUES ";
		
		$values = "";
		foreach ($items as $key => $value) {
			// $key = DB.Item
			// $value = DB.Value
			
			if (!empty($values))
				$values .= ", ";
			
			$values .= "('".$key."', '".$value."')";
		}
		
		$sql .= $values." ON DUPLICATE KEY UPDATE `Value` = VALUES(`Value`);";
		$db = $_MySql->query($sql);
		
		if ($db)
			$output = "<div class=\"success\">Data saved</div>";
		else $output = "<div class=\"error\">Saving error</div>";

		$output .= $this->showForm();

		return $output;
	}

}

?>
