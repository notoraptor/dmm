<?php
require_once(__DIR__.'/../../priv/videodetection.php');
function get_post($name, $default = '') {return strip_tags(utils_safe_post($name, $default));}
$db = new Database();
if(!utils_has_s_get('id')) utils_request_redirection('index.php?panel=models');
else {
$id = utils_s_get('id');
$model = $db->model($id);
if(!$model) utils_request_redirection('index.php?panel=models');
else {
if(!empty($_POST)) {
	$first_name = get_post('first_name');
	$last_name = get_post('last_name');
	$trend_rank = get_post('trend_rank', 0);
	$hint = get_post('hint', 0);
	$category = get_post('category', 0);
	$instagram_link = hex2bin(get_post('instagram_link'));
	$video_link = hex2bin(get_post('video_link'));
	$age = get_post('age', 0);
	$sex = get_post('sex', 'female');
	$height = get_post('height');
	$hair = get_post('hair');
	$eyes = get_post('eyes');
	$values = array(
		'first_name'=> $first_name,
		'last_name'=> $last_name,
		'instagram_link'=> $instagram_link,
		'video_link'=> $video_link,
		'hint'=> $hint,
		'category'=> $category,
		'trend_rank'=> $trend_rank,
		'height'=> $height,
		'hair'=> $hair,
		'eyes'=> $eyes,
		'sex'=> $sex,
		'age'=> $age
	);
	if($first_name == '') utils_message_add_error('Veuillez indiquer un prénom !');
	else if($last_name == '') utils_message_add_error('Veuillez indiquer un nom !');
	else if($instagram_link && !utils_valid_url($instagram_link))
		utils_message_add_error("Le lien instagram est invalide.");
	else if($video_link && (!utils_valid_url($video_link) || !Video::parse($video_link)))
		utils_message_add_error("Le lien vidéo est invalide.");
	else {
		$model = $db->model_update($model->id(), $values);
		if(!$model) utils_message_add_error('Erreur interne: impossible de mettre à jour les informations de ce modèle.');
		else {
		    utils_message_add_success('Le modèle '.$first_name.' '.$last_name.' a été modifié.');
			$title = 'Model card';
			$name = 'model_card';
			if (isset($_FILES[$name]) && $_FILES[$name]['name']) {
				$uploaded = utils_upload($name, DIR_DB(), utils_model_card_name($model->id()), 'pdf', array('pdf'));
				$error = $uploaded['error'];
				if($error)
					utils_message_add_error("Erreur interne: impossible d'enregistrer la model card. ".$error);
			}
		}
	}
}
$_POST = $model->to_post();

$fullName = $model->full_name();
$profilePhoto = $model->get_profile_photo();
?>
<div class="modelEdition">
<div class="table breadcumbs">
	<div class="cell main">
		<h2><a href="index.php?panel=models">Modèles</a> / <?php echo $fullName;?></h2>
		<p>
            <a href="index.php?panel=modelportfolio&id=<?php echo $model->id();?>">Portfolio</a> |
            <a style="color:red;"
               href="index.php?panel=deletemodel&id=<?php echo $model->id();?>"
               onclick="return confirm('Voulez-vous vraiment supprimer le modèle <?php echo $fullName;?> ?');">
                <strong>Supprimer ce modèle</strong>
            </a>
		</p>
	</div>
	<div class="cell photo"><?php if($profilePhoto) { ?><img src="<?php echo $profilePhoto['url'];?>"/><?php } ?></div>
</div>
<form method="post" onsubmit="careful(['video_link', 'instagram_link']);" enctype="multipart/form-data">
<fieldset>
<legend>Modifier ses infos.</legend>
<div class="table">
	<?php
	$help = '(navigateurs récents) appuyez sur la touche BAS dans le champ pour afficher des valeurs prédéfinies proposées.';
	echo utils_required_input('Prénom', 'first_name', 'text', '');
	echo utils_required_input('Nom', 'last_name', 'text', '');
	echo utils_input('Ordre de tendance', 'trend_rank', 'number');
	echo utils_input('Caractéristique', 'hint', 'text', 'list="current-hints"', $help);
	echo utils_input('Catégorie', 'category', 'text', 'list="current-categories"', $help);
	echo input_url("Lien Instagram", 'instagram_link');
	echo input_url("Lien vidéo", 'video_link');
	echo utils_input('Age', 'age', 'number', 'min="0" max="200"');
	echo utils_select('Sexe', 'sex', array('male' => 'male', 'female' => 'female', 'X' => 'X'));
	echo utils_input('Hauteur [height]', 'height', 'text', '');
	echo utils_input('Couleur des cheveux [hair]', 'hair', 'text', 'list="current-hairs"', $help);
	echo utils_input('Couleur des yeux [eyes]', 'eyes', 'text', 'list="current-eyes"', $help);
	echo utils_input('Model card (PDF)', 'model_card', 'file');
	$model_card = utils_model_card($model->id());
	if ($model_card) {
		echo '<div class="row">'.
			'<div class="cell name">[Model card] current</div>'.
			'<div class="cell value">'.
			'<a href="'.utils_as_link($model_card).'">'.utils_model_card_name($model->id()).'</a>'.
			'</div>'.
			'</div>';
    }
	echo utils_datalist('current-hairs', $db->list_hairs());
	echo utils_datalist('current-eyes', $db->list_eyes());
	echo utils_datalist('current-hints', $db->list_hints());
	echo utils_datalist('current-categories', $db->list_categories());
	?>
</div>
<p><input type="submit" value="modifier ce modèle"/></p>
</fieldset>
</form>
</div>
<?php
}
}
 ?>