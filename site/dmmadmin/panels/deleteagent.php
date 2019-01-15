<?php
if(utils_has_s_get('id')) {
	$id = utils_s_get('id');
	if(ctype_digit($id)) {
		$db = new Database();
		$deleted = $db->agent_delete($id);
		if($deleted)
			utils_message_add_success('Agent supprimé.');
		else
			utils_message_add_error("Il n'existe aucun agent ayant l'ID $id.");
		utils_request_redirection('index.php?panel=agents');
	} else {
		utils_request_redirection('index.php');
	}
}
?>