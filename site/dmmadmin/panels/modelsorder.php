<?php
$db = new Database();
$id = utils_s_get('id', false);
$action = utils_s_get('action', false);
$model_category = utils_s_get('category', '');
if ($model_category)
    $model_category = urldecode($model_category);
if (!$model_category) {
    $model_category = 'models';
}
$models = $db->models($model_category);

if($id !== false && ctype_digit($id)) {
    $model = $db->model($id);
	if($model) {
		$fullName = $model->full_name();
		if ($action == 'up' || $action == 'down') {
			$direction = $action == 'up' ? -1 : +2;
			if ($db->model_order_update($model->id(), $model->trend_rank() + $direction, $models))
			    utils_message_add_success('Ordre modifié.');
			else
			    utils_message_add_error("Erreur pendant la modification de l'ordre.");
		}
	}
};

$models = $db->models($model_category);

?><h2>Ordre d'affichage des modèles</h2>
<?php
if (!empty($models)) {
    $categories = $db->list_categories()->values();
    ?>
    <h2>Catégorie affich&eacute;e:
        <?php
        $printed = false;
        foreach ($categories as $category) {
            if ($printed) {
                echo ', ';
            } else {
                $printed = true;
            }
            if ($category == $model_category) {
                ?><span style="color:red;"><?php echo $category;?></span><?php
            } else {
				?><span><a href="index.php?panel=modelsorder&category=<?php echo urlencode($category);?>"><?php echo $category; ?></a></span><?php
            }
        }
        ?>
    </h2>
    <table>
        <thead>
        <tr>
            <th>Modèle</th>
            <th>Rank</th>
            <th>Rank up</th>
            <th>Rank down</th>
        </tr>
        </thead>
        <tbody>
		<?php foreach($models as $model) {
			$fullName = $model->first_name().' '.$model->last_name();
			$photos = $model->photos();
			$photo = null;
			if (count($photos)) {
				$photo = $photos[0];
			}
		    ?>
            <tr>
                <td>
                    <?php
                    if ($photo) {
                        ?><div><img style="max-width: 200px; max-height: 100px" src="<?php echo $photo->getURL();?>"/></div><?php
                    }
                    ?>
                    <div><?php echo $fullName;?></div>
                </td>
                <td>(<?php echo $model->trend_rank();?>)</td>
                <td><a href="index.php?panel=modelsorder&category=<?php echo $model_category;?>&action=up&id=<?php echo $model->id();?>">Monter</a></td>
                <td><a href="index.php?panel=modelsorder&category=<?php echo $model_category;?>&action=down&id=<?php echo $model->id();?>">Descendre</a></td>
            </tr>
		<?php } ?>
        </tbody>
    </table>
    <?php
}
?>