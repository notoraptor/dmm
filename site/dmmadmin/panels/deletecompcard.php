<?php
$redirection = 'index.php';
if(utils_has_s_get('id')) {
	$id = utils_s_get('id');
	if(ctype_digit($id)) {
		$model_card = utils_model_card($id);
		if($model_card) {
			$deleted = @unlink($model_card);
			if($deleted)
				utils_message_add_success('COMPCARD supprimé.');
			else
				utils_message_add_error("Aucun COMPCARD à supprimer.");
		}
		$redirection = 'index.php?panel=model&id='.$id;
	} else {
		$redirection = 'index.php?panel=models';
	}
}
utils_request_redirection($redirection);
?>

