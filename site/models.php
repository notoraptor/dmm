<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

$db = new Database();
$models = $db->models();
$models_lines = utils_array_to_lines($models, 4);
$model_categories = $db->list_categories();
$config = $db->config();
$data = new Data();

capture_start();
?>
	<div>
		<div class="models-menu">
			<ul>By Type
				<li>Trends</li>
				<?php foreach($model_categories->values() as $model_category) { ?>
					<li><?php echo $model_category; ?></li>
				<?php } ?>
			</ul>
		</div>
		<div class="models">
			<?php foreach ($models_lines as $line) {
				?><div class="row">
					<?php foreach ($line as $model) {
						$profile_photo = $model->get_profile_photo();
						?>
					<div class="col">
						<div class="model">
							<div class="image">
								<a href="model.php?id=<?php echo $model->id();?>">
								<?php if ($profile_photo) { ?><img class="img-fluid" alt="<?php echo $model->first_name();?>" src="<?php echo $profile_photo;?>"/><?php } ?>
								</a>
							</div>
							<div class="name"><a href="model.php?id=<?php echo $model->id();?>"><?php echo $model->first_name();?></a></div>
						</div>
					</div>
					<?php
				} ?>
				</div><?php
			} ?>
		</div>
	</div>
<?php
capture_end($data->content);

$data->title = 'DMM';
$data->pagename = 'models';
echo template($data);
?>