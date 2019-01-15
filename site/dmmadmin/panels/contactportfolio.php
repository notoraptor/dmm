<?php
$action = utils_s_get('action', false);
$photo_id = utils_s_get('photo_id', false);

$db = new Database();
$name = 'new_photo';
if (isset($_FILES[$name]) && $_FILES[$name]['name']) {
	$photo_id = $db->contact_photo_add();
	$photo_name = utils_contact_photo_name($photo_id);
	$previous = utils_contact_photo($photo_id);
	$uploaded = utils_upload($name, DIR_DB(), $photo_name);
	$error = $uploaded['error'];
	if(!$error) {
		utils_message_add_success('Photo ajoutée.');
		if ($previous && $previous != utils_contact_photo($photo_id))
			unlink($previous);
	} else
		utils_message_add_error('Erreur interne: impossible d\'ajouter une photo de la page de contacts. '.$error);
} else if ($photo_id) {
	if ($action == 'delete') {
		if ($db->contact_photo_delete($photo_id))
			utils_message_add_success('Photo supprimée de la page de contacts.');
	} else if ($action == 'up' || $action == 'down') {
		foreach($db->contact_photos() as $current_photo) {
			if ($current_photo->id() == $photo_id) {
				$direction = $action == 'up' ? -1 : +2;
				$db->contact_photo_update($photo_id, $current_photo->rank() + $direction);
				utils_message_add_success('Ordre modifié.');
				break;
			}
		}
	}
}
$contact_photos = $db->contact_photos();
?>
    <div class="table breadcumbs">
        <div class="cell main"><h2>Portfolio de la page de contacts</h2></div>
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
if ($contact_photos) {
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
		<?php foreach($contact_photos as $photo) { ?>
            <tr>
                <td><img style="max-width: 200px; max-height: 100px" src="<?php echo $photo->getURL();?>"/></td>
                <td>(<?php echo $photo->rank();?>)</td>
                <td><a href="index.php?panel=contactportfolio&action=up&photo_id=<?php echo $photo->id();?>">Monter</a></td>
                <td><a href="index.php?panel=contactportfolio&action=down&photo_id=<?php echo $photo->id();?>">Descendre</a></td>
                <td>
                    <a style="color:red; font-weight: bold;"
                       onclick="return confirm('Voulez-vous vraiment supprimer la photo No. <?php echo $photo->rank();?> ?');"
                       href="index.php?panel=contactportfolio&action=delete&photo_id=<?php echo $photo->id();?>">Supprimer</a></td>
            </tr>
		<?php } ?>
        </tbody>
    </table>
	<?php
}

?>