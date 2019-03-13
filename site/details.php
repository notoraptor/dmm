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
<div class="details pb-5 mb-5">
    <div class="titled-outer text-center">
        <div class="titled-inner">
            <h1 class="text-center"><?php echo $config->details_page_title();?></h1>
            <div class="titled-content-wrapper d-flex">
                <div class="mt-5 text-justify flex-grow-1 square-text"><?php echo $config->details_page_text();?></div>
            </div>
        </div>
    </div>
	<?php if (utils_submission_bottom_photo()) { ?>
    <div class="image"><img alt="open call details" class="img-fluid" src="<?php echo utils_as_link(utils_submission_bottom_photo());?>"/></div>
	<?php } ?>
    <h2 class="py-5"><?php echo $config->details_page_middle_title();?></h2>
</div>
<?php
capture_end($data->content);
echo template($data);
?>

