<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

define('LINE_SIZE', 4);

$db = new Database();
$model_categories = $db->list_categories();
$trend = utils_s_get('trend', '');
if ($trend) {
	$trend = urldecode($trend);
	if (!$model_categories->contains($trend))
	    $trend = '';
}
$all_models = $db->models();
$models = $all_models;
if ($trend) {
    $models = array();
    foreach ($all_models as $model) if ($model->category() == $trend)
        $models[] = $model;
}
$models_lines = utils_array_to_lines($models, LINE_SIZE);
$config = $db->config();
$data = new Data();

capture_start();
?>
	<div>
		<div class="models-menu mb-5 float-md-left">
			<div class="menu-title"><?php if ($trend) { ?><span class="current-trend"><?php echo $trend; ?></span><?php } else { ?>By Type<?php } ?></div>
            <ul>
                <?php if ($trend) { ?><li><a href="models.php">All trends</a></li><?php } ?>
				<?php foreach($model_categories->values() as $model_category) if ($model_category != $trend) { ?>
					<li><a href="models.php?trend=<?php echo urlencode($model_category);?>"><?php echo $model_category; ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="models">
			<?php foreach ($models_lines as $line) {
				?><div class="row">
					<?php foreach ($line as $model) {
						$profile_photo = $model->get_profile_photo();
						?>
					<div class="col-md-3 align-self-center">
						<div class="model">
                            <?php
                            if ($profile_photo) {
                                ?>
                                <div class="image">
                                    <a href="model.php?id=<?php echo $model->id();?>">
                                        <img class="img-fluid" src="<?php echo $profile_photo;?>"/>
                                    </a>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="image placeholder" style="background-color: rgb(80, 80, 80);">
                                    <a href="model.php?id=<?php echo $model->id();?>"></a>
                                </div>
                                <?php
                            }
                            ?>
							<div class="name"><a href="model.php?id=<?php echo $model->id();?>"><?php echo $model->first_name();?></a></div>
						</div>
					</div>
					<?php
				}
				if (count($line) < LINE_SIZE) {
				    $nb_remining_cols = LINE_SIZE - count($line);
				    for ($i = 0; $i < $nb_remining_cols; ++$i) { ?>
                        <div class="col-md-3"></div>
                    <?php }
                }
				?>
				</div><?php
			} ?>
		</div>
	</div>
<?php
capture_end($data->content);

$data->title = 'Models | DMM';
$data->pagename = 'models';
echo template($data);
?>