<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

$db = new Database();
$config = $db->config();
$data = new Data();

$data->title = 'Submission | DMM';
$data->pagename = 'submission';
capture_start();
?>
<div class="submission">
    <div class="header d-flex">
		<?php if (utils_submission_photo()) { ?>
            <div class="photo"><img class="img-fluid" src="<?php echo utils_as_link(utils_submission_photo());?>"/></div>
		<?php } ?>
        <div class="logo align-self-center"><img class="img-fluid" src="data/main/dmm_logo_cropped.png"/></div>
    </div>
    <h2 class="py-4"><?php echo $config->submission_title();?></h2>
    <p class="text-center"><?php echo $config->submission_text();?></p>
    <div class="bottom text-center mt-5">
        <?php if (utils_submission_bottom_photo()) { ?>
        <div class="image"><img class="img-fluid" src="<?php echo utils_as_link(utils_submission_bottom_photo());?>"/></div>
		<?php } ?>
        <div class="text"><?php echo $config->submission_bottom_photo_text();?></div>
    </div>
</div>
<?php
capture_end($data->content);
echo template($data);
?>

