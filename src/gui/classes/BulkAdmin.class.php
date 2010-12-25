<?php

include_once("classes/AdminModule.class.php");


class BulkAdmin extends AdminModule {
	
	function __construct () {

		$this->moduleName = "Bulk queues administration";
	}


	public function getContent () {
		
		$action = isset($_GET['action']) ? $_GET['action'] : "showList";

		switch ($action) {
			case "showForm":
				$output = $this->showForm();
				break;

			case "remove":
				$output = $this->remove();
				break;
			
			case "start":
				$output = $this->start();
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

		$output = "
		<form method=\"post\" action=\"?module=".get_class($this)."&amp;action=save\" enctype=\"multipart/form-data\">
		<table border=\"0\" cellspacing=\"5\">
			<tr>
				<td>Name:</td>
				<td><input type=\"text\" name=\"name\" value=\"\" /></td>
			</tr>
			<tr>
				<td>Message:</td>
				<td>
					<select name=\"messageID\">";
					
					$sql = "SELECT `id`, `Subject`
							FROM `Message`;";
						
					$res = $_MySql->query($sql);
					while ($row = $res->fetch_assoc())
						$output .= "<option value=\"".$row['id']."\">".$row['Subject']."</option>";
						
					
		$output .= "</select>
				</td>
			</tr>
			<tr>
				<td>Start sending immediately:</td>
				<td>Ano<input type=\"radio\" name=\"startImmediately\" value=\"1\" checked=\"checked\" />&nbsp;&nbsp;&nbsp;Ne<input type=\"radio\" name=\"startImmediately\" value=\"0\" /></td>
			</tr>
		 ";

		$output .= "</table>
		<input type=\"submit\" value=\"Save\" /><a href=\"?module=".get_class($this)."\"><input type=\"button\" value=\"Back\" /></a>
		</form>
		";

		return $output;
	}


	private function remove () {
	
		global $_MySql;

		$contentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
		if ($contentId == 0)
			return "<div class=\"error\">Bad object ID</div>";

		$sql = "DELETE FROM `Queue` WHERE `id` = ".$contentId.";";
	
		if ($_MySql->query($sql)) {
			
			$output = "<div class=\"success\">Successfully stopped and removed</div>";
			$output .= $this->showList();
			return $output;
		} else return "<div class=\"error\">Deletion error</div>";
	}


	private function save () {
		
		global $_MySql;

		$name = isset($_POST['name']) ? Utils::escape($_POST['name']) : "";
		$messageID = isset($_POST['messageID']) ? intval($_POST['messageID']) : 0;
		$startImmediately = (isset($_POST['startImmediately'])&& $_POST['startImmediately'] == "1") ? "true" : "false";
		
		$sql = "INSERT INTO `Queue` (`Name`, `MessageID`, `isSending`)
				VALUES ('".$name."', ".$messageID.", ".$startImmediately.");";

		$db = $_MySql->query($sql);
		
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
							<th>Name</th>
							<th>Is sending</th>
							<th>Is completely sent</th>
							<th>Action</th>
						</tr>";

		$sql = "SELECT `id`, `Name`, `isSending`, `isCompleted`
				FROM `Queue`
				ORDER BY `id` DESC;";
				
		$res = $_MySql->query($sql);

		if ($res->num_rows == 0)
			$output .= "<tr><td colspan=\"4\"><div class=\"error\">No queues in database</div></td></tr>";

		while ($row = $res->fetch_assoc()) {
			
			$actions = "<a href=\"?module=".get_class($this)."&amp;action=remove&amp;id=".$row['id']."\"><img src=\"".CURRENT_ROOT."gui/images/icons/remove.png\" title=\"Stop & Remove\" alt=\"Stop & Remove\" /></a>";
			if (!$row['isCompleted'] && !$row['isSending'])
				$actions .= "<a href=\"?module=".get_class($this)."&amp;action=start&amp;id=".$row['id']."\"><img src=\"".CURRENT_ROOT."gui/images/icons/up.png\" title=\"Start sending\" alt=\"Stop & Remove\" /></a>";
				
			$output .= "<tr><td>".$row['Name']."</td><td align=\"center\">".($row['isSending'] ? "<img src=\"".CURRENT_ROOT."gui/images/icons/loader.gif\" title=\"\" alt=\"Sending\" />" : "X")."</td><td align=\"center\">".($row['isCompleted'] ? "<img src=\"".CURRENT_ROOT."gui/images/icons/checked.png\" title=\"\" alt=\"Sent\" />" : "X")."</td><td align=\"center\">".$actions."</td></tr>";
		}

		$output .= "</table>";

		return $output;
	}
	
	
	private function start() {
		
		global $_MySql;
		$output = "";
		
		$contentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
		$sql = "UPDATE `Queue`
				SET `isSending` = true
				WHERE `id` = ".$contentId.";";
		
		if ($_MySql->query($sql))
			$output .= "<div class=\"success\">Queue has been successfuly started</div>";
		else $output .= "<div class=\"error\">Error in starting</div>";
		
		$output .= $this->showList();
		
		return $output;
	}

}

?>
