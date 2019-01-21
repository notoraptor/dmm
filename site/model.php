<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');
$id = utils_s_get('id');
if (!ctype_digit($id))
	utils_redirection('index.php');
$db = new Database();
$model = $db->model($id);
if(!$model) utils_redirection('index.php');
$data = new Data();
$data->title = $model->full_name();
$data->pagename = 'model';
$data->show_menu = false;
$data->meta_description[] = $model->full_name();
$data->meta_keywords[] = $model->full_name();
capture_start();
?>
<div class="model">
	<div class="logo"><a href="index.php"><img class="img-fluid" src="data/main/dmm_logo.png"/></a></div>
	<div class="details">
		<h1><?php echo $model->first_name();?></h1>
		<h2><?php echo $model->hint();?></h2>
		<div class="buttons">
			<?php if ($model->instagram_link()) { echo '<a target="_blank" class="btn btn-outline-dark" href="'.$model->instagram_link().'">INSTAGRAM</a>'; } ?>
			<?php if (utils_model_card($model->id())) { echo '<a target="_blank" class="btn btn-outline-dark" href="'.utils_as_link(utils_model_card($model->id())).'">MODEL CARD</a>'; } ?>
			<?php if ($model->video_link()) { echo '<a target="_blank" class="btn btn-outline-dark" href="'.$model->video_link().'">CLIP</a>'; } ?>
		</div>
		<div class="photos">
			<?php
			if ($model->photos()) {
				foreach ($model->photos() as $photo) {
					?>
					<img class="img-fluid" src="<?php echo $photo->getURL();?>"/>
					<?php
				}
			}
			?>
		</div>
	</div>
</div>
<?php
capture_end($data->content);
echo template($data, $model);
?>