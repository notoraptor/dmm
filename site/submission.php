<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/Data.php');
require_once('_template.php');

$db = new Database();
$config = $db->config();
$data = new Data($db);

$data->title = 'Submission | DMM';
$data->pagename = 'submission';
capture_start();
?>
<div class="submission pt-5">
    <div class="header d-flex">
		<?php if (utils_submission_photo()) { ?>
            <div class="photo"><img class="img-fluid" src="<?php echo utils_as_link(utils_submission_photo());?>"/></div>
		<?php } ?>
        <div class="logo align-self-center text-md-right">
            <div class="text-open-call">Open Call</div>
            <div class="text-details"><a href="details.php">details</a></div>
        </div>
    </div>
    <div class="row">
        <!-- texte -->
        <div class="col-md"></div>
        <!-- formulaire -->
        <div class="col-md"></div>
    </div>
</div>
<?php
capture_end($data->content);
echo template($data);
?>

