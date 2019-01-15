<?php
$id = utils_s_get('id', false);
$action = utils_s_get('action', false);
$photo_id = utils_s_get('photo_id', false);
if($id !== false && ctype_digit($id)) {
	$db = new Database();
	$model = $db->model($id);
	if($model) {
	    $fullName = $model->full_name();
	    $name = 'new_photo';
		if (isset($_FILES[$name]) && $_FILES[$name]['name']) {
			$photo_id = $db->model_photo_add($model->id());
			$photo_name = utils_model_photo_name($model->id(), $photo_id);
			$previous = utils_model_photo($model->id(), $photo_id);
			$uploaded = utils_upload($name, DIR_DB(), $photo_name);
			$error = $uploaded['error'];
			if(!$error) {
				utils_message_add_success('Photo ajoutée.');
				if ($previous && $previous != utils_model_photo($model->id(), $photo_id))
					unlink($previous);
			} else
				utils_message_add_error('Erreur interne: impossible d\'ajouter une photo. '.$error);
		} else if ($photo_id) {
			if ($action == 'delete') {
                if ($db->model_photo_delete($model->id(), $photo_id))
                    utils_message_add_success('Photo supprimée.');
			} else if ($action == 'up' || $action == 'down') {
			    foreach($model->photos() as $current_photo) {
			        if ($current_photo->id() == $photo_id) {
			            $direction = $action == 'up' ? -1 : +2;
						$db->model_photo_update($model->id(), $photo_id, $current_photo->rank() + $direction);
						utils_message_add_success('Ordre modifié.');
			            break;
                    }
                }
			}
		}
		$model = $db->model($id);
		$model_photos = $model->photos();
	    ?>
        <div class="table breadcumbs">
            <div class="cell main">
                <h2>
                    <a href="index.php?panel=models">Modèles</a> /
                    <a href="index.php?panel=model&id=<?php echo $id;?>"><?php echo $fullName;?></a> /
                    Portfolio
                </h2>
            </div>
        </div>
        <div class="configuration">
            <form method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend>Ajouter une photo</legend>
                    <div class="table">
						<?php
						echo utils_input('Nouvelle photo', 'new_photo', 'file');
						?>
                    </div>
                    <input type="submit" value="ajouter la photo"/>
                </fieldset>
            </form>
        </div>
        <?php
        if ($model_photos) {
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Rank</th>
                        <th>Rank up</th>
                        <th>Rank down</th>
                        <th>delete</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($model_photos as $photo) { ?>
                    <tr>
                        <td><img style="max-width: 200px; max-height: 100px" src="<?php echo $photo->getURL();?>"/></td>
                        <td>(<?php echo $photo->rank();?>)</td>
                        <td><a href="index.php?panel=modelportfolio&action=up&id=<?php echo $model->id();?>&photo_id=<?php echo $photo->id();?>">Monter</a></td>
                        <td><a href="index.php?panel=modelportfolio&action=down&id=<?php echo $model->id();?>&photo_id=<?php echo $photo->id();?>">Descendre</a></td>
                        <td>
                            <a style="color:red; font-weight: bold;"
                               onclick="return confirm('Voulez-vous vraiment supprimer la photo No. <?php echo $photo->rank();?> ?');"
                               href="index.php?panel=modelportfolio&action=delete&id=<?php echo $model->id();?>&photo_id=<?php echo $photo->id();?>">Supprimer</a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
        }
	}
} else {
	utils_message_add_error("Impossible d'afficher le portfolio du modèle ayant pour ID $id. Modèle inexistant, ou erreur interne.");
}
?>