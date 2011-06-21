<?php

include_once("classes/AdminModule.class.php");

class MessagesManager extends AdminModule {
	
	function __construct () {

		$this->moduleName = "Messages manager";
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
			
			$sql = "SELECT * FROM `Message` WHERE `id` = ".$contentId.";";
			$res = $_MySql->query($sql);
			$row = $res->fetch_assoc();
		} else $row = Array();

		$output = "
		<form method=\"post\" action=\"?module=".get_class($this)."&amp;action=save\" enctype=\"multipart/form-data\">
		<table border=\"0\" cellspacing=\"5\">
			<tr>
				<td>Subject:</td>
				<td><input type=\"text\" name=\"subject\" value=\"".(isset($row['Subject']) ? $row['Subject'] : "")."\" /></td>
			</tr>
			<tr>
				<td>Message text:</td>
				<td><textarea name=\"text\" rows=\"10\" cols=\"60\">".(isset($row['Text']) ? $row['Text'] : "")."</textarea></td>
			</tr>
			<tr><td></td><td style=\"font-style:italic; font-size:12px;\">It's possible to use HTML, but if you send message <br />you must change the option plain to HTML</td></tr>
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

		$sql = "DELETE FROM `Message` WHERE `id` = ".$contentId.";";
		
		if ($_MySql->query($sql)) {
			
			$output = "<div class=\"success\">Successfully removed</div>";
			$output .= $this->showList();
			return $output;
		} else return "<div class=\"error\">Deletion error</div>";
	}


	private function save () {

		global $_MySql;

		$contentId = isset($_POST['contentId']) ? intval($_POST['contentId']) : 0;
		$subject = isset($_POST['subject']) ? $_POST['subject'] : "";
		$text = isset($_POST['text']) ? $_POST['text'] : "";

		$sql = "INSERT INTO `Message` (`id`, `Subject`, `Text`)
				VALUES (".$contentId.", '".$subject."', '".$text."')
				ON DUPLICATE KEY UPDATE `Subject` = '".$subject."', `Text` = '".$text."';";
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

		$output = "<span class=\"newItem\"><a href=\"?module=".get_class($this)."&amp;action=showForm\"><img src=\"".CURRENT_ROOT."gui/images/icons/add.png\" alt=\"Add\" title=\"Add\" /></a></span>";
		$output .= "<br /><br />";
		$output .= "<table class=\"listTable\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\">
						<tr>
							<th>Subject</th>
							<th>Text</th>
							<th>Action</th>
						</tr>";

		$sql = "SELECT `Subject`, `Text`, `id` FROM `Message` ORDER BY `id` ASC;";
		$res = $_MySql->query($sql);
		
		if ($res->num_rows == 0)
			$output .= "<tr><td colspan=\"3\"><div class=\"error\">No messages in database</div></td></tr>";

		while ($row = $res->fetch_assoc()) {
			$actions = "<a href=\"?module=".get_class($this)."&amp;action=showForm&amp;id=".$row['id']."\"><img src=\"".CURRENT_ROOT."gui/images/icons/edit.png\" title=\"Edit\" alt=\"Edit\" /></a>";
			$actions .= "<a class=\"remove\" href=\"?module=".get_class($this)."&amp;action=delete&amp;id=".$row['id']."\" onClick=\"return confirmation()\"><img src=\"".CURRENT_ROOT."gui/images/icons/remove.png\" title=\"Remove\" alt=\"Remove\" /></a>";
			$output .= "<tr><td>".$row['Subject']."</td><td>".Utils::cutString($row['Text'], 40)."</td><td align=\"center\">".$actions."</td></tr>";
		}

		$output .= "</table>";

		return $output;
	}

}

?>
