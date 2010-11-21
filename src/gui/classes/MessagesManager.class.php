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
		global $_Linguist;
		global $_MySql;

		$contentId = isset($_GET['id']) ? $_GET['id'] : 0;

		if (is_numeric($contentId) && ($contentId != 0)) {
			$sql = "SELECT * FROM `partner` WHERE `id` = ".$contentId.";";
			$res = $_MySql->query($sql);
			$row = $res->fetch_assoc();
		} else $row = Array();

		$output = "
		<form method=\"post\" action=\"?module=".get_class($this)."&amp;action=save\" enctype=\"multipart/form-data\">
		<table border=\"0\" cellspacing=\"5\">
			<tr>
				<td>".$_Linguist->translate("title").":</td>
				<td><input type=\"text\" name=\"title\" value=\"".(isset($row['title']) ? $row['title'] : "")."\" /></td>
			</tr>
			<tr>
				<td>".$_Linguist->translate("link").":</td>
				<td>http://<input type=\"text\" name=\"url\" value=\"".(isset($row['url']) ? $row['url'] : "")."\" /></td>
			</tr>
			<tr>
				<td>".$_Linguist->translate("file").":</td>
				<td><input type=\"file\" name=\"imageFilename\" /></td>
			</tr>
		 ";

		$output .= "</table>
		<input type=\"submit\" value=\"".$_Linguist->translate("save")."\" /><a href=\"?module=".get_class($this)."\"><input type=\"button\" value=\"".$_Linguist->translate("goBack")."\" /></a>
		<input type=\"hidden\" name=\"contentId\" value=\"".$contentId."\" />
		</form>
		";

		return $output;
	}


	private function delete () {
		global $_Linguist;
		global $_MySql;

		if (isset($_GET['id']))
			$contentId = intval($_GET['id']);
		else return "<div class=\"error\">".$_Linguist->translate("badObjectId")."</div>";

		$sql = "DELETE FROM `partner` WHERE `id` = ".$contentId.";";
		if ($_MySql->query($sql)) {
			$output = "<div class=\"success\">".$_Linguist->translate("successfullyDeleted")."</div>";
			$output .= $this->showList();
			return $output;
		} else return "<div class=\"error\">".$_Linguist->translate("deletionError")."</div>";
	}


	private function save () {
		global $_Linguist;
		global $_MySql;

		$contentId = isset($_POST['contentId']) ? intval($_POST['contentId']) : 0;
		$title = isset($_POST['title']) ? $_POST['title'] : "";
		$imageFilename = isset($_FILES['imageFilename']) ? $_FILES['imageFilename']['name'] : "";
		$url = isset($_POST['url']) ? $_POST['url'] : "";

		$sql = "INSERT INTO `partner` (`id`, `title`, `imageFilename`, `url`)
				VALUES (".$contentId.", '".$title."', '".$imageFilename."', '".$url."')
				ON DUPLICATE KEY UPDATE `title` = '".$title."', `imageFilename` = '".$imageFilename."', `url` = '".$url."';";
		$db = $_MySql->query($sql);

		if ($contentId == 0)
			$contentId = $_MySql->insert_id;

		if (!empty($imageFilename))
			$uploadImageFilename = move_uploaded_file($_FILES['imageFilename']['tmp_name'], CURRENT_ROOT."images/partners/".$imageFilename);
		else $uploadImageFilename = true;

		if ($db && $uploadImageFilename)
			$output = "<div class=\"success\">".$_Linguist->translate("dataSaved")."</div>";
		else $output = "<div class=\"error\">".$_Linguist->translate("savingError")."</div>";

		$output .= $this->showList();

		return $output;
	}


	private function showList () {
		global $_Linguist;
		global $_MySql;

		$output = "<span class=\"newItem\"><a href=\"?module=".get_class($this)."&amp;action=showForm\"><img src=\"".CURRENT_ROOT."images/page/tools/add.png\" alt=\"Add\" title=\"".$_Linguist->translate("add")."\" /></a></span>";
		$output .= "<br /><br /><table class=\"listTable\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"400\">
						<tr>
							<th>".$_Linguist->translate("title")."</th>
							<th>".$_Linguist->translate("action")."</th>
						</tr>";

		$sql = "SELECT `url`, `title`, `id` FROM `partner` ORDER BY `id` ASC;";
		$res = $_MySql->query($sql);

		while ($row = $res->fetch_assoc()) {
			$actions = "<a href=\"?module=".get_class($this)."&amp;action=showForm&amp;id=".$row['id']."\"><img src=\"".CURRENT_ROOT."images/page/tools/edit.png\" title=\"".$_Linguist->translate("edit")."\" alt=\"Edit\" /></a>";
			$actions .= "<a href=\"?module=".get_class($this)."&amp;action=delete&amp;id=".$row['id']."\" onClick=\"return confirmation()\"><img src=\"".CURRENT_ROOT."images/page/tools/remove.png\" title=\"".$_Linguist->translate("remove")."\" alt=\"Remove\" /></a>";
			$output .= "<tr><td>".$row['title']."</td><td align=\"center\">".$actions."</td></tr>";
		}

		$output .= "</table>";

		return $output;
	}

}

?>
