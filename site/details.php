<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

$db = new Database();
$config = $db->config();
$data = new Data($db);

$data->title = 'Open Call Details | DMM';
$data->pagename = 'details';
capture_start();
?>
<div class="details py-5 mb-5">
    <h1 class="text-center mt-5"><?php echo $config->submission_bottom_photo_text();?></h1>
    <p class="main-text"><?php echo $config->submission_text();?></p>
	<?php if (utils_submission_bottom_photo()) { ?>
    <div class="image"><img alt="open call details" class="img-fluid" src="<?php echo utils_as_link(utils_submission_bottom_photo());?>"/></div>
	<?php } ?>
    <h2 class="py-5 text-center"><?php echo $config->submission_title();?></h2>
</div>
<?php
capture_end($data->content);
echo template($data);
?>

