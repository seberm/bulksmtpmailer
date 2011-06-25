$(document).ready(function() {
	$("img.help").tooltip({
		track:true
	});
	
	
	$("a.remove").click(function() {
		return confirm ("Do you really want to delete this item?");
	});
}); 
