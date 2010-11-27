<?php

include_once("classes/AdminModule.class.php");

class MailsManager extends AdminModule {
	
	function __construct () {

		$this->moduleName = "Emails manager";
	}

	public function getContent () {
		
		$action = isset($_GET['action']) ? $_GET['action'] : "showList";

		switch ($action) {
			case "showForm":
				$output = $this->showForm();
				break;

			case "delete":
				$output = $this->delete();
				break;

			case "save":
				$output = $this->save();
				break;

			case "showList":
			default:
				$output = $this->showList();
				break;
		}

		return $output;
	}


	private function showForm () {
		
		global $_MySql;

		$contentId = isset($_GET['id']) ? $_GET['id'] : 0;

		if (is_numeric($contentId) && ($contentId != 0)) {
			$sql = "SELECT * FROM `Mail` WHERE `id` = ".$contentId.";";
			$res = $_MySql->query($sql);
			$row = $res->fetch_assoc();
		} else $row = Array();

		$output = "
		<form method=\"post\" action=\"?module=".get_class($this)."&amp;action=save\" enctype=\"multipart/form-data\">
		<table border=\"0\" cellspacing=\"5\">
			<tr>
				<td>Name:</td>
				<td><input type=\"text\" name=\"name\" value=\"".(isset($row['Name']) ? $row['Name'] : "")."\" /></td>
			</tr>
			<tr>
				<td>E-mail:</td>
				<td><input type=\"text\" name=\"email\" value=\"".(isset($row['Email']) ? $row['Email'] : "@")."\" /></td>
			</tr>
		 ";

		$output .= "</table>
		<input type=\"submit\" value=\"Save\" /><a href=\"?module=".get_class($this)."\"><input type=\"button\" value=\"Back\" /></a>
		<input type=\"hidden\" name=\"contentId\" value=\"".$contentId."\" />
		</form>
		";

		return $output;
	}


	private function delete () {
		
		global $_MySql;

		if (isset($_GET['id']))
			$contentId = intval($_GET['id']);
		else return "<div class=\"error\">Bad object ID</div>";

		$sql = "DELETE FROM `Mail` WHERE `id` = ".$contentId.";";
		
		if ($_MySql->query($sql)) {
			
			$output = "<div class=\"success\">Successfully removed</div>";
			$output .= $this->showList();
			return $output;
		} else return "<div class=\"error\">Deletion error</div>";
	}


	private function save () {
		
		global $_MySql;

		$contentId = isset($_POST['contentId']) ? intval($_POST['contentId']) : 0;
		$name = isset($_POST['name']) ? $_POST['name'] : "";
		$email = isset($_POST['email']) ? $_POST['email'] : "";
		
		if (!Utils::isEmail($email)) {
			
			$output = "<div class=\"error\">Bad e-mail format</div>";
			$output .= $this->showForm();
			
			return $output;
		}

		$sql = "INSERT INTO `Mail` (`id`, `Name`, `Email`)
				VALUES (".$contentId.", '".$name."', '".$email."')
				ON DUPLICATE KEY UPDATE `Name` = '".$name."', `Email` = '".$email."';";
		$db = $_MySql->query($sql);

		if ($contentId == 0)
			$contentId = $_MySql->insert_id;

		
		if ($db)
			$output = "<div class=\"success\">Data saved</div>";
		else $output = "<div class=\"error\">Saving error</div>";

		$output .= $this->showList();

		return $output;
	}


	private function showList () {
		
		global $_MySql;

		$output = "<span class=\"newItem\"><a href=\"?module=".get_class($this)."&amp;action=showForm\"><img src=\"".CURRENT_ROOT."gui/images/tools/add.png\" alt=\"Add\" title=\"Add\" /></a></span>";
		$output .= "<br /><br />";
		$output .= "<table class=\"listTable\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\">
						<tr>
							<th>Name</th>
							<th>E-mail</th>
							<th>Action</th>
						</tr>";

		$sql = "SELECT `id`, `Name`, `Email` FROM `Mail` ORDER BY `id` ASC;";
		$res = $_MySql->query($sql);

		while ($row = $res->fetch_assoc()) {
			$actions = "<a href=\"?module=".get_class($this)."&amp;action=showForm&amp;id=".$row['id']."\"><img src=\"".CURRENT_ROOT."gui/images/tools/edit.png\" title=\"Edit\" alt=\"Edit\" /></a>";
			$actions .= "<a href=\"?module=".get_class($this)."&amp;action=delete&amp;id=".$row['id']."\"><img src=\"".CURRENT_ROOT."gui/images/tools/remove.png\" title=\"Remove\" alt=\"Remove\" class=\"remove\" /></a>";
			$output .= "<tr><td>".$row['Name']."</td><td>".$row['Email']."</td><td align=\"center\">".$actions."</td></tr>";
		}

		$output .= "</table>";

		return $output;
	}

}

?>
