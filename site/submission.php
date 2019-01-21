<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

$db = new Database();
$config = $db->config();
$data = new Data();

capture_start();
?>
<div class="submission">
</div>
<?php
capture_end($data->content);

$data->title = 'SUBMISSION';
$data->pagename = 'submission';
capture_start();
?>
<div class="submission">
    <?php if (utils_submission_photo()) { ?>
    <div class="photo"><img class="img-fluid" src="<?php echo utils_as_link(utils_submission_photo());?>"/></div>
    <?php } ?>
    <div class="logo"><img class="img-fluid" src="data/main/diversity_logo.png"/></div>
    <p><?php echo $config->submission_title();?></p>
    <p><?php echo $config->submission_text();?></p>
    <div class="bottom">
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

